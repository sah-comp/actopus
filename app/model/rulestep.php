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
 * Manages rulestep.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Rulestep extends Cinnebar_Model
{
    /**
     * update.
     */
    public function update()
    {
        if ( ! $this->bean->sequence) $this->bean->sequence = 0;
        parent::update();
    }
}
