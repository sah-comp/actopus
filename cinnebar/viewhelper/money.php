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
 * The money viewhelper class of the cinnebar system.
 *
 * @package Cinnebar
 * @subpackage Viewhelper
 * @version $Id$
 */
class Viewhelper_Money extends Cinnebar_Viewhelper
{
    /**
     * Renders a money value nicely.
     *
     * @param bool $decimal
     * @return string
     */
    public function execute($decimal, $iso = null)
    {
        global $config;
        if (null === $iso) $iso = $this->view()->language();
        if (false === setlocale(LC_MONETARY, $config['isolocale'][$iso])) {
            Cinnebar_Logger::instance()->log('Viewhelper_Decimal: Could not set locale', 'warn');
        }
        return money_format($config['template']['decimal'], $decimal);
    }
}
