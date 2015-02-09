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
 * Manages optins.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Optin extends Cinnebar_Model
{
    /**
     * @var int
     */
    public $seconds_until_dropout = 1209600;
    
    /**
     * Prepare and send the opt-in email for this optin bean.
     *
     * If the double opt-in activation email was not already sent to this optin bean, we use
     * PHPMailer to prepare and send the activation mail.
     *
     * @uses $config
     * @uses PHPMailer
     * @todo Get rid of global stuff and dont asume smtp configuration is set properly
     *
     * @param Cinnebar_Controller $controller
     * @return bool
     */
    public function sendMailWithActivationLink(Cinnebar_Controller $controller)
    {
        global $config;
        require_once BASEDIR.'/vendors/PHPMailer_5.2.4/class.phpmailer.php';
        
		$mail = new PHPMailer();
		$mail->CharSet = 'UTF-8';
		$mail->Subject = __('optin_activation_mail_subject');
		
		$mail->From = $config['listmanager']['email'];
		$mail->FromName = $config['listmanager']['name'];
  		$mail->AddReplyTo($config['listmanager']['email'], $config['listmanager']['name']);
  		
		// prepate PHPMailer to use a transporter
		if (isset($config['smtp']['active']) && $config['smtp']['active'])
		{
			$mail->IsSMTP();
			//$mail->SMTPDebug = 2;
			$mail->SMTPAuth = true;
			$mail->Host = $config['smtp']['host'];
			$mail->Port = $config['smtp']['port'];
			$mail->Username = $config['smtp']['user'];
			$mail->Password = $config['smtp']['pw'];
		}
		$body_html = $controller->makeView(sprintf('newsletter/dbloptin/%s/html', $controller->router()->language()));
		$body_text = $controller->makeView(sprintf('newsletter/dbloptin/%s/text', $controller->router()->language()));
		$body_html->record = $body_text->record = $this->bean;
		
		$mail->MsgHTML($body_html->render());
		$mail->AltBody = $body_text->render();
		$mail->AddAddress($this->bean->email);
		
        return $mail->Send();
    }
    
    /**
     * Import data from csv array using a import map(per).
     *
     * @param RedBean_OODBBean $import is the import bean
     * @param array $data is an array of csv records
     * @param array $mappers is an array of map beans
     * @return void
     */
    public function csvImport(RedBean_OODBBean $import, array $data, array $mappers)
    {
        $this->bean->enabled = true;
        parent::csvImport($import, $data, $mappers);
        $this->bean->sharedCampaign = $import->sharedCampaign;
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
			DISTINCT(optin.id) as id  

		FROM
			optin

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
        		return array(
        			array(
        				'attribute' => 'email',
        				'orderclause' => 'email',
        				'class' => 'text',
        				'filter' => array(
        				    'tag' => 'text'
        				)
        			),
        			array(
        				'attribute' => 'enabled',
        				'orderclause' => 'enabled',
        				'class' => 'bool',
        				'viewhelper' => 'bool',
        				'filter' => array(
        				    'tag' => 'text'
        				)
        			),
                    array(
        				'attribute' => 'organization',
        				'orderclause' => 'organization',
        				'class' => 'text',
        				'filter' => array(
        				    'tag' => 'text'
        				)
        			),
                    array(
        				'attribute' => 'lastname',
        				'orderclause' => 'lastname',
        				'class' => 'text',
        				'filter' => array(
        				    'tag' => 'text'
        				)
        			),
                    array(
        				'attribute' => 'firstname',
        				'orderclause' => 'firstname',
        				'class' => 'text',
        				'filter' => array(
        				    'tag' => 'text'
        				)
        			),
                    array(
        				'attribute' => 'attention',
        				'orderclause' => 'attention',
        				'class' => 'text',
        				'filter' => array(
        				    'tag' => 'text'
        				)
        			)
        		);
        }
        return $ret;
	}
	
	/**
	 * Returns a customized menu.
	 *
	 * @param string $action
	 * @param Cinnebar_View $view
 	 * @param Cinnebar_Menu (optional) $menu
 	 * @return Cinnebar_Menu
 	 */
 	public function makeMenu($action, Cinnebar_View $view, Cinnebar_Menu $menu = null)
	{
        $menu = parent::makeMenu($action, $view, $menu);
        $menu->add(__('plugin_import'), $view->url('/optin/import'), 'scaffold-import');
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
     * Returns keywords from this bean for tagging.
     *
     * @var array
     */
    public function keywords()
    {
        return array(
            $this->bean->email
        );
    }
    
    /**
     * update.
     *
     * The given email address will be hashed. The email-hash will later on be used by the optout
     * or other methods. If the optin bean was entered through the backend it can be immediately
     * be enabled.
     *
     * @uses $this->bean->dbloptin to check wether the dbl-opt-in mail was already sent or not
     */
    public function update()
    {
        $this->bean->hash = md5($this->bean->email);
        //if (isset($this->bean->invalid) && $this->bean->invalid) $this->bean->invalid = false;
        if ($this->bean->enabled) {
            $this->bean->expires = null;
            $this->bean->dbloptin = true;
        }
        if ( ! $this->bean->getId() && ! $this->bean->enabled) {
            $this->bean->expires = time() + $this->seconds_until_dropout;
            $this->bean->dbloptin = false;
        }
        parent::update();
    }

    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        //$this->bean->setMeta('buildcommand.unique', array(array('email')));
        //$this->setAutoTag(true);
        $this->setAutoInfo(true);
        $this->addValidator('email', 'isemail');
        $this->addValidator('email', 'isunique', array('bean' => $this->bean, 'attribute' => 'email'));
    }
}
