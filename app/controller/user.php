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
 * Manages CURD on user beans.
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_User extends Controller_Scaffold
{
    /**
     * Holds the bean type to apply scaffolding to.
     *
     * @var string
     */
    public $type = 'user';
    
    /**
     * This will run before scaffold edit performs.
     *
     * @return void
     */
    public function before_edit()
    {
        $this->pushRolesToView();
        $this->pushTeamsToView();
        $this->pushDomainsToView();
        //$this->pushEnabledLanguagesToView();
    }
    
    /**
     * This will run before scaffold add performs.
     *
     * @todo Get rid of storing the password in the session for that moment until its sent by email
     *
     * @return void
     */
    public function before_add()
    {
        $this->pushRolesToView();
        $this->pushTeamsToView();
        $this->pushDomainsToView();
        //$this->pushEnabledLanguagesToView();
        if ($this->input()->post()) {
            $dialog = $this->input()->post('dialog');
            $_SESSION['user']['pw_once'] = $dialog['pw'];
        }
    }
    
    /**
     * Send invitation mail after user bean was added.
     */
    public function after_add()
    {
        if ($this->view->record->getId()) {
            //$this->view->record->sendInvite($this);
            unset($_SESSION['user']['pw_once']);
        }
    }
    
    /**
     * Pushes domains user has access (index) to the view.
     */
    public function pushDomainsToView()
    {
        $this->view->domains = R::findAll('domain', ' ORDER BY name');
    }
    
    /**
     * Pushes role beans to the view.
     *
     * @return void
     */
    protected function pushRolesToView()
    {
        $this->view->roles = R::findAll('role', 'ORDER BY sequence');
    }
    
    /**
     * Pushes team beans to the view.
     *
     * @return void
     */
    protected function pushTeamsToView()
    {
        $this->view->teams = R::findAll('team', 'ORDER BY sequence');
    }
}
