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
	 * Define error code for payment code out of range.
	 *
	 * We currently only handle EPO payment codes from 33 up to 50.
	 * Those codes for payments that handle renewal for the third up to the
	 * twenties year.
	 */
	const ERROR_PAYMENTCODE = 1;

	/**
	 * Define the error code for missing fee.
	 */
	const ERROR_FEE = 2;

	/**
	 * update.
	 */
	public function update()
	{
				$this->bean->errorcode = 0;
				if ( (int)$this->bean->paymentcode < 33 || (int)$this->bean->paymentcode > 50 ) {
						$this->bean->errorcode += Model_Multipayfee::ERROR_PAYMENTCODE;
				}
				if ( $this->bean->amount <= 0 ) {
						$this->bean->errorcode += Model_Multipayfee::ERROR_FEE;
				}
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
