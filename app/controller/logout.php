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
 * Logout controller.
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_Logout extends Cinnebar_Controller
{
    /**
     * Logout the current user and redirect to the login page with a message about successful logout.
     *
     * @uses Model_User::logout() to log out current user
     * @uses Cinnebar_Cache::deactivate() to turn off caching of the page
     * @return void
     */
    public function index()
    {
        session_start();
        $this->cache()->deactivate();
        if ( $this->auth()) {
            $this->user()->logout();
            try {
                R::store($this->user());
            } catch (Exception $e) {
                // hmm, could not logout
            }
        }
        session_destroy();
        session_regenerate_id();
        $this->redirect('/login/index/byebye/');
    }
}