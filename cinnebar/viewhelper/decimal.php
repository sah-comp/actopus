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
     * @param bool $decimal
     * @return string
     */
    public function execute($decimal, $decimals = 2, $iso = null)
    {
        global $config;
        if (null === $iso) $iso = $this->view()->language();
        //$decimal = (float)str_replace(',', '.', $decimal);
        return number_format($decimal, $decimals, $config['decimal'][$iso]['point'], $config['decimal'][$iso]['separator']);
    }
}
