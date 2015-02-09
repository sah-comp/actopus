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
 * The date viewhelper class of the cinnebar system.
 *
 * @package Cinnebar
 * @subpackage Viewhelper
 * @version $Id$
 */
class Viewhelper_date extends Cinnebar_Viewhelper
{
    /**
     * Renders a date according to the locale using the 'date' template.
     *
     * @uses Cinnebar_View::locale() to retrieve the locale for the given iso code
     * @uses Cinnebar_View::template() to retrieve the date template
     * @param string $date
     * @param string (optional) $template defaults to datetime or use date, time or whats set in config
     * @param string $iso language code from which the locale is determined
     * @return string $formattedDateString
     */
    public function execute($date, $template = 'date', $iso = null)
    {
        global $config;
        if ( ! $date || empty($date) || $date == '0000-00-00') return '';
        if (null === $iso) $iso = $this->view()->language();
        if (false === setlocale(LC_TIME, $config['isolocale'][$iso])) {
            Cinnebar_Logger::instance()->log('Could not set locale', 'warn');
        }
        return strftime($config['template'][$template], strtotime($date));
    }
}