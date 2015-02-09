<?php
error_log('Check if Cinnebar test database is already up...');
if ( ! defined('CINNEBAR_TEST_DB_SETUP')) {
    R::setup('mysql:host=localhost;dbname=stest', 'eloide', 'elo58JiTs3');
    R::nuke();
    define('CINNEBAR_TEST_DB_SETUP', true);
    error_log('... was not, but is now setup');
}
error_log('... it already was');
?>