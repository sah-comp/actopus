<?php
/**
 * Cinnebar example configuration file.
 *
 * Copy this file and name it config.php to create the required configuration file for your
 * Cinnebar installation.
 *
 * The following configuration can be made here:
 * - Cache (required) for {@link Cinnebar_Cache}
 * - Router (required) for {@link Cinnebar_Router}
 * - Theme (required) for {@link Cinnebar_View}
 * - Database (optional) for RedBean ORM {@link http://redbeanphp.com/}
 * - Logging (optional) for {@link Cinnebar_Logger}
 *
 * @package Cinnebar
 * @subpackage Configuration
 * @author $Author$
 * @version $Id$
 */

 /**
  * Holds the name of the app directory to use.
  *
  * @global string $config['app']
  * @name $config_app
  */
 $config['app'] = 'app';

/**
 * Holds the name of the theme to use.
 *
 * A theme consists of templates, css and javascripts. It defines the look and feel of
 * the frontend of the site or app you develop with Cinnebar.
 *
 * @see Cinnebar_View::__construct()
 * @global string $config['theme']
 * @name $config_theme
 */
$config['theme'] = 'default';

/**
 * Holds the version string.
 *
 * @global string $config['version']
 * @name $config_version
 */
$config['version'] = 'Autumn 2012';

/**
 * Switch to decide wether to use phpass as a password hasher or not.
 *
 * To learn more about phpass visit the site at {@link http://www.openwall.com/phpass/}.
 *
 * @see Model_User::__construct()
 * @global string $config['user']['phpass']
 * @name $user_phpass
 */
$config['user']['phpass'] = true;

/**
 * Holds the name of the sessionhandler to use.
 *
 * Leave this blank to use the internally set PHP sessionhandler or set this to
 * one of the sessionhandlers that can be found in the sessionhandler folder.
 * If this is not set in your config.php then the default sessionhandler will be used.
 *
 * @see Cinnebar_Sessionhandler
 * @global string $config['sessionhandler']
 * @name $config_sessionhandler
 */
$config['sessionhandler'] = ''; // e.g. 'apc' or 'database'

/**
 * Holds flag which decides wether to handle logs or not at the end of a cyle.
 *
 * A theme consists of templates, css and javascripts. It defines the look and feel of
 * the frontend of the site or app you develop with Cinnebar.
 *
 * @see Cinnebar_Logger::write()
 * @global bool $config['logger']['active']
 * @name $config_logger_active
 */
$config['logger']['active'] = false;

/**
 * Holds one or more writers which are used to write logs at the end of a cycle.
 *
 * Do not forget to set {@link $config_logger_active} to true, otherwise the writers
 * you set here are never called.
 *
 * @see Cinnebar_Writer to learn which writers are there and how to implement your own
 * @global array $config['logger']['writer']
 * @name $config_logger_writer
 */
$config['logger']['writer'] = array(
    'file'
);

/**
 * Holds the template for date-only fields.
 *
 * The template is a string as used with the {@link strftime()} command.
 *
 * @global string $config['template']['date']
 * @name $config_template_date
 */
$config['template']['date'] = '%x';

/**
 * Holds the template for time-only fields.
 *
 * The template is a string as used with the {@link strftime()} command.
 *
 * @global string $config['template']['time']
 * @name $config_template_time
 */
$config['template']['time'] = '%X';

/**
 * Holds the template for datetime fields.
 *
 * The template is a string as used with the {@link strftime()} command.
 *
 * @global string $config['template']['datetime']
 * @name $config_template_datetime
 */
$config['template']['datetime'] = '%x %X';

/**
 * Holds the template for decimal fields.
 *
 * The template is a string as used with the {@link money_format()} command.
 *
 * @global string $config['template']['decimal']
 * @name $config_template_decimal
 */
$config['template']['decimal'] = '%^!.2n';

