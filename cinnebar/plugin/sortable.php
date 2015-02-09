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
 * Updates the sequence of a set of beans after sorting with jQuery.
 *
 * @package Cinnebar
 * @subpackage Plugin
 * @version $Id$
 */
class Plugin_Sortable extends Cinnebar_Plugin
{
	/**
	 * Updates all given beans of a certain type after being sorted by client in frontend.
	 *
	 * @param string $type
	 * @param string (optional) $varname is the name of the variable the list of sortable items is in
	 * @return void
	 */
	public function execute($type, $varname = 'sequence')
	{
        session_start();
        $this->controller()->cache()->deactivate();
        if ($sequence = $this->controller()->input()->get($varname)) {
            foreach ($sequence as $n => $id) {
                $bean = R::load($type, $id);
                if ( ! $bean->getId()) continue; // skip if that id does not exist
                $bean->sequence = $n;
                try {
                    R::store($bean);
                } catch (Exception $e) {
                    Cinnebar_Logger::instance()->log($e, 'exceptions');
                    return false;
                }
            }
        }
		return true;
	}
}
