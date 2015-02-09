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
 * Implements writing the log to a file in any writeable folder of the cinnebar folder.
 *
 * @package Cinnebar
 * @subpackage Writer
 * @version $Id$
 */
class Writer_File extends Cinnebar_Writer
{
    /**
     * Defines the additional string to attach to a logfile name.
     */
    const LOGFILE_EXTENSION = '_log';

    /**
     * Holds the (relative) path/to/logs of the directory to write to.
     *
     * @var string
     */
    public $folder = 'logs';
    
    /**
     * Sets the (relative) path/to/logs.
     *
     * @param string $path_to_logs
     */
    public function setFolder($path_to_logs)
    {
        $this->folder = $path_to_logs;
    }

    /**
     * Transports the log sections and entries of a logger instance to their locations.
     *
     * @param array (optional) $logs
     * @return bool $writtenOrNotWritten
     */
    public function write(array $logs = array())
    {
        foreach ($logs as $section=>$lines) {
            $file = BASEDIR.'/'.$this->folder.'/'.$section.self::LOGFILE_EXTENSION;
            if ( ! $handle = fopen($file, 'a')) return false;
            if ( ! fwrite($handle, implode("\n", $lines)."\n")) return false;
            fclose($handle);
        }
        return true;
    }
}
