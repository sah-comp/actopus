<?php
if ( ! defined('BASEDIR')) define('BASEDIR', dirname(__FILE__).'/..');

require_once '../vendors/simpletest/autorun.php';
require_once '../vendors/simpletest/unit_tester.php';

class UnitTests extends TestSuite {
    function UnitTests() {
        $this->TestSuite('Unit tests');
        $path = dirname(__FILE__);
        $this->addFile($path . '/test_general.php');
        $this->addFile($path . '/test_regex.php');
        $this->addFile($path . '/test_redbean343.php');
        $this->addFile($path . '/test_cinnebar_model.php');
    }
}
?>