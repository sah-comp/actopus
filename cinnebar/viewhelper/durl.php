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
 * The durl viewhelper class of the cinnebar system.
 *
 * @todo get rid of this and update Viewhelper_Url instead
 *
 * @package Cinnebar
 * @subpackage Viewhelper
 * @version $Id$
 */
class Viewhelper_Durl extends Cinnebar_Viewhelper
{
    /**
     * Renders a download link.
     *
     * @uses Cinnebar_Router::basehref()
     *
     * @param string $url
     * @param string (optional) $type defaults to href and may be href, css or js
     * @return string
     */
    public function execute($file = '')
    {
        global $config;
        return $config['upload']['path'].$file;
    }
}
