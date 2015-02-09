<?php
/**
 * Cinnebar.
 *
 * My lightweight no-framework framework written in PHP.
 *
 * @package Cinnebar
 * @author $Author$
 * @version $Id$
 */

/**
 * Manages newsletters.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Newsletter extends Cinnebar_Model
{
    /**
     * Sends the newsletter to all people on the list.
     *
     * @uses send_workhorse()
     *
     * @throws Exception if the newsletter was not confirm(ed) for sending
     *
     * @param Cinnebar_Controller $controller
     * @return bool
     */
    public function send(Cinnebar_Controller $controller)
    {
        if ( ! $this->bean->confirm) {
            $this->addError(__('newsletter_warn_confirm_false'), 'confirm');
            throw new Exception(__('newsletter_warn_confirm_false'));
        }
        $queues = $this->make_queue($this->bean->getOptins());
        $this->bean->queued = time();
        return $this->send_workhorse($controller, $queues);
    }
    
    /**
     * Sends the newsletter to the one test person of this newsletter.
     *
     * @uses send_workhorse()
     *
     * @param Cinnebar_Controller $controller
     * @return bool
     */
    public function test(Cinnebar_Controller $controller)
    {
        $queues = $this->make_queue(array($this->bean->optin));
        return $this->send_workhorse($controller, $queues);
    }
    
    /**
     * Returns an array of optin beans that share the same campaign as this newsletter.
     *
     * @return array
     */
    public function getOptins()
    {
        $optins = array();
        foreach ($this->bean->sharedCampaign as $id => $campaign) {
            $optins = array_merge($campaign->getOptins(), $optins);
        }
        return $optins;
    }
    
    /**
     * Returns wether the newsletter has a queue or not.
     *
     * @return bool
     */
    public function isQueued()
    {
        return $this->bean->queued;
    }
    
    /**
     * Returns the issue bean.
     *
     * @return RedBean_OODBBean $issue
     */
    public function issue()
    {
        if ( ! $this->bean->issue) $this->bean->issue = R::dispense('issue');
        return $this->bean->issue;
    }
    
    /**
     * Returns an array of article beans.
     *
     * @return array
     */
    public function getArticles()
    {
        return R::find('article', ' newsletter_id = ? ORDER BY sequence, name', array($this->bean->getId()));
    }
    
    /**
     * Returns an array of newsletter beans.
     *
     * @return array
     */
    public function getArchived()
    {
        $sql = <<<SQL
            SELECT
                newsletter.id AS id
            FROM
                newsletter
            LEFT JOIN
                issue ON newsletter.issue_id = issue.id
            WHERE
                queued > 0
            ORDER BY
                issue.y, issue.m
SQL;
        $assoc = R::$adapter->getAssoc($sql);
		//R::debug(false);
        return R::batch('newsletter', array_keys($assoc));
    }
    
    /**
     * Returns an array of queue beans.
     *
     * @return array
     */
    public function getQueues()
    {
        return R::find('queue', ' newsletter_id = ? ORDER BY id', array($this->bean->getId()));
    }

    /**
     * Generate queue beans for each optin bean of this newsletter and returns an array with them.
     *
     * A queue bean is used to keep track of success or errors when transfering
     * to the mail server and to track if a newsletter mail was opened by the receiver.
     *
     * @param array $optins is the array of all optin beans to receive this newsletter
     * @return array
     */
    protected function make_queue(array $optins)
    {
        $result = array();
		foreach ($optins as $id => $optin) {
		    $queue = R::dispense('queue');
		    try {
		        $queue->newsletter = $this->bean;
		        $queue->email = $optin->email;
		        R::store($queue);
		        $result[] = $queue;
		    } catch (Exception $e) {
		        error_log($e);
                // could not store the queue
		    }
		}
        return $result;
    }
    
    /**
     * Really sends out the newsletter to given email addresses.
     *
     * @uses PHPMailer
     *
     * @param Cinnebar_Controller $controller
     * @param array $queues
     * @return void
     */
    protected function send_workhorse(Cinnebar_Controller $controller, array $queues)
    {   
        require_once BASEDIR.'/vendors/PHPMailer_5.2.4/class.phpmailer.php';
        
		$mail = new PHPMailer();
		$mail->CharSet = 'UTF-8';
		$mail->Subject = $this->bean->name;
		
		$mail->From = $this->bean->listmanageremail;
		$mail->FromName = $this->bean->listmanagername;
  		$mail->AddReplyTo($this->bean->listmanageremail, $this->bean->listmanagername);
  		
		// prepate PHPMailer to use a transporter
		if ($this->bean->service == 'smtp')
		{
			$mail->IsSMTP();
			//$mail->SMTPDebug = 2;
			$mail->SMTPAuth = true;
			$mail->SMTPKeepAlive = true;
			$mail->Host = $this->bean->smtp->host;
			$mail->Port = $this->bean->smtp->port;
			$mail->Username = $this->bean->smtp->user;
			$mail->Password = $this->bean->smtp->pw;
		}
		$result = true;
		foreach ($queues as $id => $queue) {
		    // i know, this should get out of the loop, just have presure all over time
            $body_html = $controller->makeView(sprintf('newsletter/templates/%s/html', $controller->router()->language()));
    		$body_text = $controller->makeView(sprintf('newsletter/templates/%s/text', $controller->router()->language()));
    		
    		$body_html->record = $body_text->record = $this->bean;
    		/*$body_html->optin = $body_text->optin = $optin;*/
    		$body_html->articles = $body_text->articles = $this->bean->getArticles();
    		$body_html->queue = $body_text->queue = $queue;

    		$mail->MsgHTML($body_html->render());
    		$mail->AltBody = $body_text->render();
		    
		    $mail->ClearAddresses();
		    $mail->AddAddress($queue->email);
            $result = $mail->Send();
            if ($result) {
                $queue->sent = time();
            } else {
                $queue->error = true;
            }
            try {
                R::store($queue);
            } catch (Exception $e) {
                // Failed to store the queue bean
            }
		}
		return $result;
    }

    /**
     * Returns SQL for filtering these beans.
     *
     * @uses R
     * @param string $where_clause
     * @param string $order_clause
     * @param int $offset
     * @param int $limit
     * @return string $SQL
     */
    public function sqlForFilters($where_clause = '1', $order_clause = 'id', $offset = 0, $limit = 1)
    {
		$sql = <<<SQL
		SELECT
			DISTINCT(newsletter.id) as id  

		FROM
			newsletter

		WHERE {$where_clause}

		ORDER BY {$order_clause}

		LIMIT {$offset}, {$limit}
SQL;
        return $sql;
    }

	/**
	 * Returns an array with possible attributes for order clauses and such.
	 *
	 * @param string (optional) $layout defaults to table and can be of any value
	 * @return array
	 */
	public function attributes($layout = 'table')
	{
	    switch ($layout) {
	        default:
        		$ret = array(
        			array(
        				'attribute' => 'name',
        				'orderclause' => 'name',
        				'class' => 'text'
        			)
        		);
        }
        return $ret;
	}
	
	/**
	 * Returns a customized menu.
	 *
	 * Adds a menu item which allows to send/queue a newsletter. It will only be added if there
	 * is a current newsletter bean.
	 *
	 * @param string $action
	 * @param Cinnebar_View $view
 	 * @param Cinnebar_Menu (optional) $menu
 	 * @return Cinnebar_Menu
 	 */
 	public function makeMenu($action, Cinnebar_View $view, Cinnebar_Menu $menu = null)
	{
        $menu = parent::makeMenu($action, $view, $menu);
        if ( $this->bean->getId()) {
            
            $menu->add(__('newsletter_test'), $view->url(sprintf('/newsletter/test/%d/%d/%d/%s/%d/%d', $this->bean->getId(), $view->page, $view->limit, $view->layout, $view->order, $view->dir)), 'scaffold-test');
            
            $menu->add(__('newsletter_send'), $view->url(sprintf('/newsletter/send/%d/%d/%d/%s/%d/%d', $this->bean->getId(), $view->page, $view->limit, $view->layout, $view->order, $view->dir)), 'scaffold-send', $s_menu);
        }
        return $menu;
	}
	
	/**
	 * Returns an array with possible layout for list view (index).
	 *
	 * @return array
	 */
	public function layouts()
	{
        return array('table');
	}
	
	/**
	 * Returns a optin (testemail) bean.
	 *
	 * @return RedBean_OODBBean
	 */
	public function optin()
	{
        if ( ! $this->bean->optin) $this->bean->optin = R::dispense('optin');
        return $this->bean->optin;
	}

    /**
     * Returns keywords from this bean for tagging.
     *
     * @var array
     */
    public function keywords()
    {
        return array(
            $this->bean->name
        );
    }
    
    /**
     * Searches for given searchterm within bean and returns the result-set as an multi-dim array
     * after the given layout.
     *
     * @param string $term contains the searchterm as given by jQuery.autocomplete
     * @param string (optional) $layout defaults to "default"
     * @return array
     */
    public function clairvoyant($term, $layout = 'default')
    {   
        switch ($layout) {
            default:
                $sql = <<<SQL

                SELECT
                    id AS id,
                    name AS label

                FROM
                    newsletter

                WHERE
                    name like ?

                ORDER BY
                    name

SQL;
        }
        return $res = R::getAll($sql, array($term.'%'));
    }
    
    /**
     * update.
     *
     * When attribute testemail is not empty a on-the-fly validator is added to asure that
     * it is a valide email address.
     *
     */
    public function update()
    {
        if ($this->bean->service == 'local') {
            $this->bean->smtp = null;
        }
        if ( ! $this->bean->optin_id) $this->bean->optin_id = null;
        parent::update();
    }

    /**
     * Setup validators and set auto info to true.
     *
     * @uses $config to set listmanager email and name
     */
    public function dispense()
    {
        global $config;
        
        //$this->bean->setMeta('buildcommand.unique', array(array('name')));
        //$this->setAutoTag(true);
        $this->setAutoInfo(true);
        $this->addValidator('name', 'hasvalue');
        $this->addValidator('name', 'isunique', array('bean' => $this->bean, 'attribute' => 'name'));
        $this->addValidator('listmanageremail', 'isemail');
        $this->addValidator('listmanagername', 'hasvalue');
        $this->bean->service = 'smtp';
        
        if ( ! $this->bean->getId()) {
            $this->optin = null; // the test email address
            if ( isset($config['listmanager']['email'])) {
                $this->bean->listmanageremail = $config['listmanager']['email'];
            }
            if ( isset($config['listmanager']['name'])) {
                $this->bean->listmanagername = $config['listmanager']['name'];
            }
        }
    }
}
