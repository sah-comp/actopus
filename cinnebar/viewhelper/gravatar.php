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
 * The gravatar viewhelper class of the cinnebar system.
 *
 * @package Cinnebar
 * @subpackage Viewhelper
 * @version $Id$
 */
class Viewhelper_Gravatar extends Cinnebar_Viewhelper
{
    /**
     * Defines the gravatar image request URL with its placeholders.
     */
    const GRAVATAR_URL = 'http://www.gravatar.com/avatar/%s/?d=%s&amp;s=%d&amp;r=%s';

    /**
     * Renders an gravatar (globally recognized) for an given email address.
     *
     * @param string $email
     * @param int (optional) $size The size of the image
     * @param string (optional) $default One of [ 404 | mm | identicon | monsterid | wavatar ]
     * @param string (optional) $rating [ g | pg | r | x ]
     * @return string $URLToAGravatarImage
     */
    public function execute($email, $size = 80, $default = 'identicon', $rating = 'g')
    {
        return sprintf(self::GRAVATAR_URL, md5(mb_strtolower(trim($email))), urlencode($default), $size, $rating);        
    }
}