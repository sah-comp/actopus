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
 * Drops an existing bean own(ed) or shared by a master bean.
 *
 * @package Cinnebar
 * @subpackage Plugin
 * @version $Id$
 */
class Plugin_Detach extends Cinnebar_Plugin
{
	/**
	 * Deletes a Bean and drops the sub form from the clients view.
	 *
	 * @param string $prefix has to be either own or shared
	 * @param string $type
	 * @param mixed (optional) $id
	 * @param mixed $(optional) master_id
	 */
	public function execute($prefix, $type, $id = 0, $master_id = 0)
	{
        session_start();
        $this->controller()->cache()->deactivate();
        
		return $this->$prefix($type, $id, $master_id);
	}
	
	/**
	 * Deletes a own(ed) Bean.
	 *
	 * @param string $type
	 * @param mixed $id
	 * @param mixed $master_id
	 */
	protected function own($type, $id, $master_id)
	{
		$detachable = R::load($type, $id);
		if ( ! $detachable->getId()) return;
		try {
			R::trash($detachable);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * Disconnects a shared Bean from its master.
	 *
	 * @todo Find a better way to get rid of shared relationship.
	 *
	 * @param string $type
	 * @param mixed $id
	 * @param mixed $master_id
	 */
	protected function shared($type, $id, $master_id)
	{
		try {
			$sharedContainer = 'shared'.ucfirst($type);
			$master = R::load($this->controller()->type, $master_id);
			if ( ! $master->getId()) return;
			$shared = $master->$sharedContainer;
			if ( ! isset($shared[$id])) return;
			unset($shared[$id]);
			$master->$sharedContainer = $shared;
			R::store($master);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
}
