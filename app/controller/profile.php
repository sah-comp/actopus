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
 * Manages user profile.
 *
 * If no hash code is given the hash code of the current user is used to located the profile.
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_Profile extends Controller_Scaffold
{
    /**
     * Renders the current users profile page.
     *
     * @param int $page
     * @param int $limit
     * @param string $layout
     * @param int $order
     * @param int $dir
     */
    public function index($page = 1, $limit = self::LIMIT, $layout = self::LAYOUT, $order = 0, $dir = 0)
    {
        $this->cache()->deactivate();
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode(sprintf('/profile/index/%s', $hash))));
        if ( ! $this->permission()->allowed($this->user(), 'profile', 'edit')) {
			return $this->error('403');
		}
        $this->view = $this->makeView('profile/index');
        $this->view->title = __('profile_head_title');
        $this->view->record = $this->view->user = $this->user();

        $this->trigger('edit', 'before');

        if ($this->input()->post()) {
            $this->view->record = R::graph($this->input()->post('dialog'), true);
            try {
                R::store($this->view->record);

                $message = __('action_edit_success');
                with(new Cinnebar_Messenger)->notify($this->user(), $message, 'success');

                $this->trigger('edit', 'after');

                $this->redirect('/profile/index');

            } catch (Exception $e) {

                $message = __('action_edit_error');
                with(new Cinnebar_Messenger)->notify($this->user(), $message, 'error');

            }
        }

        $this->view->nav = R::findOne('domain', ' blessed = ?', array(1))->hierMenu($this->view->url());
        $this->view->urhere = with(new Cinnebar_Menu())->add(__('profile_head_title'), $this->view->url('/profile/index'));
        echo $this->view->render();
    }

    /**
     * Renders some other users profile page.
     *
     * @param string $hash of field "ego" in user bean
     */
    public function visit($hash)
    {
        $this->cache()->deactivate();
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode(sprintf('/profile/index/%s', $hash))));
        if ( ! $this->permission()->allowed($this->user(), 'profile', 'index')) {
			return $this->error('403');
		}
        $this->view = $this->makeView('profile/visit');
        $this->view->title = __('profile_head_title');
        if ( ! $this->view->record = R::findOne('user', ' ego = ?', array($hash))) {
            return $this->error('404');
        }
        $this->view->user = $this->user();

        $this->trigger('visit', 'before');
        // may do some thing on POST?
        $this->trigger('visit', 'after');

        $this->view->nav = R::findOne('domain', ' blessed = ?', array(1))->hierMenu($this->view->url());
        $this->view->urhere = with(new Cinnebar_Menu())->add(__('profile_head_title_visit'), $this->view->url(sprintf('/profile/visit/%s', $hash)));
        echo $this->view->render();
    }

    /**
     * Renders the changepassword page.
     *
     * @param string (optional) $hash of field "ego" in user bean
     */
    public function changepassword($hash = null)
    {
        $this->cache()->deactivate();
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode(sprintf('/profile/index/%s', $hash))));
        if ( ! $this->permission()->allowed($this->user(), 'profile', 'edit')) {
			return $this->error('403');
		}
        $this->view = $this->makeView('profile/changepassword');
        $this->view->title = __('profile_head_title');
        if (null === $hash) $hash = $this->user()->ego;
        $this->view->record = R::findOne('user', ' ego = ? AND (banned = 0 AND deleted = 0)', array($hash));
        $this->view->user = $this->user();

        $this->trigger('edit', 'before');

        if ($dialog = $this->input()->post('dialog')) {
            if ($this->view->record->changePassword($dialog['pw'], $dialog['pw_new'], $dialog['pw_repeated'])) {
                try {
                    R::store($this->view->record);

                    $message = __('action_changepassword_success');
                    with(new Cinnebar_Messenger)->notify($this->user(), $message, 'success');

                    $this->trigger('edit', 'after');

                    $this->redirect('/profile/index');

                } catch (Exception $e) {

                    $message = $this->view->record->actionAsHumanText('changepassword', 'error', $this->user());
                    with(new Cinnebar_Messenger)->notify($this->user(), $message, 'error');

                }
            } else {

                $message = __('action_changepassword_error');
                with(new Cinnebar_Messenger)->notify($this->user(), $message, 'error');
            }
        }

        $this->view->nav = R::findOne('domain', ' blessed = ?', array(1))->hierMenu($this->view->url());
        $this->view->urhere = with(new Cinnebar_Menu())->add(__('profile_head_title'), $this->view->url('/profile/changepassword'));
        echo $this->view->render();
    }

    /**
     * This will run before scaffold edit performs.
     *
     * @return void
     */
    public function before_edit()
    {
        $this->pushEnabledLanguagesToView();
    }
}
