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
 * Implements writing a log to the Apache error log.
 *
 * @package Cinnebar
 * @subpackage Writer
 * @version $Id$
 */
class Writer_Errorlog extends Cinnebar_Writer
{
    /**
     * Output the logs to apache (php) error_log
     *
     * @param array (optional) $logs
     * @return bool $writtenOrNotWritten
     */
    public function write(array $logs = array())
    {
        foreach ($logs as $section=>$lines) {
            foreach ($lines as $n=>$line)
            error_log(sprintf('%s: %s', $section, $line));
        }
        return true;
    }
}
