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
 * Manages regions.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Region extends Cinnebar_Model
{
    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        $this->sequence = 0;
    }
}
