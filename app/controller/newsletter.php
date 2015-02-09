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
 * Manages CURD on newsletter beans.
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_Newsletter extends Controller_Scaffold
{
    /**
     * Path to our tracking pixel.
     */
    const PATH_TO_SPYPIXEL = '/uploads/pixel.gif';

    /**
     * Holds the bean type to apply scaffolding to.
     *
     * @var string
     */
    public $type = 'newsletter';

    /**
     * Displays the page to send a test mail.
     *
     * @param int $id
     * @param int $page
     * @param int $limit
     * @param string $layout
     * @param int $order
     * @param int $dir
     */
    public function test($id, $page, $limit, $layout, $order, $dir)
    {
        return $this->send_workhorse($id, $page, $limit, $layout, $order, $dir, 'test');
    }

    /**
     * Displays the page to generate the queue beans for this newsletter and send it or
     * the current queue is displayed.
     *
     * @param int $id
     * @param int $page
     * @param int $limit
     * @param string $layout
     * @param int $order
     * @param int $dir
     */
    public function send($id, $page, $limit, $layout, $order, $dir)
    {
        return $this->send_workhorse($id, $page, $limit, $layout, $order, $dir, 'send');
    }
    
    /**
     * Displays the queued page.
     *
     * @param int $id
     * @param int $page
     * @param int $limit
     * @param string $layout
     * @param int $order
     * @param int $dir
     * @param string (optional) $mode defaults to test
     */
    protected function queued($id, $page, $limit, $layout, $order, $dir)
    {
        $this->view = $this->makeView('newsletter/queued');
        $this->view->record = R::load('newsletter', $id);
        
        $this->view->page = $page;
        $this->view->limit = $limit;
        $this->view->layout = $layout;
        $this->view->order = $order;
        $this->view->dir = $dir;
        $this->view->user = $this->user();
        
        $this->view->title = __('newsletter_head_title_queued');
        
        $this->trigger('queue', 'before');
        
        if ($this->input()->post()) {
            try {
                $this->view->record = R::graph($this->input()->post('dialog'), true);

                R::store($this->view->record);
                
                $message = __('action_queued_success');
                with(new Cinnebar_Messenger)->notify($this->user(), $message, 'success');
                $this->trigger('queued', 'after');
                $this->redirect(sprintf('/newsletter/send/%d/%d/%d/%s/%d/%d', $id, $page, $limit, $layout, $order, $dir));

            } catch (Exception $e) {

                $message = __('action_queued_error');
                with(new Cinnebar_Messenger)->notify($this->user(), $message, 'error');

            }
        }

        $this->view->nav = R::findOne('domain', ' blessed = ?', array(1))->hierMenu($this->view->url());
        $this->view->navfunc = $this->view->record->makeMenu('queued', $this->view, $this->view->nav);
        $this->view->urhere = with(new Cinnebar_Menu())->add(__('newsletter_head_title'), $this->view->url('/newsletter'));
        echo $this->view->render();
    }
    
    /**
     * Displays the page to start a queue.
     *
     * @param int $id
     * @param int $page
     * @param int $limit
     * @param string $layout
     * @param int $order
     * @param int $dir
     * @param string (optional) $mode defaults to test
     */
    protected function send_workhorse($id, $page, $limit, $layout, $order, $dir, $mode = 'send')
    {
        $this->cache()->deactivate();
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode(sprintf('/newsletter/%s/%d', $mode, $id))));
        
        $this->view = $this->makeView(sprintf('newsletter/%s', $mode));
        
        $this->view->record = R::load('newsletter', $id);
        if ($this->view->record->isQueued()) {
            return $this->queued($id, $page, $limit, $layout, $order, $dir);
        }
        
        $this->view->page = $page;
        $this->view->limit = $limit;
        $this->view->layout = $layout;
        $this->view->order = $order;
        $this->view->dir = $dir;
        $this->view->user = $this->user();
        
        $this->view->title = __(sprintf('newsletter_head_title_%s', $mode));
        $this->view->mode = $mode;
        
        $this->trigger($mode, 'before');
        
        if ($this->input()->post()) {
            try {
                $this->view->record = R::graph($this->input()->post('dialog'), true);
                $this->view->record->$mode($this); // send or test it
                R::store($this->view->record);
                
                $message = __(sprintf('action_%s_success', $mode));
                with(new Cinnebar_Messenger)->notify($this->user(), $message, 'success');
                $this->trigger($mode, 'after');
                $this->redirect(sprintf('/newsletter/send/%d/%d/%d/%s/%d/%d', $id, $page, $limit, $layout, $order, $dir));

            } catch (Exception $e) {

                $message = __(sprintf('action_%s_error', $mode));
                with(new Cinnebar_Messenger)->notify($this->user(), $message, 'error');

            }
        }

        $this->view->nav = R::findOne('domain', ' blessed = ?', array(1))->hierMenu($this->view->url());
        $this->view->navfunc = $this->view->record->makeMenu($mode, $this->view, $this->view->nav);
        $this->view->urhere = with(new Cinnebar_Menu())->add(__('newsletter_head_title'), $this->view->url('/newsletter'));
        echo $this->view->render();
    }

    /**
     * Displays the newsletter as a web page.
     *
     * @param int $id
     * @param string (optinal) $template
     */
    public function view($id, $template = 'view')
    {
        $this->cache()->deactivate();
        $this->view = $this->makeView(sprintf('newsletter/templates/%s/%s', $this->router()->language(), $template));
        
        $this->view->record = R::load('newsletter', $id);
        $this->view->title = $this->view->record->name;
        $this->view->articles = $this->view->record->getArticles();

        echo $this->view->render();
    }
    
    /**
     * Displays the newsletter archive.
     */
    public function archive()
    {
        $this->cache()->deactivate();
        $this->view = $this->makeView(sprintf('newsletter/templates/%s/archive', $this->router()->language()));
        $this->view->records = R::dispense('newsletter')->getArchived();
        echo $this->view->render();
    }
    
    /**
     * Displays an article as a web page.
     *
     * @param int $id
     */
    public function article($id)
    {
        $this->cache()->deactivate();
        $this->view = $this->makeView(sprintf('newsletter/templates/%s/article', $this->router()->language()));
        
        $this->view->record = R::load('article', $id);

        echo $this->view->render();
    }
    
    /**
     * Displays the opt-in page.
     *
     * An optin that was added by this method has to be enabled by the email address owner
     * within a certain time periode.
     */
    public function optin()
    {
        $this->cache()->deactivate();
        $this->view = $this->makeView('newsletter/optin');
        $this->view->title = __('newsletter_optin_head_title');
        $this->view->record = R::dispense('optin');
        
        if ($this->input()->post()) {
            $this->view->record = R::graph($this->input()->post('dialog'), true);
            try {
                
                R::store($this->view->record);
                $this->view->record->sendMailWithActivationLink($this);
                
                $message = __('action_optin_success');
                with(new Cinnebar_Messenger)->notify($this->user(), $message, 'success');
                
                $this->trigger('optin', 'after');
                
                $this->redirect(sprintf('/newsletter/thankyou/optin', null));

            } catch (Exception $e) {
                
                $message = __('action_optin_error');
                with(new Cinnebar_Messenger)->notify($this->user(), $message, 'error');
                
            }
        }

        $this->pushCampaignsToView();        

        $this->view->nav = R::findOne('domain', ' blessed = ?', array(1))->hierMenu($this->view->url());
        $this->view->urhere = with(new Cinnebar_Menu())->add(__('newsletter_optin_head_title'), $this->view->url('/newsletter/optin'));
        echo $this->view->render();
    }

    /**
     * Activates a previously optin.
     *
     * @param string $hash of email address
     */
    public function activate($hash = null)
    {
        $this->cache()->deactivate();
        $this->view = $this->makeView('newsletter/thankyou');
        $this->view->record = R::findOne('optin', ' hash = ? ', array($hash));
        if ( ! $this->view->record->getId()) {
            return $this->error('404');
        }
        if ($this->view->record->enabled) {
            $this->redirect('/newsletter/thankyou/alreadyactivated');
        }
        try {
            $this->view->record->enabled = true;
            R::store($this->view->record);
            $this->redirect('/newsletter/thankyou/activation');
        } catch (Exception $e) {
            return $this->error('404');
        }
    }

    /**
     * Updates the queue bean that was generated when the newsletter was send to the optin beans
     * and outputs a blind pixel gif
     *
     * @see http://stackoverflow.com/questions/4665960/most-efficient-way-to-display-a-1x1-gif-tracking-pixel-web-beacon
     *
     * @param string $hash of the queue
     */
    public function queue($hash = null)
    {
        if ($queue = R::findOne('queue', ' hash = ? ', array($hash))) {
            try {
                $queue->wasOpened();
                R::store($queue);
            } catch (Exception $e) {
                error_log($e);
            }
        }
        $this->response()->addHeader('Content-Type', 'image/gif');
        echo base64_decode("R0lGODdhAQABAIAAAPxqbAAAACwAAAAAAQABAAACAkQBADs=");
    }
    
    /**
     * Displays the opt-out page.
     *
     * @param string (optional) $hash of email address
     */
    public function optout($hash = null)
    {
        $this->cache()->deactivate();
        $this->view = $this->makeView('newsletter/optout');
        $this->view->title = __('newsletter_optout_head_title');
        
        $this->view->record = R::findOne('optin', ' hash = ?', array($hash));
        if ( ! $this->view->record) return $this->error('404');
        if ( $this->view->record->hash != $hash) return $this->error('404');
        $this->view->hash = $hash;
        
        if ($this->input()->post()) {
            try {
                R::trash($this->view->record);
                $this->redirect('/newsletter/thankyou/optout');
            } catch (Exception $e) {
                // failed to optout, why?
            }
        }
        
        $this->view->nav = R::findOne('domain', ' blessed = ?', array(1))->hierMenu($this->view->url());
        $this->view->urhere = with(new Cinnebar_Menu())->add(__('newsletter_optout_head_title'), $this->view->url('/newsletter/optout'));
        echo $this->view->render();
    }
    
    /**
     * Displays the opt-in page.
     *
     * @param string $for what event do we thank the user?
     */
    public function thankyou($for = 'nothing')
    {
        $this->cache()->deactivate();
        $this->view = $this->makeView('newsletter/thankyou');
        $this->view->title = __('newsletter_thankyou_head_title');
        $this->view->thankyoufor = $for;
        echo $this->view->render();
    }
    
    /**
     * This will run before scaffold edit performs.
     *
     * @return void
     */
    public function before_edit()
    {
        $this->pushCampaignsToView();
        $this->pushArticlesToView();
        //$this->pushOptinsToView();
        $this->pushSmtpsToView();
    }
    
    /**
     * This will run before newsletter test performs.
     *
     * @return void
     */
    public function before_test()
    {
        $this->pushOptinsToView();
    }
    
    /**
     * This will run before scaffold add performs.
     *
     * @return void
     */
    public function before_add()
    {
        $this->pushCampaignsToView();
        $this->pushArticlesToView();
        //$this->pushOptinsToView();
        $this->pushSmtpsToView();
    }
    
    /**
     * Pushes article beans that belong to this newsletter to the view.
     *
     * @return void
     */
    protected function pushArticlesToView()
    {
        $this->view->articles = $this->view->record->getArticles();
    }
    
    /**
     * Pushes (enabled) smtp beans to the view.
     *
     * @return void
     */
    protected function pushSmtpsToView()
    {
        $this->view->smtps = R::find('smtp', ' enabled = ? ORDER BY name', array(true));
    }
    
    /**
     * Pushes (enabled) optin beans to the view.
     *
     * @return void
     */
    protected function pushOptinsToView()
    {
        $this->view->optins = $this->view->record->getOptins();
    }

    /**
     * Pushes enabled campaign beans to the view.
     *
     * @return void
     */
    protected function pushCampaignsToView()
    {
        $this->view->campaigns = R::find('campaign', ' enabled = ? ORDER BY name', array(true));
    }
}
