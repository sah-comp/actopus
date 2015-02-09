<?php
/**
 * The front controller which bootstraps the Cinnebar system and runs it.
 *
 * @package Cinnebar
 * @author $Author$
 * @version $Id$
 */

/**
 * Define the BASEDIR constant which is the path to this file.
 */
define('BASEDIR', dirname(__FILE__));

/**
 * Define the configuration file to use.
 */
define('S_CONFIG', 'default');

/**
 * Require the bootstrap file.
 */ 
require_once BASEDIR.'/cinnebar/bootstrap.php';

// Take off.
$cinnebar->run();
?>