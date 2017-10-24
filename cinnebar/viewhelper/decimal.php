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
 * The decimal viewhelper class of the cinnebar system.
 *
 * @package Cinnebar
 * @subpackage Viewhelper
 * @version $Id$
 */
class Viewhelper_Decimal extends Cinnebar_Viewhelper
{
    /**
     * Renders a decimal value nicely.
     *
     * @param mixed The value to format as a decimal
     * @param int Number of decimal places
     * @param mixed The iso code to define format or NULL
     * @return string
     */
    public function execute( $decimal, $decimals = 2, $iso = NULL )
    {
        global $config;
        if ( ! $decimal ) return '';
        if ( NULL === $iso ) $iso = $this->view()->language();
        //$decimal = (float)str_replace(',', '.', $decimal);
        return number_format( $decimal, $decimals, $config['decimal'][$iso]['point'], $config['decimal'][$iso]['separator'] );
    }
}
