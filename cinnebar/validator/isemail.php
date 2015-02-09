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
 * Validator to check if the value is a valid e-mail address.
 *
 * @package Cinnebar
 * @subpackage Validator
 * @version $Id$
 */
class Validator_Isemail extends Cinnebar_Validator
{
    /**
     * Returns wether the value is a valid email address or not.
     *
     * @param mixed $value
     * @return bool $validOrInvalid
     */
    public function execute($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}
