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
 * The migrator model.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Migrator extends Cinnebar_Model
{    
    /**
     * Returns this bean as a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->bean->token;
    }
    
    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        $this->addValidator('token', 'hasvalue');
    }
}