/**
 * Holds the locale string for german iso language code.
 *
 * For each language you plan to use there has to be a entry in the array with the key isolocale
 * where the iso code is set to the wanted locale code. Viewhelpers or other functions may use
 * these settings to represent raw data in an localized way.
 *
 * @global string $config['isolocale']['de']
 * @name $config_isolocale_de
 */
$config['isolocale']['de'] = 'de_DE.utf-8';

/**
 * Holds the iso code of the base currency.
 *
 * @global string $config['currency']['base']
 * @name $config_currency_base
 */
$config['currency']['base'] = 'eur';

/**
 * Holds the path to a xml file with currency exchange rates based on Euro (EUR).
 *
 * To make use of the currency exchange rates service you have to set allow_url_fopen=On (default).
 *
 * @global string $config['currency']['exchangerates']
 * @name $config_currency_exchangerates
 */
$config['currency']['exchangerates'] = 'http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';

/**
 * Holds the character to show as decimal point in germany.
 *
 * Set this for each language you are using.
 *
 * @global string $config['decimal']['de']['point']
 * @name $config_decimal_de_point
 */
$config['decimal']['de']['point'] = '.';

/**
 * Holds the character to show as thousands separator in germany.
 *
 * Set this for each language you are using.
 *
 * @global string $config['decimal']['de']['separator']
 * @name $config_decimal_de_separator
 */
$config['decimal']['de']['separator'] = ',';

/**
 * Holds the router offset value.
 *
 * The router will interpret a given URL starting from the offset defined here. If you have
 * installed Cinnebar into a subdirectory you must set offset = 1 so the directory itself will
 * be skipped when router interprets the URL. If you have installed into an even deeper directory
 * structure set offset to the number of directories to skip.
 * If you have installed Cinnebar directly into your web root, set offset to 0 (Zero).
 *
 * @see Cinnebar_Router::interpret()
 * @global int $config['router']['offset']
 * @name $config_router_offset
 */
$config['router']['offset'] = 1;

/**
 * Holds the default language code.
 *
 * As a first slice of the URL Cinnebar expects a language code. This is an convention of the
 * Cinnebar system. Here you have to define a language code to use if the URL misses a language
 * code. If you do not want to see a language code in your URL, i guess someone has to mess around
 * with the .htaccess file of your Cinnebar installation.
 *
 * Use iso code as language codes. Examples: de, en, us, it and so on.
 *
 * @see Cinnebar_Router::interpret()
 * @global string $config['router']['language']
 * @name $config_router_language
 */
$config['router']['language'] = 'de';

/**
 * Holds the default controller name.
 *
 * If the router can not determine a controller from the URL it will fall back to the
 * default controller {@link Controller_Welcome} or whatever controller you have configured.
 *
 * @see Cinnebar_Router::interpret()
 * @global string $config['router']['controller']
 * @name $config_router_controller
 */
$config['router']['controller'] = 'welcome';

/**
 * Holds the default method name.
 *
 * If the router can not determine a method name from the URL it will fall back to this
 * default method of the default controller {@link Controller_Welcome::index()} or
 * whatever method you have configured.
 *
 * @see Cinnebar_Router::interpret()
 * @global string $config['router']['method']
 * @name $config_router_method
 */
$config['router']['method'] = 'index';

/**
 * Holds the (optional) map to re-route controllers and methods.
 *
 * Usually a controller::method() naming has to match the URL cinnebar/en/controller/method
 * to be found when routed, but you can enforce a re-mapping if this configuration parameter
 * is setup with a qualified array for re-routing.
 *
 * Example of a re-mapping array:
 * <code>
 * <?php
 * $map = array(
 *     'controller' => array('hello' => 'welcome'),
 *     'method' => array('world' => 'index')
 * );
 * ?>
 * </code>
 *
 * @see Cinnebar_Router::reRoute()
 * @global array $config['router']['map']
 * @name $config_router_map
 */
$config['router']['map'] = array();

