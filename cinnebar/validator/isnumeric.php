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
 * Validator to check if a value is numeric.
 *
 * @package Cinnebar
 * @subpackage Validator
 * @version $Id$
 */
class Validator_Isnumeric extends Cinnebar_Validator
{
    /**
     * Returns wether the value is numeric or not.
     *
     * @param mixed $value
     * @return bool $validOrInvalid
     */
    public function execute($value)
    {
        return (is_numeric($value));
    }
}
