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
 * Analyzes a URL and determines the controller, method and parameters to call.
 *
 * @todo Implement regular expression re-routing to be even more flexible
 *
 * @package Cinnebar
 * @subpackage Router
 * @version $Id$
 */
class Cinnebar_Router
{
    /**
     * Container for settings.
     *
     * @var array
     */
    public $settings;

    /**
     * Stores the scheme, either http or https.
     *
     * @var string
     */
    public $scheme = 'http';

    /**
     * Stores the host.
     *
     * @var string
     */
    public $host = 'localhost';

    /**
     * Stores the directory.
     *
     * @var string
     */
    public $directory = '';

    /**
     * Stores the sanatized URL.
     *
     * @var string
     */
    public $url;

    /**
     * Stores the internal URL.
     *
     * A internal URL is a relative URL without the language fragment.
     *
     * @var string
     */
    public $internal_url;

    /**
     * Stores the slices of our sanatized URL.
     *
     * @var array
     */
    public $slices = array();

    /**
     * Stores the parameters of the request.
     *
     * @var array
     */
    public $params = array();

    /**
     * Holds the language after interpretation of an URL.
     *
     * @var string
     */
    public $language = 'de';

    /**
     * Holds the controller after interpretation of an URL.
     *
     * @var string
     */
    public $controller = 'welcome';

    /**
     * Holds the method after interpretation of an URL.
     *
     * @var string
     */
    public $method = 'index';

    /**
     * Container for optional rerouting map.
     *
     * @var array
     */
    public $map = array();

    /**
     * Constructor.
     *
     * If there is a key named 'map' in the given settings, a re-routing map is set from
     * that settings parameter.
     *
     * @param array (optional) $settings
     */
    public function __construct(array $settings = array())
    {
        $this->settings = $settings;
        if (isset($this->settings['map']) && is_array($this->settings['map'])) {
            $this->map = $this->settings['map'];
        }
    }

    /**
     * Sets the (optional) rerouting map.
     *
     * @param array $map
     * @return bool $alwaysTrue
     */
    public function setMap(array $map = array())
    {
        $this->map = $map;
        return true;
    }

    /**
     * Interprets the given URL and sets internal attributes.
     *
     * A given URL like http://localhost/installdir/de/yourapp/help/param1/param2 is passed to
     * this method. It then analyzes the URL and sets all attributes like scheme, host and so on as
     * well as it sets the controller and method, both falling back to defaults defined in settings.
     *
     * The determined controller and methods names are (optionally) re-routed. This only happes if
     * there is a re-routing map setup, either in config/config.php or by using {@link setMap()}.
     *
     * Return values:
     * - Throws an exception if the URL given can not be parsed
     * - Returns true is interpretation was completed
     *
     * @todo get rid of the global language
     *
     * @uses Cinnebar_Router::$scheme
     * @uses Cinnebar_Router::$host
     * @uses Cinnebar_Router::$url
     * @uses Cinnebar_Router::$slices
     * @uses Cinnebar_Router::$internal_url
     * @uses Cinnebar_Router::$directory
     * @uses Cinnebar_Router::$params
     * @uses Cinnebar_Router::$controller
     * @uses Cinnebar_Router::$language
     * @uses Cinnebar_Router::$method
     * @uses Cinnebar_Router::$settings
     * @uses slice()
     * @uses reRoute() to check for re-mapping of controller and method
     * @param string $url
     * @return bool $interpreterRan
     * @throws Exception on failure to parse the url
     */
    public function interpret($url)
    {
        global $language;
        $parsed = parse_url($url);
        if (false === $parsed) {
            throw new Exception('Malicious URL '.$url);
        }
        $this->scheme = isset($parsed['scheme']) ? $parsed['scheme'] : '';
        $this->host = isset($parsed['host']) ? $parsed['host'] : '';
        $this->url = urldecode(trim(filter_var($parsed['path'], FILTER_SANITIZE_URL), '/'));
        $this->slices = explode('/', $this->url);
        $this->internal_url = implode('/', array_slice($this->slices, 1 + $this->settings['offset']));
        if ($this->settings['offset'] == 1) {
            $this->directory = $this->slice(0);
        }
        $this->language = $this->slice($this->settings['offset']);
        if ($this->language === null) {
            $this->language = $this->settings['language'];
        }
        $language = $this->language;
        $this->controller = $this->slice(1 + $this->settings['offset']);
        if ($this->controller === null) {
            $this->controller = $this->settings['controller'];
        }
        $this->method = $this->slice(2 + $this->settings['offset']);
        if ($this->method === null) {
            $this->method = $this->settings['method'];
        }
        $this->params = array_slice($this->slices, 3 + $this->settings['offset']);
        $this->reRoute();
        return true;
    }

    /**
     * Re-Routes controller and method if a re-routing map is set and returns wether it
     * re-routed something or not.
     *
     * @uses $map to lookup current controller for re-mapping
     * @uses $map to lookup current method for re-mapping
     * @uses $controller
     * @uses $method
     * @return bool $reRoutedOrNot
     */
    public function reRoute()
    {
        if (empty($this->map)) {
            return false;
        }
        $rerouted = false;
        if (isset($this->map['controller']) && isset($this->map['controller'][$this->controller])) {
            $this->controller = $this->map['controller'][$this->controller];
            $rerouted = true;
        }
        if (isset($this->map['method']) && isset($this->map['method'][$this->method])) {
            $this->method = $this->map['method'][$this->method];
            $rerouted = true;
        }
        return $rerouted;
    }

    /**
     * Returns the language name.
     *
     * @return string
     */
    public function language()
    {
        return $this->language;
    }

    /**
     * Returns the controller name.
     *
     * @return string
     */
    public function controller()
    {
        return $this->controller;
    }

    /**
     * Returns the methods name.
     *
     * @return string
     */
    public function method()
    {
        return $this->method;
    }

    /**
     * Sets the method.
     *
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * Returns the parameters array or an empty array.
     *
     * @return array
     */
    public function params()
    {
        return $this->params;
    }

    /**
     * Sets the params.
     *
     * @param array $params
     */
    public function setParams(array $params = array())
    {
        $this->params = $params;
    }

    /**
     * Returns the scheme.
     *
     * @return string
     */
    public function scheme()
    {
        return $this->scheme;
    }

    /**
     * Returns the basehref.
     *
     * @param bool (optional) $omit If set to true the protocol and host part are omitted
     * @return string
     */
    public function basehref($omit = false)
    {
        if (true === $omit) {
            return '/'.$this->directory.'/'.$this->language;
        }
        //return $this->scheme.'://'.$this->host().'/'.$this->directory().'/'.$this->language();
        return '//'.$this->host().'/'.$this->directory().'/'.$this->language();
    }

    /**
     * Returns the host.
     *
     * @return string
     */
    public function host()
    {
        return $this->host;
    }

    /**
     * Returns the directory.
     *
     * @return string
     */
    public function directory()
    {
        return $this->directory;
    }

    /**
     * Returns the internal URL, that is without absolute path and language part.
     *
     * @return string
     */
    public function internalUrl()
    {
        return $this->internal_url;
    }

    /**
     * returns a part of the url by its index number.
     *
     * @param int $index
     * @return mixed
     */
    protected function slice($index)
    {
        if (! isset($this->slices[$index])) {
            return null;
        }
        return $this->slices[$index];
    }
}
