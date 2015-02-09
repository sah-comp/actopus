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
 * Home controller.
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_Home extends Cinnebar_Controller
{
    /**
     * Displays the install page.
     */
    public function index()
    {
        $this->cache()->deactivate();
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/home/index/')));
        $view = $this->makeView('home/index');
        $view->title = __('home_head_title');
        $view->user = $this->user();
        $view->users = $this->user()->whoisonline();
        $view->nav = R::findOne('domain', ' blessed = ? LIMIT 1', array(1))->hierMenu($view->url());
        $view->urhere = with(new Cinnebar_Menu())->add(__('home_head_title'), $view->url('/home'));
        echo $view->render();
    }

    /**
     * Loads users who are online and renders them as gravatars.
     */
    public function whoisonline()
    {
        $this->cache()->deactivate();
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/home/index/')));
        $view = $this->makeView('shared/user/gravatars');
        $view->user = $this->user();
        $view->users = $this->user()->whoisonline();
        echo $view->render();
    }
}
