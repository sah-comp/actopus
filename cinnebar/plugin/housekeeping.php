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
 * Does housekeeping thingys.
 *
 * This should be called in a certain interval to keep up a session and check for new stuff of a user
 * when logged in.
 *
 * @package Cinnebar
 * @subpackage Plugin
 * @version $Id$
 */
class Plugin_Housekeeping extends Cinnebar_Plugin
{
	/**
	 * Refreshed a user seesion and does other user centric stuff.
	 *
	 * @param string $type
	 * @param string (optional) $varname is the name of the variable the list of sortable items is in
	 * @return void
	 */
	public function execute()
	{
        session_start();
        $this->controller()->cache()->deactivate();
        if ( ! $this->controller()->auth()) return null;
        if ( ! isset($_SESSION['heartbeats'])) $_SESSION['heartbeats'] = 0;
        $_SESSION['heartbeats']++;
		return true;
	}
}
