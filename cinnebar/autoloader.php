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
 * The autoloader class of the cinnebar system.
 *
 * @package Cinnebar
 * @subpackage Autoloader
 * @version $Id$
 */
class Cinnebar_Autoloader
{
    /**
     * Container for directories to scan for classes.
     *
     * By default the configured app directory is already on the list.
     *
     * @var array
     */
    public $dirs = array(
        APPDIR,
        'cinnebar'
    );

    /**
     * Constructor.
     */
    public function __construct()
    {
    }
    
    /**
     * Add a directory.
     *
     * @param string $dir
     */
    public function addDirectory($dir)
    {
        $this->dirs[] = $dir;
    }
    
    /**
     * Tries to load the requested class file and returns true or returns false if the class
     * file was not found.
     *
     * @param string $class
     * @return bool $wetherTheClassFileWasRequiredOrNot
     */
    public function load($class)
    {
        $path = strtr(strtolower($class), '_\\', '//');
        if ($path_to_file = $this->load_workhorse($path)) {
            require $path_to_file;
            return true;
        }
        return false;
    }
    
    /**
     * Really loads the first file to find scanning the given directories.
     *
     * @param string $path
     * @return void
     */
    public function load_workhorse($path)
    {
        foreach ($this->dirs as $dir) {
            $fullpath = $dir.'/'.$path.'.php';
            if (is_file($fullpath)) return $fullpath;
        }
        return false;
    }
    
    /**
     * Register our autoloader.
     *
     * @return bool
     */
    public function register()
    {
        return spl_autoload_register(array($this, 'load'));
    }
    
    /**
     * Unregister our autoloader.
     *
     * @return bool
     */
    public function unregister()
    {
        return spl_autoload_unregister(array($this, 'load'));    
    }
}
