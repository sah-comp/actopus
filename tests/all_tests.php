<?php
if ( ! defined('BASEDIR')) define('BASEDIR', dirname(__FILE__).'/..');

require_once '../vendors/simpletest/autorun.php';

class AllTests extends TestSuite {
    function AllTests() {
        $this->TestSuite('All tests for Cinnebar');
        $this->addFile(dirname(__FILE__) . '/unit_tests.php');
    }
}
?>