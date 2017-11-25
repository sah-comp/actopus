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
 * Setting controller.
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_Setting extends Controller_Scaffold
{
	/**
	 * Renders general setting page.
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
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/setting/index/')));
		if ( ! $this->permission()->allowed($this->user(), 'setting', 'edit')) {
			return $this->error('403');
		}

		$this->view = $this->makeView('setting/index');
        $this->view->title = __('setting_head_title');
        $this->view->user = $this->user();
        $this->view->record = R::load('setting', 1);

        $this->trigger('index', 'before');

		if ($this->input()->post('submit')) {
		    try {
		        $this->view->record = R::graph($this->input()->post('dialog'));
		        R::store($this->view->record);
                $message = __('setting_edit_success');
                with(new Cinnebar_Messenger)->notify($this->user(), $message, 'success');
                $this->trigger('index', 'after');
                $this->redirect('/setting/index');
		    } catch (Exception $e) {
		        $message = __('setting_edit_error '.$e);
                with(new Cinnebar_Messenger)->notify($this->user(), $message, 'error');
		    }
		}


        $this->view->nav = R::findOne('domain', ' blessed = ? LIMIT 1', array(1))->hierMenu($this->view->url());
        $this->view->urhere = with(new Cinnebar_Menu())->add(__('setting_head_title'), $this->view->url('/setting/index'));
        $this->trigger('index', 'after');
        echo $this->view->render();
	}

	/**
	 * Renders the role based access control settings page and lets admin change rbacs.
	 *
	 * @return void
	 */
	public function rbac()
	{
        $this->cache()->deactivate();
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/setting/rbac/')));
		if ( ! $this->permission()->allowed($this->user(), 'rbac', 'index')) {
			return $this->error('403');
		}

		$this->view = $this->makeView('setting/rbac');
        $this->view->title = __('rbac_head_title');

		$this->rbacs = array();

		if ($this->input()->post('submit')) {

		    if ( ! $this->permission()->allowed($this->user(), 'rbac', 'edit')) {
    			return $this->error('403');
    		}

			$this->rbacs = $this->input()->post('dialog');
			if ($this->updateRbacs($this->rbacs)) {
				$this->redirect('/setting/rbac');
			}
		}

		//$this->actions('rbac');
		$this->view->roles = $this->loadAssoc('role');
		$this->view->domains = $this->loadAssoc('domain');
		$this->view->actions = $this->loadAssoc('action');

        $this->view->nav = R::findOne('domain', ' blessed = ? LIMIT 1', array(1))->hierMenu($this->view->url());
        $this->view->urhere = with(new Cinnebar_Menu())->add(__('setting_head_title'), $this->view->url('/setting/index'))->add(__('rbac_head_title'), $this->view->url('/setting/rbac'));

        echo $this->view->render();
	}

	/**
	 * Sets the backend language iso code in the users session.
	 *
	 * If the given iso code is not found the frontend language is asked from the router
	 * to set backend language to that.
	 * If a GET parameter goto is given the system will redirect there.
	 *
	 * @uses Cinnebar_Router::language()
	 *
	 * @param string $iso
	 * @return void
	 */
	public function language($iso)
	{
        $this->cache()->deactivate();
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/home/')));
        if ( ! $language = R::findOne('language', ' iso = ? AND enabled = 1 LIMIT 1', array($iso))) {
            $iso = $this->router()->language();
        }
        $goto = $this->input()->get('goto');
        $_SESSION['backend']['language'] = $iso;
        if ($goto) $this->redirect($goto);
        return;
    }

	/**
	 * Updates all rabc rules and returns wether it went well or not.
	 *
	 * @param array $rbacs
	 * @return bool
	 */
	protected function updateRbacs(array $rbacs)
	{
		$rbac_collection = array();
		foreach ($rbacs as $role_id=>$domains) {
			$role = R::load('role', $role_id);
			foreach ($domains as $domain_id=>$actions) {
				$domain = R::load('domain', $domain_id);
				if ( ! $rbac = R::findOne('rbac', ' role_id = ? AND domain_id = ? LIMIT 1', array($role_id, $domain_id))) {
					$rbac = R::dispense('rbac');
				}
				$rbac->role = $role;
				$rbac->domain = $domain;
				unset($rbac->ownPermission);
				foreach ($actions as $action_id=>$allow) {
					$action = R::load('action', $action_id);
					if ( ! $permission = R::findOne('permission', ' rbac_id = ? AND action_id = ? LIMIT 1', array($rbac->getId(), $action_id))) {
						$permission = R::dispense('permission');
					}
					$permission->action = $action;
					$permission->allow = $allow;
					$rbac->ownPermission[] = $permission;
				}
				$rbac_collection[] = $rbac;
			}
		}
		// store them all
		R::begin();
		try {
			R::storeAll($rbac_collection);
			R::commit();
			return true;
		} catch (Exception $e) {
			R::rollback();
            Cinnebar_Logger::instance()->log('Setting could not update rbac: '.$e, 'sql');
			return false;
		}
	}

    /**
     * This will run before scaffold edit performs.
     *
     * @return void
     */
    public function before_index()
    {
        $this->pushDomainsToView();
        $this->pushEnabledCurrenciesToView();
        $this->pushEnabledCountriesToView();
        $this->pushEnabledLanguagesToView();
        $this->pushEnabledInvoicetypesToView();
    }

    /**
     * Pushes domain beans to the view.
     *
     * @return void
     */
    protected function pushDomainsToView()
    {
        $this->view->domains = R::findAll('domain', 'ORDER BY name');
    }

    /**
     * Pushes currency beans to the view.
     *
     * @return void
     */
    protected function pushEnabledCurrenciesToView()
    {
        $this->view->currencies = R::find('currency', ' enabled = 1 ORDER BY name');
    }

    /**
     * Pushes country beans to the view.
     *
     * @return void
     */
    protected function pushEnabledCountriesToView()
    {
        $this->view->countries = R::find('country', ' enabled = 1 ORDER BY name');
    }

    /**
     * Pushes enabled cardtypes in alphabetic order to the view.
     */
    public function pushEnabledInvoicetypesToView()
    {
        $this->view->invoicetypes = R::find('invoicetype', ' 1 ORDER BY name');
    }

	/**
	 * Load roles, domain and actions.
	 *
	 * @param string $type of bean to load
	 * @return array
	 */
	protected function loadAssoc($type)
	{
		$sql = <<<SQL

		SELECT
			{$type}.id as id,
			{$type}.name as name
		FROM
			{$type}
		ORDER BY name
SQL;
        return R::$adapter->getAssoc($sql);
	}
}
