<?php
require_once '../vendors/simpletest/autorun.php';
require_once '../vendors/RedBeanPHP3_4_3/rb.php';
require_once '../cinnebar/model.php';
require_once 'setup_test_database.php';

class Model_Bean extends Cinnebar_Model
{
    /* dummy class for bean */
}

class TestOfCinnebarModel extends UnitTestCase
{
    public $beanA, $beanB, $beanC;
    
    public function testParentChildren()
    {
        list($beanA, $beanB, $beanC) = R::dispense('bean', 3);
        $beanA->name = 'A';
        $beanB->name = 'B';
        $beanC->name = 'B';
        $beanB->bean = $beanA; // A is parent of B
        $beanC->bean = $beanA; // A is parent of C
        try {
            R::store($beanA);
            R::store($beanB);
            R::store($beanC);
            $this->assertTrue(true);
        } catch (Exception $e) {
            $this->assertTrue(false);
        }
        
        $reloadA = R::load('bean', $beanA->getId());
        $this->assertTrue($beanA->getId() == $reloadA->getId());
        
        $reloadB = R::load('bean', $beanB->getId());
        $this->assertTrue($beanB->getId() == $reloadB->getId());
        
        $this->assertTrue($reloadB->bean->name == 'A');
        $this->assertTrue($reloadB->parent()->name == 'A');
        
        $this->assertTrue(count($reloadA->children()) == 2);
        
    }
}
?>