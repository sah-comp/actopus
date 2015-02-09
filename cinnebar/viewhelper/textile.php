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
 * Require the textile library.
 */
require_once BASEDIR.'/vendors/textile/classTextile.php';

/**
 * The textile viewhelper class of the cinnebar system.
 *
 * @package Cinnebar
 * @subpackage Viewhelper
 * @version $Id$
 */
class Viewhelper_Textile extends Cinnebar_Viewhelper
{
    /**
     * Renders a string with Textile.
     *
     * If the optional parameter is set to true, Textile will render content as
     * untrusted input.
     *
     * @uses Textile
     * @param string (optional) $text
     * @param bool (optional) $restricted
     * @return string $htmlizedTextile
     */
    public function execute($text = '', $restricted = false)
    {
        if (empty($text)) return '';
        $textile = new Textile();
        if ( ! $restricted) return trim($textile->TextileThis($text));
        return trim($textile->TextileRestricted($text));
    }
}
