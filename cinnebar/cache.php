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
 * The cache class of the cinnebar system.
 *
 * @package Cinnebar
 * @subpackage Cache
 * @version $Id$
 */
class Cinnebar_Cache
{
    /**
     * Holds the settings of the cache.
     *
     * @var array
     */
    public $settings = array();

    /**
     * Constructor.
     *
     * @uses $settings By default the cache is turned off. If turned on, pages stay for 5 minutes
     * @param array (optional) $settings
     */
    public function __construct(array $settings = array('active' => false, 'ttl' => 300))
    {
        $this->settings = $settings;
    }
    
    /**
     * Returns a md5 hash of the given string.
     *
     * @param string $url
     * @return string $hashedUrl
     */
    public function hashUrl($url)
    {
        return md5($url);
    }
    
    /**
     * Deletes all files that follow the patter page_*.html in the cache folder.
     *
     * @uses clear()
     * @return bool $alwaysTrue
     */
    public function clearAll()
    {
        return $this->clear('page_*.html');
    }
    
    /**
     * Deletes all files that match the pattern in the cache folder.
     *
     * @param string $pattern A regex pattern to match the files in cache folder
     * @return bool $alwaysTrue
     */
    public function clear($pattern)
    {
        return true;
    }
    
    /**
     * Returns wether the page caching is active or not.
     *
     * @uses settings
     * @return bool $onOrOffAndIfOnTTLisGreaterThanZero
     */
    public function isActive()
    {
        if ( ! isset($this->settings['active'])) return false;
        if ( ! $this->settings['active']) return false;
        if ( ! isset($this->settings['ttl'])) return false;
        if ($this->settings['ttl'] <= 0) return false;
        return true;
    }
    
    /**
     * Sets the cache to either active or inactive.
     *
     * @deprecated since the beginning (just to test the deprecated tag)
     * @see activate(),deactivate()
     * @param bool $switch
     */
    public function setActive($switch)
    {
        $this->settings['active'] = $switch;
    }
    
    /**
     * Turns the caching off.
     *
     * @uses $settings
     */
    public function deactivate()
    {
        $this->settings['active'] = false;
    }
    
    /**
     * Turns the caching on.
     *
     * @uses $settings
     */
    public function activate()
    {
        $this->settings['active'] = true;
    }

    /**
     * Returns either false or the full path to the cached file.
     *
     * If there is not cache file or it is outdated or the caching system is off then this
     * will return false. Otherwise a string with the full path to the cached file is returned.
     *
     * @uses isActive()
     * @uses filename()
     * @param string $url
     * @return mixed $falseOrStringWithFullPathToCachedFileOfThatUrl
     */
    public function isCached($url)
    {
        if ( ! $this->isActive()) return false;
        $file = $this->filename($url);
		if ( ! is_file($file)) return false;
		clearstatcache();
        if (filemtime($file) <= (time() - $this->settings['ttl'])) return false;
        return $file;
    }
    
    /**
     * Saves a string into the cache directory and returns wether that worked or not.
     *
     * @uses filename()
     * @param string $url
     * @param string $content
     * @return bool $savedOrNotSaved
     */
    public function savePage($url, $content)
    {
        $file = $this->filename($url);
		$handle = fopen($file, 'w');
		if ( ! $handle) return false;
		$ret = flock($handle, LOCK_EX);
		if ( ! $ret) return false;
		$ret = fwrite($handle, $content);
		if ( ! $ret) return false;
		$ret = flock($handle, LOCK_UN);
		if ( ! $ret) return false;
		$ret = fclose($handle);
		if ( ! $ret) return false;
		return true;
    }
    
    /**
     * Returns the complete filename (hashed).
     *
     * @uses hashUrl()
     * @param string $url
     * @return string $completePathToCachedFile
     */
    public function filename($url)
    {
        return BASEDIR.'/cache/page_'.$this->hashUrl($url).'.html';
    }
}
