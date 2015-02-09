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
 * Logs messages.
 *
 * You may use the default log or dedicated sections to log messages into. As the logger class
 * itself does not save logs persistent or send them out by email or anything else you will need an
 * instance of {@link Cinnebar_Writer} to write your logs.
 *
 * As this class is implemented as a singleton pattern use {@link Cinnebar_Logger::instance()}
 * to construct a logger instance.
 *
 * Usage:
 * <code>
 * <?php
 * $logger = Cinnebar_Logger::instance();
 * $logger->log('I discovered logging as a hobby');
 * $logger->log('If you dont log you are a hog', 'nonsense');
 * // ...
 * // ... later on
 * // ...
 * $logger->write(new Writer_File());
 * ?>
 * </code>
 *
 * @package Cinnebar
 * @subpackage Logger
 * @version $Id$
 */
class Cinnebar_Logger
{
    /**
     * Defines the default log.
     */
    const DEFAULT_LOG = 'general';
    
    /**
     * Holds the instance of a logger.
     *
     * @var Cinnebar_Logger
     */
    private static $instance;

    /**
     * Holds the logs and their messages.
     *
     * @var array
     */
    public $logs = array();
    
    /**
     * Returns an instance of logger.
     *
     * @return Cinnebar_Logger $logger the one and only instance of our logger
     */
    public static function instance()
    {
        if ( ! isset(self::$instance)) self::$instance = new Cinnebar_Logger();
        return self::$instance;
    }

    /**
     * Constructor.
     *
     * Use of constructor is prohibited from the outside.
     */
    private function __construct()
    {
    }
    
    /**
     * Clone.
     *
     * Cloning is only allowed from subclasses, but not from the outside.
     */
    protected function __clone()
    {
    }
    
    /**
     * Clears all log messages, regardless of section.
     *
     * @return void
     */
    public function clearAll()
    {
        $this->logs = array();
    }
    
    /**
     * Add a message to the log container.
     *
     * If the optional parameter is not given the message will be written to the general log.
     * Otherwise if it is given the message gets written to that section of log messages.
     *
     * @uses $logs
     * @param string $message
     * @param string (optional) $log
     */
    public function log($message, $log = self::DEFAULT_LOG)
    {
        $this->logs[$log][] = $message;
        return true;
    }
    
    /**
     * Transports the logged messages to their locations.
     *
     * @uses Cinnebar_Writer::write()
     * @param Cinnebar_Writer $writer A instance of a log writer
     * @return bool $wetherWrittenOrNot
     */
    public function write(Cinnebar_Writer $writer)
    {
        return $writer->write($this->logs);
    }
}
