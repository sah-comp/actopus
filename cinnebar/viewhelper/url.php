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
 * The url viewhelper class of the cinnebar system.
 *
 * @package Cinnebar
 * @subpackage Viewhelper
 * @version $Id$
 */
class Viewhelper_Url extends Cinnebar_Viewhelper
{
    /**
     * Renders a url.
     *
     * The optional parameter specifies wether to generate a normal href url or when it is
     * of value 'css' or 'js' it will return a url to the given resource.
     *
     * @uses Cinnebar_Router::basehref()
     * @todo Get rid of the /../ in the path because that throws a warning in the html validators
     *
     * @param string $url
     * @param string (optional) $type defaults to href and may be href, css or js
     * @return string
     */
    public function execute($url = '', $type = 'href')
    {
        if ($type == 'href') return $this->view()->basehref().$url;
        return '/themes/'.S_THEME.'/'.$type.'/'.$url.'.'.$type;
    }
}