/**
 * Holds the default state of the cache.
 *
 * @see Cinnebar_Cache::isActive()
 * @global bool $config['cache']['active']
 * @name $config_cache_active
 */
$config['cache']['active'] = false;

/**
 * Holds the default time to live (ttl) of a cached file.
 *
 * @see Cinnebar_Cache::isActive()
 * @see Cinnebar_Cache::isCached()
 * @global int $config['cache']['ttl']
 * @name $config_cache_ttl
 */
$config['cache']['ttl'] = 300;

/**
 * Holds the (optional) listmanager email address.
 *
 * @global string $config['listmanager']['email']
 * @name $config_listmanager_email
 */
$config['listmanager']['email'] = 'listmanager@example.com';

/**
 * Holds the (optional) listmanager name.
 *
 * @global string $config['listmanager']['name']
 * @name $config_listmanager_name
 */
$config['listmanager']['name'] = 'Listmanager';

/**
 * Holds the smtp server state.
 *
 * If you want to use mail function with an smtp server you may set this to true and define the
 * following smtp server settings.
 *
 * @global bool $config['smtp']['active']
 * @name $config_smtp_active
 */
$config['smtp']['active'] = false;

/**
 * Holds the smtp host name.
 *
 * @global string $config['smtp']['host']
 * @name $config_smtp_host
 */
$config['smtp']['host'] = 'smtp.example.com';

/**
 * Holds the port of the smtp server to use.
 *
 * @global string $config['smtp']['host']
 * @name $config_smtp_host
 */
$config['smtp']['port'] = 25;

/**
 * Holds a username that has access to the smtp host.
 *
 * @global string $config['smtp']['user']
 * @name $config_smtp_user
 */
$config['smtp']['user'] = 'user@example.com';

/**
 * Holds the password of the smtp account.
 *
 * @global string $config['smtp']['pw']
 * @name $config_smtp_pw
 */
$config['smtp']['pw'] = '';

/**
 * Holds the database active state.
 *
 * If you want to use a database in your Cinnebar application you have to set this switch to true.
 * In case the database is not activated, your application may fail.
 *
 * @global bool $config['db']['active']
 * @name $config_db_active
 */
$config['db']['active'] = false;

/**
 * Holds the database driver name.
 *
 * As a driver name you may choose any which is supported by the database layer used.
 * As we use RedBean {@link http://redbeanphp.com/} this would currently be MySQL, Postgres,
 * SQLite, Oracle and CUBRID. More drivers may be implemented by the RedBean community or yourself.
 *
 * @global string $config['db']['driver']
 * @name $config_db_driver
 */
$config['db']['driver'] = 'mysql';

/**
 * Holds the database host name.
 *
 * @global string $config['db']['host']
 * @name $config_db_host
 */
$config['db']['host'] = 'localhost';

/**
 * Holds the name of the database to use.
 *
 * @global string $config['db']['database']
 * @name $config_db_database
 */
$config['db']['database'] = 'test';

/**
 * Holds a username that has access to the database.
 *
 * @global string $config['db']['username']
 * @name $config_db_username
 */
$config['db']['username'] = 'root';

/**
 * Holds the password of the database user account.
 *
 * @global string $config['db']['password']
 * @name $config_db_password
 */
$config['db']['password'] = '';

/**
 * Holds RedBeans "freeze" state.
 *
 * If freeze is set to true RedBean driver will use the database in "frozen" mode. No automatic
 * changes will be made to the database and things will get faster. Before you change to frozen
 * state, make sure your tables have all fields, indices and stuff right or otherwise things
 * might no happen as expected.
 *
 * @global bool $config['db']['freeze']
 * @name $config_db_freeze
 */
$config['db']['freeze'] = false;

/**
 * Holds the maximum filesize allowed for user uploads in bytes.
 *
 * @global int $config['upload']['maxfilesize']
 * @name $config_upload_maxfilesize
 */
$config['upload']['maxfilesize'] = 30000;
?>