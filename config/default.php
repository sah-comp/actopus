<?php
/**
 * My local development configuration
 *
 * Have a look at {@link config.example.php} to see configuration options with
 * full comments. This file is not intended to be documented anyway and i am too
 * lazy to repeat everything here.
 *
 * @author info@sah-company.com
 * @version Autumn 2012
 */

/**
 * Set default timezone, because php.ini failed on customer server.
 */
date_default_timezone_set('Europe/Berlin');

// app
$config['app'] = 'app';

// theme configuration
$config['theme'] = 'default';

// version
$config['version'] = 'Autumn 2012';

// user configuration
$config['user'] = array(
    'phpass' => false
);

// sessionhandler
$config['sessionhandler'] = 'database';//'apc';

// logger configuration
$config['logger'] = array(
    'active' => true,
    'writer' => 'file'
);

// templates
$config['template'] = array(
    'date' => '%x', // Preferred date representation based on locale, without the time
    'time' => '%X', // Preferred time representation based on locale, without the date
    'datetime' => '%x %X', // Preferred date and time
    'decimal' => '%^!.2n'
);

// language iso code to locale mapper
$config['isolocale'] = array(
    'de' => 'de_DE.utf-8',
    'en' => 'en_GB.utf-8',
    'us' => 'us_US.utf-8'
);

// currency settings
$config['currency'] = array(
    'base' => 'eur',
    'exchangerates' => 'http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml',
    //'exchangerates' => '/Library/WebServer/Documents/uploads/eurofxref-daily.xml'
);

// decimal point and separator character for each language used
$config['decimal'] = array(
    'de' => array('point' => ',', 'separator' => ''),
    'en' => array('point' => '.', 'separator' => ','),
    'us' => array('point' => '.', 'separator' => ',')
);

// map for possible re-routing
$map = array();

// router configuration
$config['router'] = array(
    'offset' => 1,
    'language' => 'de',
    'controller' => 'welcome',
    'method' => 'index',
    'map' => $map
);

// cache configuration
$config['cache'] = array(
    'active' => true,
    'ttl' => 300 // 5 mins.
);

// listmanager configuration
$config['listmanager'] = array(
    'email' => 'ich@7ich.de',
    'name' => '7ich'
);

// smtp configuration
$config['smtp'] = array(
    'active' => true,
    'host' => 'mail.7ich.de',
    'port' => 25,
    'user' => 'ich@7ich.de',
    'pw' => 'TUhuEJRSVqZj'
);

// database configuration
$config['db'] = array(
    'active' => true,
    'driver' => 'mysql', // or postgres, oracle, CUBRID, sqlite
    'host' => 'localhost',
    'database' => 's',
    'username' => 'eloide',
    'password' => 'elo58JiTs3',
    'freeze' => true
);

// user upload configuration
$config['upload'] = array(
    'maxfilesize' => 2097152, // a fat 2 MB upload limit for user upload
    'dir' => '/Library/WebServer/Documents/uploads/',
    'path' => '/uploads/'
);
