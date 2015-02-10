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
 * Validator Pregmatch.
 *
 * @package Cinnebar
 * @subpackage Validator
 * @version $Id$
 */
class Validator_Pregmatch extends Cinnebar_Validator
{
    /**
     * Returns wether the value is in the given range or not.
     *
     * @uses Cinnebar_Validator::$options
     * @param mixed $value
     * @return bool $validOrInvalid
     */
    public function execute($value)
    {
        if ( ! isset($this->options['regex'])) {
            throw new Exception('exception_validator_pregmatch_has_no_regex');
        }
        return preg_match($this->options['regex'], $value, $matches);
    }
}
