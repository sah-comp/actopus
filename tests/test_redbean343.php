<?php
require_once '../vendors/simpletest/autorun.php';
//require_once '../vendors/redbeanPHP3_3/rb.php';
require_once '../vendors/RedBeanPHP3_4_3/rb.php';
require_once 'setup_test_database.php';

class TestOfRedbean343 extends UnitTestCase
{
    public function testBeanBasics()
    {
        $bean = R::dispense('bean');
        $bean->value = '1';
        try {
            R::store($bean);
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->assertTrue(false);
        }
    }
}
?>