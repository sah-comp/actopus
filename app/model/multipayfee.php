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
 * The multipayfee model class.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Multipayfee extends Cinnebar_Model
{
	/**
	 * update.
	 */
	public function update()
	{
        parent::update();
	}
	
	/**
	 * dispense.
	 */
	public function dispense()
	{
        $this->addConverter('amount', 'decimal');
        $this->addConverter('datedue', 'mySQLDate');
	}
}
