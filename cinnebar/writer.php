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
 * The basic writer class of the cinnebar system.
 *
 * To add your own writer simply add a php file to the writer directory of your Cinnebar
 * installation. Name the writer after the scheme Writer_* extend Cinnebar_Writer and
 * implement a write() method. You will not call a writer directly, but you will use it from
 * the {@link Cinnebar_Logger}. As an example see {@link Writer_File}.
 *
 * Example usage of the file writer to write a loggers log:
 * <code>
 * <?php
 * Cinnebar_Logger::write(new Writer_File());
 * ?>
 * </code>
 *
 * @package Cinnebar
 * @subpackage Writer
 * @version $Id$
 */
class Cinnebar_Writer
{   
    /**
     * Container for writer options.
     *
     * @var array
     */
    public $options = array();

    /**
     * Constructor.
     *
     * @param array (optional) $options
     */
    public function __construct(array $options = array())
    {
        $this->options = $options;
    }
    
    /**
     * Transports the log sections and entries of a logger instance to their locations.
     *
     * @param array (optional) $logs
     * @return bool $writtenOrNotWritten
     */
    public function write(array $logs = array())
    {
        return true;
    }
}
