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
 * Manages role based access control on user beans.
 *
 * @uses $_SESSION to cache current users permission after the first usage for the active session
 *
 * @package Cinnebar
 * @subpackage Permission
 * @version $Id$
 */
class Cinnebar_Permission implements iPermission
{
	/**
	 * Constructs a new Permission
	 */
	public function __construct()
	{
	}

	/**
	 * Returns wether user is allowed to do action on domain or not.
	 *
	 * @param mixed $user
	 * @param string $domain
	 * @param string $action
	 * @return bool
	 */
	public function allowed($user = null, $domain, $action)
	{
		if ( ! $user || ! $user->getId()) return false;
		if ($user->admin) return true;
		$this->load($user);
		if (isset($_SESSION['permissions'][$domain][$action]))
			return (bool)$_SESSION['permissions'][$domain][$action];
		return false;
	}
	
	/**
	 * returns an key/value array of all domains where user can do action.
	 *
	 * @param mixed $user
	 * @param string $action
	 * @return array
	 */
	public function domains($user, $action)
	{
		if ( ! $user || ! $user->getId()) {
			return array();
		}
		$this->load($user);
		$ret = array();
		foreach ($_SESSION['permissions'] as $domain=>$actions) {
			if (isset($actions[$action]) && $actions[$action]) {
				$ret[$domain] = __('domain_'.$domain); // localized name
			}
		}
		asort($ret);
		return $ret;
	}
	
	/**
	 * Loads the users permissions and caches them in users session.
	 *
	 * @param mixed $user
	 */
	public function load($user = null)
	{
		if (isset($_SESSION['permissions']) && is_array($_SESSION['permissions'])) return;
		$_SESSION['permissions'] = array();
		$sql = <<<SQL

			SELECT
				domain.name AS domain,
				action.name AS action,
				permission.allow AS allow
			FROM
				user

			LEFT JOIN role_user ON role_user.user_id = user.id
			LEFT JOIN role ON role.id = role_user.role_id
			LEFT JOIN rbac ON rbac.role_id = role.id
			LEFT JOIN permission ON permission.rbac_id = rbac.id
			LEFT JOIN domain ON domain.id = rbac.domain_id
			LEFT JOIN action ON action.id = permission.action_id

			WHERE
				user.id = ?

			ORDER BY
				role.sequence
SQL;
		$rbacs = R::getAll($sql, array($user->getId()));
		foreach ($rbacs as $n=>$rbac) {
			$_SESSION['permissions'][$rbac['domain']][$rbac['action']] = $rbac['allow'];
		}
	}
}
