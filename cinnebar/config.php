<?php
/**
 * Cinnebar.
 *
 * @package Cinnebar
 * @author $Author$
 * @version $Id$
 */
 
/**
 * Manages configuration.
 *
 * @package Cinnebar
 * @subpackage Configuration
 * @version $Id$
 */
class Cinnebar_Config
{
    /**
     * Container for the configuration.
     *
     * @var array
     */
    public $config = array();

    /**
     * Constructs a new Configuration Manager.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Returns a configuration value or null if not set.
     *
     * @param string $token name of the configuration setting to fetch
     * @return mixed
     */
    public function getSetting($token)
    {
        if ( ! isset($this->config[$token])) return null;
        return $this->config[$token];
    }
}
