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
 * Converter to turn the value into a mysql date formatted value.
 *
 * @package Cinnebar
 * @subpackage Converter
 * @version $Id$
 */
class Converter_Mysqldate extends Cinnebar_Converter
{
    /**
     * Returns the value as a mysql date value.
     *
     * @param mixed $value
     * @return string $mySQLDateValue
     */
    public function execute($value)
    {
        if ( ! $value || empty($value) || $value == '0000-00-00') return '0000-00-00';
        return date('Y-m-d', strtotime($value));
    }
}
