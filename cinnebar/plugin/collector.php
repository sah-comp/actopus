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
 * Sets a flag in session container to mark a certain bean as checked or not.
 *
 * @package Cinnebar
 * @subpackage Plugin
 * @version $Id$
 */
class Plugin_Collector extends Cinnebar_Plugin
{
    /**
     * Toggles the flag in $_SESSION collector array.
     *
     * @uses $_SESSION
     * @param string $type
     * @param mixed (optional) $id
     * @return void
     */
    public function execute($type, $id)
    {
        session_start();
        if (isset($_SESSION['collector'][$type][$id]) && $_SESSION['collector'][$type][$id]) {
            unset($_SESSION['collector'][$type][$id]);
        } else {
            $_SESSION['collector'][$type][$id] = true;
        }
        return null;
    }
}
