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
//require_once BASEDIR.'/vendors/textile/classTextile.php';
require_once BASEDIR . '/vendors/Netcarver/Textile/DataBag.php';
require_once BASEDIR . '/vendors/Netcarver/Textile/Tag.php';
require_once BASEDIR . '/vendors/Netcarver/Textile/Parser.php';

/**
 * The textonly viewhelper class of the cinnebar system.
 *
 * @package Cinnebar
 * @subpackage Viewhelper
 * @version $Id$
 */
class Viewhelper_Textile extends Cinnebar_Viewhelper
{
    /**
     * Renders html/textile as textonly.
     *
     * @uses Textile
     * @param string (optional) $text
     * @param bool (optional) $restricted
     * @return string $htmlizedTextile
     */
    public function execute($text = '', $restricted = false)
    {
        if (empty($text)) {
            return '';
        }
        $parser = new \Netcarver\Textile\Parser();
        //if ( ! $restricted) return trim(strip_tags($textile->TextileThis($text)));
        //return trim(strip_tags($textile->TextileRestricted($text)));
        return $parser->parse($text);
    }
}
