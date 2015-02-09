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
 * Login controller.
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_Login extends Cinnebar_Controller
{
    /**
     * Displays the login page.
     *
     * If we receive a POST request the login credentials will be checked against the user database
     * and if there is a valid, not banned, not retired account that qualifies the backend language
     * session var is set to the current frontend language, the session id is bound to the user
     * so we can later on track that account with housekeeping.
     */
    public function index()
    {
        $this->cache()->deactivate();
        session_start();
        $view = $this->makeView('login');
        $view->title = __('login_head_title');
        $view->record = R::dispense('login');
        $view->fck = array();
        $view->record->goto = $this->input()->get('goto');
        if ($this->input()->post()) {
            $view->record->import($this->input()->post('dialog'));
            $trial = R::dispense('user');
            if ( ! $user = $trial->login($view->record->name, $view->record->pw)) {
                $view->fck = $trial->errors();
                Cinnebar_Logger::instance()->log(__('login_failed'), 'warn');
            } else {
                try {
                    $_SESSION['user']['id'] = $user->getId();
                    $_SESSION['backend']['language'] = $this->router()->language();
                    $user->sid = session_id();
                    R::store($user);
                    $message = __('login_welcome_user', array($user->name));
                    with(new Cinnebar_Messenger)->notify($user, $message, 'success');
                    $this->redirect($user->home($view->record->goto));
                } catch (Exception $e) {
                    $view->fck = $trial->errors();
                    Cinnebar_Logger::instance()->log(__('login_could_not_store_user: '.$e), 'exceptions');
                }
            }
        }
        $view->nav = with(new Cinnebar_Menu)->add(__('domain_login'), $view->url('/login'));
        echo $view->render();
    }
}
