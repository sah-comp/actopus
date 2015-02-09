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
 * The dateformatted viewhelper class of the cinnebar system.
 *
 * @package Cinnebar
 * @subpackage Viewhelper
 * @version $Id$
 */
class Viewhelper_timestamp extends Cinnebar_Viewhelper
{
    /**
     * Renders a date according to the locale using the 'date' template.
     *
     * @todo Add $template to signature, thus allowing custom templates
     *
     * @uses Cinnebar_View::locale() to retrieve the locale for the given iso code
     * @uses Cinnebar_View::template() to retrieve the date template
     * @param string $unixtimestamp
     * @param string (optional) $template defaults to datetime or use date, time or whats set in config
     * @param string $iso language code from which the locale is determined
     * @return string $formattedDateString
     */
    public function execute($unixtimestamp, $template = 'datetime', $iso = null)
    {
        global $config;
        if (null === $iso) $iso = $this->view()->language();
        if (false === setlocale(LC_TIME, $config['isolocale'][$iso])) {
            Cinnebar_Logger::instance()->log('Viewhelper_Timestamp: Could not set locale', 'warn');
        }
        if ( ! isset($config['template'][$template])) $template = 'datetime';
        return strftime($config['template'][$template], $unixtimestamp);
    }
}