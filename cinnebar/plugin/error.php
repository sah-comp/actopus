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
 * Displays an error page with the given error code.
 *
 * @package Cinnebar
 * @subpackage Plugin
 * @version $Id$
 */
class Plugin_Error extends Cinnebar_Plugin
{
    /**
     * Renders a error page to the client.
     *
     * @uses Cinnebar_Cache::deactivate() to turn off caching for the errornous URL
     * @uses Cinnebar_Controller::makeView() to factory the error view
     * @param string (optional) $code The error code you want to render, defaults to 404
     * @return void
     */
    public function execute($code = '404')
    {
        $this->controller()->cache()->deactivate();
        $view = $this->controller()->makeView('error/index');
        $view->title = __('error_head_title');
        $view->nav = with(new Cinnebar_Menu)->add(__('domain_app'), $view->url('/home'));
        $view->code = $code;
        echo $view->render();
    }
}