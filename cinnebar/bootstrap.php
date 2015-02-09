<?php
/**
 * Initializes the system, configures it, injects dependencies and instantiates a Cinnebar object.
 *
 * @package Cinnebar
 * @author $Author$
 * @version $Id$
 */
 
/**
 * Set internal encoding to UTF-8.
 */
mb_internal_encoding('UTF-8');

/**
 * Require redbean as ORM.
 */
//require_once BASEDIR.'/vendors/RedBeanPHP3_3/rb.php';
//require_once BASEDIR.'/vendors/RedBeanPHP3_4_3/rb.php';
require_once BASEDIR.'/vendors/RedBeanPHP3_5_4/rb.php';
RedBean_Plugin_Cooker::enableBeanLoading(true); // to allow compatibility to RB3.3

/**
 * Require the Cinnebar no-framework core.
 */ 
require_once BASEDIR.'/cinnebar/cinnebar.pack.php';

/**
 * Require the config file.
 */ 
require_once BASEDIR.'/config/'.S_CONFIG.'.php';

/**
 * Define the path to the app directory.
 */
define('APPDIR', BASEDIR.'/'.$config['app']);

/**
 * Define the maximum file size for user uploads.
 */
define('APP_MAX_FILE_SIZE', $config['upload']['maxfilesize']);

/**
 * Define the maximum session lifetime in seconds.
 */
define('MAX_SESS_LIFETIME', 14400); // 4 hours

/**
 * Set GC max lifetime according to maximum session lifetime.
 */
ini_set('session.gc_maxlifetime', MAX_SESS_LIFETIME);

/**
 * Define the GUI theme to use.
 */
if ( ! defined('S_THEME')) {
    define('S_THEME', $config['theme']);
}

$stopwatch = new Cinnebar_Stopwatch();
$stopwatch->start();

// Instanciate the autoloader and register it
$autoloader = new Cinnebar_Autoloader();
$autoloader->addDirectory(BASEDIR.'/vendors');
$autoloader->register();

// shall we activate a database?
if (isset($config['db']['active']) && $config['db']['active'] === true) {
    // yes: setup the database with RedBeanPHP
    R::setup($config['db']['driver'].':host='.
        $config['db']['host'].';dbname='.$config['db']['database'],
        $config['db']['username'],
        $config['db']['password']);
    if (isset($config['db']['freeze']) && $config['db']['freeze'] === true) {
        R::freeze(true);
    }
}

// There shall be non url rewriter and session id gets handled by cookies only
ini_set('url_rewriter.tags', '');
ini_set('session.use_trans_sid', '0');
ini_set('session.use_cookies', '1');
ini_set('session.use_only_cookies', '1');

// set a sessionhandler if configured
if (isset($config['sessionhandler']) && ! empty($config['sessionhandler'])) {
    $sessionhandler_name = 'Sessionhandler_'.ucfirst(strtolower($config['sessionhandler']));
    $sessionhandler = new $sessionhandler_name;
    session_set_save_handler(array($sessionhandler, 'open'),
                             array($sessionhandler, 'close'),
                             array($sessionhandler, 'read'),
                             array($sessionhandler, 'write'),
                             array($sessionhandler, 'destroy'),
                             array($sessionhandler, 'gc'));
    register_shutdown_function('session_write_close');
}

// set the session id
session_name('CINNEBARv1');

// Instantiate a Facade.
$cinnebar = new Cinnebar();

// Register a shutdown function
register_shutdown_function(array($cinnebar, 'stop'), $config);

// Inject dependencies.
$cinnebar->di(array(
    'config' => new Cinnebar_Config($config),
    'request' => new Cinnebar_Request(),
    'response' => new Cinnebar_Response(),
    'cache' => new Cinnebar_Cache($config['cache']),
    'input' => new Cinnebar_Input(),
    'router' => new Cinnebar_Router($config['router']),
    'stopwatch' => $stopwatch,
    'permission' => new Cinnebar_Permission()
));
