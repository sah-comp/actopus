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
 * Validator to check if a value is neither null nor empty.
 *
 * @package Cinnebar
 * @subpackage Validator
 * @version $Id$
 */
class Validator_Hasvalue extends Cinnebar_Validator
{
    /**
     * Returns wether a value has a piece or information or not.
     *
     * @param mixed $value
     * @return bool $hasValueOrNot
     */
    public function execute($value)
    {
        if (null === $value) return false;
        if (empty($value)) return false;
        return true;
    }
}
