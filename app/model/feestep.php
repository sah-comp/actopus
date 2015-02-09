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
 * Manages feestep.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Feestep extends Cinnebar_Model
{
    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        $this->addConverter('net', 'decimal');
        $this->addConverter('additional', 'decimal');
    }

    /**
     * update.
     */
    public function update()
    {
        if ( ! $this->bean->sequence) $this->bean->sequence = 0;
        parent::update();
    }
}
