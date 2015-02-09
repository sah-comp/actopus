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
 * The humanmemorysize viewhelper class of the cinnebar system.
 *
 * @package Cinnebar
 * @subpackage Viewhelper
 * @version $Id$
 */
class Viewhelper_Humanmemorysize extends Cinnebar_Viewhelper
{
    /**
     * Strings that holds memory size indicators.
     *
     * @var string
     */
    public $sizes = 'BKMGTP';

    /**
     * Returns a human readable formatted byte value.
     *
     * @param int $bytes
     * @param int (optional) $decimals
     * @param string (optional) $lng
     * @return string
     */
     public function execute($bytes, $decimals = 2)
     {
         $factor = floor((strlen($bytes) - 1) / 3);
         return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$this->sizes[$factor];
     }
}
