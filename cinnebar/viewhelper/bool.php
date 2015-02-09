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
 * The bool(ean) viewhelper class of the cinnebar system.
 *
 * @package Cinnebar
 * @subpackage Viewhelper
 * @version $Id$
 */
class Viewhelper_Bool extends Cinnebar_Viewhelper
{
    /**
     * Renders a bool(ean) value nicely.
     *
     * @param bool $value
     * @return string
     */
    public function execute($value)
    {
        if ($value) return __('bool_on');
        return __('bool_off');
    }
}
