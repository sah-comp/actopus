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
 * Sandbox controller.
 *
 * Use the sandbox controller to play around with controllers, models, views and templates, viewhelpers
 * and all the other features you implement.
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_Sandbox extends Cinnebar_Controller
{
    /**
     * Play around in the sandbox.
     *
     * @param string (optional) $type of the bean to list, defaults to 'token'
     * @param int (optional) $offset from where the resultset will begin, defaults to 0
     * @param int (optional) $limit of results to get per request, defaults to 10
     */
    public function index($type = 'token', $offset = 0, $limit = 10)
    {
        $this->cache()->deactivate();
        
        $offset = max(0, $offset);
        $limit = max(1, $limit);
        
        session_start();
        if ( ! isset($_SESSION['sandbox'])) $_SESSION['sandbox'] = 0;
        $_SESSION['sandbox']++;
        
        //Cinnebar_Logger::instance()->log('Sandy plays with...', 'sandbox');
        Cinnebar_Logger::instance()->log('Stash in the sandbox.', 'sandbox');

        $view = $this->makeView('sandbox/index');

        $token = R::load($type, 1);
        $tokens = R::find($type, ' 1 LIMIT ?, ?', array((int)$offset, (int)$limit));
        
        $view->name = 'Guest';
        if ($this->input()->post('name')) $view->name = $this->input()->post('name');
        
        $view->switch = 'Off';
        if ($this->input()->get('switch')) $view->switch = 'On';
        
        $view->record = $token;
        $view->records = $tokens;
        
        $view->offset = $offset;
        $view->limit = $limit;
        $view->counter = $_SESSION['sandbox'];
        
        $view->helloworld = R::dispense('helloworld');
        
        $settings = array(
            'baseUri' => 'http://stefans-mac-mini.local/webdav/',
            'userName' => 'admin',
            'password' => 'webdav01'
        );
        $view->client = new Sabre\DAV\Client($settings);
        
        echo $view->render();
    }
    
    /**
     * Play around with webdav in the sandbox.
     */
    public function webdav()
    {
        $this->cache()->deactivate();

        $view = $this->makeView('sandbox/webdav');
        
        $settings = array(
            'baseUri' => 'http://stefans-mac-mini.local/webdav/',
            'userName' => 'admin',
            'password' => 'webdav01'
        );
        try {
            $dav = new Sabre\DAV\Client($settings);
            $view->dav['options'] = $dav->options();
            $view->dav['response'] = $dav->propfind('', array('{DAV:}displayname', '{DAV:}getcontentlength'), 1);
        } catch (Exception $e) {
            $view->dav = array('error' => $e);
        }
        echo $view->render();
    }
    
    /**
     * Play around with partials in sandbox.
     */
    public function partial()
    {
        $this->cache()->deactivate();
        $view = $this->makeView('sandbox/partial');
        $view->nav = with(new Cinnebar_Menu)->add('back', $view->url('/sandbox/index/token'));
        echo $view->render();
    }
    
    /**
     * Play around with Cinnebar_Menu in sandbox.
     */
    public function menu()
    {
        $this->cache()->deactivate();

        $view = $this->makeView('sandbox/menu');
        $view->nav = with(new Cinnebar_Menu)->add('back', $view->url('/sandbox/index/token'));
        // play around with a self made menu from a bean
        $view->menu = R::findOne('domain', 'blessed = ?', array(1))->hierMenu($view->url(), $this->router()->language());
        // play around with a manual menu
        $view->menu2 = with(new Cinnebar_Menu)
            ->add(__('home'), $view->url('/home/index'))
            ->add(__('mail'), $view->url('/home/mail'), with(new Cinnebar_Menu)
                ->add(__('inbox'), $view->url('/home/mail/inbox'), with(new Cinnebar_Menu)
                    ->add(__('local'), $view->url('/home/mail/inbox/local'))
                    ->add(__('remote'), $view->url('/home/mail/inbox/remote')))
                ->add(__('outbox'), $view->url('/home/mail/outbox')))
            ->add(__('quote'), $view->url('/home/quote'))
            ->add(__('invoice'), $view->url('/home/invoice'));
        echo $view->render();
    }
}
