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
 * Manages issue.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Issue extends Cinnebar_Model
{
    /**
     * Setup validators and set auto info to true.
     *
     * @uses $config to set listmanager email and name
     */
    public function dispense()
    {
        $this->addValidator('y', 'isnumeric');
        $this->addValidator('m', 'range', array('min' => 1, 'max' => 12));
        if ( ! $this->bean->getId()) {
            $this->bean->y = date('Y');
            $this->bean->m = date('m');
        }
    }
}
