<?php 
/**
 * LICENSE
 *
 * TAKE IT AS IT IS AND SEND A POSTCARD TO ME AT INFO@SAH-COMPANY.COM. PORTIONS OF THIS SOFTWARE
 * IS OPEN SOURCE AND THIS SOFTWARE AS ITSELF WILL HAVE THE SAME RESTRICTIVE OR NON-RESTRICTIVE
 * LICENSE AS ONE OF ITS PARTS. 
 */


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
  * Interface for language model.
  *
  * @package Cinnebar
  * @subpackage Model
  * @version $Id$
  */
interface iLanguage
{
    /**
     * Returns all enabled languages.
     *
     * @return array of language beans
     */
    public function enabled();
}

/**
 * Interface for token model.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
interface iToken
{
    /**
     * Returns a translation of the token for the given language.
     *
     * @param string $iso code of the wanted translation language
     * @return RedBean_OODBBean $translation
     */
    public function in($iso = 'de');
}

/**
 * Interface for permission.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
interface iPermission
{

    /**
     * Returns wether user is allowed to do action on domain or not.
     *
     * @param mixed $user
     * @param string $domain
     * @param string $action
     * @return bool
     */
    public function allowed($user = null, $domain, $action);

    /**
     * returns an key/value array of all domains where user can do action.
     *
     * @param mixed $user
     * @param string $action
     * @return array
     */
    public function domains($user, $action);

    /**
     * Loads the users permissions and caches them in users session.
     *
     * @param mixed $user
     */
    public function load($user = null);
}

/**
 * Interface for modules.
 *
 * A module has to render a slice bean in either backend or frontend mode.
 *
 * @package Cinnebar
 * @subpackage Module
 * @version $Id$
 */
interface iModule
{
    /**
     * Renders a slice bean in frontend mode.
     */
    public function renderFrontend();

    /**
     * Renders a slice bean in backend mode.
     */
    public function renderBackend();
}


/**
 * Global functions to be used around the whole application.
 *
 * @package Cinnebar
 * @author $Author$
 * @version $Id$
 */

/**
 * Returns a translated string or the given token.
 *
 * @todo get rid of the global language or make that a fashion and cool down!
 *
 * @param string $text
 * @param mixed (optional) replacement values
 * @param string $lng iso-code of the language to use for translation
 * @param string (optional) $mode defines the mode that is used to render the token, e.g. textile
 * @param string (optional) $desc may describe the token, e.g. to help the translation team
 * @return string
 */
function __($text, $replacements = null, $lng = null, $mode = null, $desc = null)
{
    global $language;
    if (empty($lng)) $lng = $language;
    if ( ! $token = R::findOne('token', ' name = ? LIMIT 1', array($text))) {
        $token = R::dispense('token')->setAttr('name', $text);
        $token->mode = $mode;
        R::store($token);
    }
    if ( $replacements !== null) {
        if ( ! is_array($replacements)) $replacements = array($replacements);
        return vsprintf($token->in($lng)->payload, $replacements);
    }
    return $token->in($lng)->payload;
}

/**
 * Returns the given object for easier chaining.
 *
 * You can use this to directly chain method calls to an object on instantiation. In PHP < 5.4 you can
 * not do new Foo()->bar(), but you can use with(new Foo)->bar() as an escape.
 *
 * @param mixed $object
 * @return mixed $object
 */
function with($object)
{
    return $object;
}


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
 * Class factory.
 *
 * Usage:
 * <code>
 * 
 * $menu = Cinnebar_Factory::make('menu'); // gives us a new Cinnebar_Menu instance
 * 
 * </code>
 *
 * @package Cinnebar
 * @subpackage Factory
 * @version $Id$
 */
class Cinnebar_Factory
{
    /**
     * Constructor.
     */
    public function __construct()
    {
    }
    
    /**
     * Returns a new instance of a class.
     *
     * @param string $class to instantiate
     * @param string $prefix of the class to instantiate, defaults to 'Cinnebar'
     * @return mixed
     * @throws 
     */
    public static function make($class, $prefix = 'Cinnebar')
    {
        $class_name = ucfirst(strtolower($prefix)).'_'.ucfirst(strtolower($class));
        if (class_exists($class_name)) return new $class_name();
        throw new Exception(sprint(__('Unable to make a new "%s"-class.'), $class_name));
    }
}


/**
 * Cinnebar.
 *
 * @package Cinnebar
 * @author $Author$
 * @version $Id$
 */
 
/**
 * Facade gives you easy access to several stuff.
 *
 * @package Cinnebar
 * @subpackage Facade
 * @version $Id$
 */
class Cinnebar_Facade extends Cinnebar_Element
{
    /**
     * Holds the release version tag
     */
    const RELEASE = '1.05';

    /**
     * Holds an instance of a cycle bean.
     *
     * @var RedBean_OODBBean
     */
    private $cycle;
    
    /**
     * Returns true if this was called from the command line.
     *
     * @todo Implement more checks for non unix servers
     *
     * @return bool
     */
    public function cli()
    {
        return (php_sapi_name() == 'cli');
    }
    
    /**
     * Decides wether to run a cli command or http controller.
     *
     * @return bool
     */
    public function run()
    {
        if ($this->cli()) return $this->run_cli();
        return $this->run_http();
    }
    
    
    /**
     * Run a command(-controller) from the command line.
     *
     * @return bool
     */
    protected function run_cli()
    {
        global $language;
        $language = 'en';
        $options = getopt('c:');
        if ( ! $options) {
            echo 'No parameters are given. Please use at least -c [command]';
            echo "\n";
            echo 'Example: php -f index.php -- -c welcome';
            echo "\n";
            return true;
        }
        $command_name = 'Command_'.ucfirst(strtolower($options['c']));
        if (class_exists($command_name, true)) {
            $command = new $command_name();
        } else {
            $command = new Cinnebar_Command();
        }
        $command->parse($_SERVER['argv']);
        $result = call_user_func_array(
            array(
                $command,
                'execute'
            ),
            array()
        );
        return true;
    }

    /**
     * Run a Cinnebar http request/response cycle.
     *
     * If no controller class was found an 404 error page will be shown instead.
     *
     * @uses Cinnebar_Router::interpret()
     * @uses Cinnebar_Request::url()
     */
    public function run_http()
    {
        if ($cached_file = $this->deps['cache']->isCached($this->deps['request']->url())) {
            include $cached_file;
            exit;
        }
        $this->deps['router']->interpret($this->deps['request']->url());
        
        $controller_name = 'Controller_'.ucfirst(strtolower($this->deps['router']->controller()));
        if (class_exists($controller_name, true)) {
            $controller = new $controller_name();
        } else {
            $controller = new Cinnebar_Controller();
            $this->deps['router']->setMethod('error');
            $this->deps['router']->setParams(array('404'));
        }
        $controller->di(array(
            'request' => $this->deps['request'],
            'response' => $this->deps['response'],
            'router' => $this->deps['router'],
            'cache' => $this->deps['cache'],
            'input' => $this->deps['input'],
            'permission' => $this->deps['permission']
        ));
        
        $this->deps['response']->start();
        // call app/controller/[name]
        try {
            $result = call_user_func_array(
        	    array(
        	        $controller,
        	        $this->deps['router']->method()
        	    ),
        	    $this->deps['router']->params()
        	);
        } catch (Exception $e) {
            Cinnebar_Logger::instance()->log(date('Y-m-d H:i:s: ').$e, 'exceptions');
            $this->deps['cache']->deactivate();
            echo 'An exceptional error has occured, please review logs.';
        }
    	// add some replacement tokens
    	$this->deps['response']->addReplacement('remote_addr', $_SERVER['REMOTE_ADDR']);
    	$this->deps['response']->addReplacement('memory_usage', 
    	                                round(memory_get_peak_usage(true)/1048576, 2));
    	$this->deps['response']->addReplacement('execution_time', 
    	                                $this->deps['stopwatch']->mark('stop')->laptime('start', 'stop'));
    	// output response to client
        echo $payload = $this->deps['response']->flush();
    	if ($this->deps['cache']->isActive()) {
    	    $this->deps['cache']->savePage($this->deps['request']->url(), $payload);
    	}
    }
    
    /**
     * Performs stuff after a script ends or is exited.
     *
     * You have to use {@link register_shutdown_function} in your bootstrap php file {@link bootstrap.php}
     * or otherwise this will not get called.
     *
     * @param array $config
     * @return void
     */
    public function stop(array $config = array())
    {
        if (isset($config['logger']['active']) && $config['logger']['active']) {
            $writer_name = 'Writer_'.ucfirst(strtolower($config['logger']['writer']));
            Cinnebar_Logger::instance()->write(new $writer_name);
        }
    }
}


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
 * 
 * $logger = Cinnebar_Logger::instance();
 * $logger->log('I discovered logging as a hobby');
 * $logger->log('If you dont log you are a hog', 'nonsense');
 * // ...
 * // ... later on
 * // ...
 * $logger->write(new Writer_File());
 * 
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
 * 
 * Cinnebar_Logger::write(new Writer_File());
 * 
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
 * Calculates laptimes between benchmarks using microtime.
 *
 * @package Cinnebar
 * @subpackage Stopwatch
 * @version $Id$
 */
class Cinnebar_Stopwatch
{
    /**
     * Holds the marks of our stopwatch.
     *
     * @var array
     */
    public $marks = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
    }
    
    /**
     * Returns the value of a mark.
     *
     * @return string
     */
    public function __get($mark)
    {
        if ( ! isset($this->marks[$mark])) return null;
        return $this->marks[$mark];
    }
    
    /**
     * Sets the start mark.
     *
     * @return Cinnebar_Stopwatch $this
     */
    public function start()
    {
        $this->marks['start'] = microtime(true);
        return $this;
    }
    
    /**
     * Set a benchmark.
     *
     * @param string $mark
     * @return Cinnebar_Stopwatch $this
     */
    public function mark($mark)
    {
        $this->marks[$mark] = microtime(true);
        return $this;
    }
    
    /**
     * Calculates the time between two marks and returns the result.
     *
     * @param mixed $mark1
     * @param mixed $mark2
     * @param int $digits
     * @return float $diffBetweenMark1AndMark2
     */
    public function laptime($mark1 = 'start', $mark2 = 'stop', $digits = 5)
    {
        if ( ! isset($this->marks[$mark1]) || ! isset($this->marks[$mark2])) return 0.0000;
        return round($this->marks[$mark2] - $this->marks[$mark1], $digits);
    }
}


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
 * Manages a http request.
 *
 * @package Cinnebar
 * @subpackage Request
 * @version $Id$
 */
class Cinnebar_Request
{
    /**
     * String for HTTP protocol.
     *
     * @var string
     */
    const PROTOCOL_HTTP = 'http://';
    
    /**
     * String for HTTPS protocol.
     *
     * @var string
     */
    const PROTOCOL_HTTPS = 'https://';

    /**
     * Constructor.
     */
    public function __construct()
    {
    }
    
    /**
     * Returns the protocol of the request.
     *
     * @return string $protocol
     */
    public function protocol()
    {
        if ( ! isset($_SERVER['HTTPS']) || ! $_SERVER['HTTPS']) return self::PROTOCOL_HTTP;
        return self::PROTOCOL_HTTPS;
    }
    
    /**
     * Returns the host.
     *
     * @return string $host
     */
    public function host()
    {
        return $_SERVER['HTTP_HOST'];
    }
    
    /**
     * Returns a string with port if port differs from 80.
     *
     * @return string $port
     */
    public function port()
    {
        return '';
    }
    
	/**
	 * Returns the clients request type, either post or get.
	 *
	 * @return string $getOrPost
	 */
	public function getOrPost()
	{
		if (count($_POST) == 0) return 'get';
		return 'post';
	}
	
	/**
	 * Returns the full URL of the request.
	 *
	 * @return string $url
	 */
	public function url()
	{
        return $this->protocol().$this->host().$_SERVER['REQUEST_URI'];
	}
    
	/**
	 * Returns true if the clients request was an ajax call.
	 *
	 * Checks the SERVER variable to see if this was an ajax initiated request.
	 * Your controller can then decide wether to output a complete HTML page or
	 * only a certain partial view.
	 *
	 * @return bool $isAjaxOrNormalHTTPRequest
	 */
	public function isAjax() {
	    return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
	}
}


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
 * Manages a http response.
 *
 * @package Cinnebar
 * @subpackage Response
 * @version $Id$
 */
class Cinnebar_Response
{
    /**
     * Holds the headers for this response.
     *
     * @var array
     */
    public $headers = array();

    /**
     * Holds the replacements for this response.
     *
     * @var array
     */
    public $replacements = array();
    
    /**
     * Holds the response payload.
     *
     * @var string
     */
    public $payload = '';

    /**
     * Constructor.
     */
    public function __construct()
    {
    }
    
    /**
     * Starts an output buffer.
     *
     */
    public function start()
    {
		ob_start();
    }
    
    /**
     * Send all headers and return the payload as a string.
     *
     * @uses Cinnebar_Response::$payload
     * @uses replacements() to replace eventually tokens with values
     * @uses headers() to send headers
     * @return string $payload
     */
    public function flush()
    {
        $this->payload = ob_get_contents();
        $this->replacements();
		ob_end_clean();
		$this->headers();
        return $this->payload;
    }
    
    /**
     * Add a header to this response.
     *
     * Usage from a controller may look like this:
     * <code>
     * 
     * // ...
     * // ... your code within a controller method
     * // ...
     * $this->response->addHeader('X-CINNEBAR-GREETZ', 'Hello World');
     * // ...
     * // ...
     * 
     * </code>
     *
     * @uses Cinnebar_Response::$headers
     * @param string $header
     * @param string $content
     * @return bool $added
     */
    public function addHeader($header, $content)
    {
        $this->headers[$header] = $content;
        return true;
    }
    
    /**
     * Add a replacement token/value for this response.
     *
     * Replacements are strings in a template or payload of the response which are surrounded
     * by double curly brackets like this for example: {{memory_usage}}.
     *
     * The following replacement tokens are preset by {@link Cinnebar_Facade::run()}:
     * - memory_usage
     * - remote_addr
     * - execution_time
     *
     * Usage from a controller may look like this:
     * <code>
     * 
     * // ...
     * // ... your code within a controller method
     * // ...
     * $this->response->addReplacement('memory_usage', '320 MB');
     * // ...
     * // ...
     * 
     * </code>
     *
     * @uses Cinnebar_Response::$replacements
     * @param string $token
     * @param string $value
     * @return bool $added
     */
    public function addReplacement($token, $value)
    {
        $this->replacements[$token] = $value;
        return true;
    }
    
	/**
	 * send headers to the client.
	 *
     * @uses Cinnebar_Response::$headers
	 */
	public function headers()
	{
		foreach ($this->headers as $header=>$value)
		{
			header("{$header}: {$value}");
		}
	}
	
	/**
	 * Replaces all tokens like {{yourtoken}} in payload.
	 *
     * @uses Cinnebar_Response::$replacements
     * @uses Cinnebar_Response::$payload
	 * @return bool $replaced
	 */
	protected function replacements()
	{
	    if ( empty($this->replacements)) return false;
		foreach ($this->replacements as $key=>$value) {
			$needle = '{{'.$key.'}}';
			$this->payload = str_replace($needle, $value, $this->payload);
		}
		return true;
	}
}


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
		if ( false === $parsed) throw new Exception('Malicious URL '.$url);
		$this->scheme = isset($parsed['scheme']) ? $parsed['scheme'] : '';
		$this->host = isset($parsed['host']) ? $parsed['host'] : '';
		$this->url = urldecode(trim(filter_var($parsed['path'], FILTER_SANITIZE_URL), '/'));
		$this->slices = explode('/', $this->url);
		$this->internal_url = implode('/', array_slice($this->slices, 1 + $this->settings['offset']));
		if ($this->settings['offset'] == 1) {
		    $this->directory = $this->slice(0);
		}
		$this->language = $this->slice($this->settings['offset']);
		if ($this->language === null) $this->language = $this->settings['language'];
		$language = $this->language;
		$this->controller = $this->slice(1 + $this->settings['offset']);
		if ($this->controller === null) $this->controller = $this->settings['controller'];
		$this->method = $this->slice(2 + $this->settings['offset']);
		if ($this->method === null) $this->method = $this->settings['method'];
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
        if (empty($this->map)) return false;
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
        if (true === $omit) return '/'.$this->directory.'/'.$this->language;
        return $this->scheme.'://'.$this->host().'/'.$this->directory().'/'.$this->language();
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
		if ( ! isset($this->slices[$index])) return null;
		return $this->slices[$index];
	}
}


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
 * A basic controller.
 *
 * To add your own controller simply add a php file to the controller directory of your Cinnebar
 * installation. Name the controller after the scheme Controller_* extends Cinnebar_Controller and
 * implement methods as you wish. You will not call a controller directly, instead it is called
 * from the {@link Cinnebar_Facade} while a request/response cycle runs.
 *
 * Example controller may look like this:
 * <code>
 * 
 * class Controller_Example extends Cinnebar_Controller
 * {
 *     public function helloworld() {
 *         // ...
 *         // ... your code for helloworld
 *         // ...
 *     }
 *
 *     public function thankyou($code) {
 *         // ...
 *         // ... your code for thankyou
 *         // ...
 *     }
 * }
 * 
 * </code>
 *
 * Look at {@link Controller_Welcome} as an example.
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Cinnebar_Controller extends Cinnebar_Element
{
    /**
     * Holds an instance of the current user or null.
     *
     * @var mixed
     */
    public $user = null;

    /**
     * Constructs a new controller instance.
     */
    public function __construct()
    {
    }
    
    /**
     * Runs a plugin from the plugin directory.
     *
     * @param string $method
     * @param array (optional) $params
     * @return mixed
     */
    public function __call($method, array $params = array())
    {
        $plugin_name = 'Plugin_'.ucfirst(strtolower($method));
        if ( ! class_exists($plugin_name, true)) {
            Cinnebar_Logger::instance()->log(sprintf('Plugin "%s" not found', $method), 'warn');
            exit(sprintf('Plugin "%s" not found', $method));
        }
        $plugin = new $plugin_name($this);
        return call_user_func_array(array($plugin, 'execute'), $params);
    }
    
    /**
     * Returns wether there is a valid user or not.
     *
     * <b>Attention</b>: A session must have been started before calling this method.
     *
     * @return bool $IdOfTheCurrentUserOrZero
     */
    public function auth()
    {
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > MAX_SESS_LIFETIME)) {
            session_unset();
            session_destroy();
            return false;
        }
        $_SESSION['last_activity'] = time();
        $this->user = R::dispense('user')->current();
        return $this->user->getId();
    }
    
    /**
     * Instantiates a new Cinnebar_View object and returns it.
     *
     * @param string $template path/to/template which you want to use
     * @return Cinnebar_View
     */
    public function makeView($template)
    {
        $view = new Cinnebar_View($template);
        $view->controller($this);
        return $view;
    }
    
    /**
     * Redirects to the given URL.
     *
     * When redirecting to the given URL all other location headers become obsolete.
     * If the third optional parameter is set to true the URL given will not be
     * preceeded with the basehref. That way you can redirect to external URLs.
     *
     * @uses Cinnebar_Router::basehref()
     * @param string $url
     * @param int (optional) $http_response_code
     * @param bool $raw
     * @return void
     */
    public function redirect($url, $http_response_code = 302, $raw = false)
    {
        if ( ! $raw) $url = $this->router()->basehref().$url;
        header("Expires: 0"); 
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
        header("Cache-Control: private", false); // required for certain browsers
        header('Location: '.$url, true, $http_response_code);
        exit;
    }
    
    /**
     * Returns the request instance.
     *
     * @return Cinnebar_Request
     */
    public function request()
    {
        return $this->deps['request'];
    }
    
    /**
     * Returns the response instance.
     *
     * @return Cinnebar_Response
     */
    public function response()
    {
        return $this->deps['response'];
    }
    
    /**
     * Returns the router instance.
     *
     * @return Cinnebar_Router
     */
    public function router()
    {
        return $this->deps['router'];
    }
    
    /**
     * Returns the input instance.
     *
     * @return Cinnebar_Input
     */
    public function input()
    {
        return $this->deps['input'];
    }
    
    /**
     * Returns the cache instance.
     *
     * @return Cinnebar_Cache
     */
    public function cache()
    {
        return $this->deps['cache'];
    }
    
    /**
     * Returns the permission instance.
     *
     * @return Cinnebar_Permission
     */
    public function permission()
    {
        return $this->deps['permission'];
    }
    
    /**
     * Returns a user bean.
     *
     * If no session is active or the user is not yet logged in, it will return an empty bean.
     * Otherwise it will return the current user bean.
     *
     * @return RedBean_OODBBean $user
     */
    public function user()
    {
        if ( ! $this->user || ! is_a($this->user, 'RedBean_OODBBean')) return R::dispense('user');
        return $this->user;
    }

    /**
     * Default method.
     *
     * @return void
     */
    public function index()
    {
        echo 'It works?! Start writing your own controller, now.';
    }
}


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
 * Handles CURD operations on a certain bean type.
 *
 * As the Death Star Cantina man says: "you'll still need a tray." please do not forget to have the model
 * templates for the scaffold bean type in place. I went that way because scaffolding is nice, but later
 * on i have to setup individual partials for customizing needs anyway.
 *
 * @todo Evolve a plugin from this controller so that any controller may benefit
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_Scaffold extends Cinnebar_Controller
{
    /**
     * Default limit value.
     */
    const LIMIT = 23;
    
    /**
     * Default layout for index.
     */
    const LAYOUT = 'table';

    /**
     * Holds the bean type to apply scaffolding to.
     *
     * @var string
     */
    public $type = 'token';
    
    /**
     * Holds the alias for a bean type to apply pageflip to.
     *
     * @var string
     */
    public $typeAlias = null;
    
    /**
     * Holds the current action.
     *
     * @var string
     */
    public $action;
    
    /**
     * Holds the path to scaffold templates.
     *
     * @var string
     */
    public $path = 'shared/scaffold/';
    
    /**
     * Holds an instance of a Cinnebar_View
     *
     * @var Cinnebar_View
     */
    public $view;
    
    /**
     * Holds the current page number.
     *
     * @var int
     */
    public $page;
    
    /**
     * Holds the limit of beans to fetch at once.
     *
     * @var int
     */
    public $limit;
    
    /**
     * Holds the layout to use.
     *
     * @var string
     */
    public $layout;
    
    /**
     * Holds the key of the orderclause to use for sorting.
     *
     * @var int
     */
    public $order;
    
    /**
     * Holds the dir(ection) key for sorting.
     *
     * @var int
     */
    public $dir;
    
    /**
     * Container for sort directions.
     *
     * @var array
     */
    public $sortdirs = array(
        'ASC',
        'DESC'
    );
    
    /**
     * Container for actions.
     *
     * @var array
     */
    public $actions = array(
        'table' => array('expunge'),
        'edit' => array('next', 'prev', 'update', 'list'),
        'add' => array('continue', 'update', 'list')
    );
    
    /**
     * Set environmental parameters like page, limit and so on.
     *
     * A record (maybe empty) of a certain type is loaded and the environment is set according
     * to the given parameters.
     *
     * @uses Cinnebar_Model::makeMenu() to create a menu based on the current action
     * @uses Cinnebar_Model::makeActions() to create possible scaffold actions
     * @uses $actions which holds the preset scaffolding actions
     * @param int $page
     * @param int $limit
     * @param string $layout
     * @param int $order
     * @param int (optional) $id of a certain bean to load
     * @param string $action
     */
    protected function env($page, $limit, $layout, $order, $dir, $id = null, $action = 'index')
    {
        $this->view = $this->makeView($this->path.$action);
        $this->view->title = __('scaffold_head_title_'.$action);
        
        $this->view->user = $this->user();
        $this->view->action = $this->action = $action;
        $this->view->record = $this->record($id);
        
        $this->view->layout = $this->layout = $layout;
        
        $this->view->filter = $this->make_filter();
        
        $this->view->page = $this->page = $page;
        $this->view->limit = $this->limit = $limit;
        
        $this->view->attributes = $this->view->record->attributes($this->view->layout);
        $this->view->colspan = count($this->view->attributes) + 1;
        $this->view->order = $this->order = $order;
        $this->view->sortdirs = $this->sortdirs;
        $this->view->orderclass = strtolower($this->sortdirs[$dir]);
        $this->view->dir = $this->dir = $dir;
        $this->view->id = $this->id = $id;
        $this->view->actions = $this->view->record->makeActions($this->actions);
        //$this->view->action = $this->action = $action;
        $this->view->followup = null;
        if (isset($_SESSION['scaffold'][$this->view->action]['followup'])) {
            $this->view->followup = $_SESSION['scaffold'][$this->view->action]['followup'];
        }
        // Last, but not least we create a menu
        $this->view->nav = R::findOne('domain', ' blessed = ? LIMIT 1', array(1))->hierMenu($this->view->url());
        $this->view->navfunc = $this->view->record->makeMenu($action, $this->view, $this->view->nav);
        
        $this->view->urhere = with(new Cinnebar_Menu())->add(__($this->type.'_head_title'), $this->view->url(sprintf('/%s/index/%d/%d/%s/%d/%d', $this->type, 1, self::LIMIT, $this->view->layout, $this->view->order, $this->view->dir)));
    }
    
    /**
     * Generates an instance of filter.
     *
     * @return Model_Filter
     */
    protected function make_filter()
    {
        if ( ! isset($_SESSION['filter'][$this->type]['id'])) {
            $_SESSION['filter'][$this->type]['id'] = 0;
        }
        $filter = R::load('filter', $_SESSION['filter'][$this->type]['id']);
        if ( ! $filter->getId()) {
            $filter->rowsperpage = self::LIMIT;
            $filter->model = $this->type; // always re-inforce the bean type
            $filter->user = $this->view->user; // owner
            $filter->logic = 'AND';
            $filter->name = __('filter_auto_title', array(__('domain_' . $this->type)));
            try {
                R::store($filter);
                $_SESSION['filter'][$this->type]['id'] = $filter->getId();
            } catch (Exception $e) {
                Cinnebar_Logger::instance()->log('Filter: '.$e, 'exceptions');
            }
        }
        return $filter;
    }
    
    /**
     * Returns an bean of the scaffolded type.
     *
     * The returned bean may be empty in case the optional parameter was not given.
     *
     * @uses $type
     * @param int (optional) $id
     * @return RedBean_OODBBean
     */
    protected function record($id = null)
    {
        return R::load($this->type, $id);
    }
    
    /**
     * Returns an array of beans.
     *
     * @uses $type
     * @uses Cinnebar_Filter to build where clause and get filter values
     * @uses Cinnebar_Model to get SQL for filters
     * @return array
     */
    protected function collection()
    {
    	$whereClause = $this->view->filter->buildWhereClause();
		$orderClause = $this->view->attributes[$this->order]['orderclause'].' '.$this->sortdir($this->dir);
		$sql = $this->view->record->sqlForFilters($whereClause, $orderClause, $this->offset($this->page, $this->limit), $this->limit);
		
		$this->view->total = 0;
		
		try {
			//R::debug(true);
			$assoc = R::$adapter->getAssoc($sql, $this->view->filter->filterValues());
			//R::debug(false);
			$this->view->records = R::batch($this->type, array_keys($assoc));
			//R::debug(true);
            $this->view->total = R::getCell($this->view->record->sqlForTotal($whereClause), $this->view->filter->filterValues());
			//R::debug(false);
			//error_log(count($this->records));
			return true;
		} catch (Exception $e) {
            Cinnebar_Logger::instance()->log('Scaffold Collection has issues: '.$e.' '.$sql, 'sql');
			$this->view->records = array();
			return false;
		}    
    }
    
    /**
     * Returns the offset calculated from the current page number and limit of rows per page.
     *
     * @param int $page
     * @param int $limit
     * @return int
     */
    protected function offset($page, $limit)
    {
        return ($page - 1) * $limit;
    }
    
    /**
     * Returns the sort direction.
     *
     * @param int $dir
     * @return string
     */
    protected function sortdir($dir = 0)
    {
        if ( ! isset($this->sortdirs[$dir])) return 'ASC';
        return $this->sortdirs[$dir];
    }
    
	/**
	 * Checks for the callback trigger method and if it exists calls it.
	 *
	 * You can have none, one or all of these callbacks in your custom controller:
	 * - before_add
	 * - after_add
	 * - before_view
	 * - after_view
	 * - before_edit
	 * - after_edit
	 * - before_delete
	 * - after_delete
	 * - before_index
	 * - after_index
	 *
	 * @param string $action
	 * @param string $condition
	 * @return void
	 */
	public function trigger($action, $condition)
	{
	    $callback = $condition.'_'.$action;
        if ( ! method_exists($this, $callback)) return;
        $this->$callback();
	}
	
	/**
	 * Applies the action to a selection of beans.
	 *
	 * @param mixed $pointers must hold a key/value array where key is the id of a bean
	 * @param int $action
	 * @return bool
	 */
	protected function applyToSelection($pointers = null, $action = 'idle')
	{
        if ( ! $this->permission()->allowed($this->user(), $this->type, $action)) {
			return false;
		}	    

        if ( empty($pointers)) return false;
        if ( ! is_array($pointers)) return false;
        $valid = true;
        foreach ($pointers as $id=>$switch) {
            $record = R::load($this->type, $id);
            try {
                $record->$action();
            } catch (Exception $e) {
                $valid = false;
            }
        }
        if ($valid) return count($pointers);
        return false;
	}

    /**
     * Displays a page with a (paginated) selection of beans.
     *
     * @param int $page
     * @param int $limit
     * @param string $layout
     * @param int $order
     */
    public function report($page = 1, $limit = self::LIMIT, $layout = self::LAYOUT, $order = 0, $dir = 0)
    {
        $this->cache()->deactivate();
        
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/'.$this->router()->internalUrl())));
        
        if ( ! $this->permission()->allowed($this->user(), $this->type, 'index')) {
			return $this->error('403');
		}

        $this->env($page, $limit, $layout, $order, $dir, null, 'report');
        /*
        if (empty($this->view->filter->ownCriteria)) {
            $attribute = reset($this->view->record->attributes('report'));
            $criteria = R::dispense('criteria');
            $criteria->attribute = $attribute['orderclause'];
            if (isset($attribute['filter']['orderclause'])) {
                $criteria->attribute = $attribute['filter']['orderclause'];
            }
            $criteria->tag = $attribute['filter']['tag'];
            $this->view->filter->ownCriteria = array($criteria);
        }
        */
        $this->trigger('report', 'before');
        
        
        if ($this->input()->post()) {
            
            if ($this->input()->post('otherreport')) {
                // change to another report
                $_SESSION['filter'][$this->type]['id'] = $this->input()->post('otherreport');
                $this->redirect(sprintf('/%s/report/%d/%d/%s/%d/%d/', $this->type, 1, $this->limit, $this->layout, $this->order, $this->dir));
            }
            
            if ($this->input()->post('submit') == __('filter_submit_refresh')) {
                $this->view->filter = R::graph($this->input()->post('filter'), true);
                try {
                    R::store($this->view->filter);
                    $_SESSION['filter'][$this->type]['id'] = $this->view->filter->getId();
                    $this->redirect(sprintf('/%s/report/%d/%d/%s/%d/%d/', $this->type, 1, $this->limit, $this->layout, $this->order, $this->dir));
                } catch (Exception $e) {
                    Cinnebar_Logger::instance()->log($e, 'exceptions');
                    $message = __('action_filter_error');
                    with(new Cinnebar_Messenger)->notify($this->user(), $message, 'error');
                    //$this->view->filter->addError('error_filter_could_not_be_applied');
                }
            }
            if ($this->input()->post('submit') == __('filter_submit_clear') && $_SESSION['filter'][$this->type]['id']) {
                //R::trash($this->view->filter);
                $_SESSION['filter'][$this->type]['id'] = 0;
                unset($_SESSION['filter'][$this->type]['id']);
                $this->redirect(sprintf('/%s/report/%d/%d/%s/%d/%d/', $this->type, 1, $this->limit, $this->layout, $this->order, $this->dir));
            }
            if ($this->input()->post('submit') == __('filter_submit_delete') && $_SESSION['filter'][$this->type]['id']) {
                R::trash($this->view->filter);
                $_SESSION['filter'][$this->type]['id'] = 0;
                unset($_SESSION['filter'][$this->type]['id']);
                $this->redirect(sprintf('/%s/report/%d/%d/%s/%d/%d/', $this->type, 1, $this->limit, $this->layout, $this->order, $this->dir));
            }
            
            if ($this->input()->post('submit') == __('scaffold_submit_order')) {
                $this->redirect(sprintf('/%s/report/%d/%d/%s/%d/%d/', $this->type, 1, $this->limit, $this->layout, $this->input()->post('order'), $this->input()->post('dir')));            
            }
            
            $this->view->selection = $this->input()->post('selection');
            $counter = $this->applyToSelection($this->view->selection[$this->type], $this->input()->post('action'));
            if ($counter) {
                $message = __('scaffold_apply_to_selection_success', array($counter, __('action_'.$this->input()->post('action'))), null, null, 'This takes two parameters where the first one is the number of records and the second is the translation of the applied action token');
                with(new Cinnebar_Messenger)->notify($this->user(), $message, 'success');
            }

            $this->trigger('report', 'after');
            $this->redirect(sprintf('/%s/report/%d/%d/%s/%d/%d/', $this->type, 1, $this->limit, $this->layout, $this->order, $this->dir));
        }

        $this->collection();
        
        $this->view->pagination = new Cinnebar_Pagination(
            $this->view->url(sprintf('/%s/report/', $this->type)),
            $this->page,
            $this->limit,
            $this->layout,
            $this->order,
            $this->dir,
            $this->view->total
        );
        
        $this->trigger('report', 'after');
        
        echo $this->view->render();
    }
    
    /**
     * Displays a page with a (paginated) selection of beans.
     *
     * @param int $page
     * @param int $limit
     * @param string $layout
     * @param int $order
     */
    public function index($page = 1, $limit = self::LIMIT, $layout = self::LAYOUT, $order = 0, $dir = 0)
    {
        $this->cache()->deactivate();
        
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/'.$this->router()->internalUrl())));
        
        if ( ! $this->permission()->allowed($this->user(), $this->type, 'index')) {
			return $this->error('403');
		}

        $this->env($page, $limit, $layout, $order, $dir, null, 'index');
        
        $this->trigger('index', 'before');
        
        
        if ($this->input()->post()) {
            
            if ($this->input()->post('submit') == __('filter_submit_refresh')) {
                $this->view->filter = R::graph($this->input()->post('filter'), true);
                try {
                    R::store($this->view->filter);
                    $_SESSION['filter'][$this->type]['id'] = $this->view->filter->getId();
                    $this->redirect(sprintf('/%s/index/%d/%d/%s/%d/%d/', $this->type, 1, $this->limit, $this->layout, $this->order, $this->dir));
                } catch (Exception $e) {
                    Cinnebar_Logger::instance()->log($e, 'exceptions');
                    $message = __('action_filter_error');
                    with(new Cinnebar_Messenger)->notify($this->user(), $message, 'error');
                    //$this->view->filter->addError('error_filter_could_not_be_applied');
                }
            }
            if ($this->input()->post('submit') == __('filter_submit_clear') && $_SESSION['filter'][$this->type]['id']) {
                R::trash($this->view->filter);
                $_SESSION['filter'][$this->type]['id'] = 0;
                $this->redirect(sprintf('/%s/index/%d/%d/%s/%d/%d/', $this->type, 1, $this->limit, $this->layout, $this->order, $this->dir));
            }
            
            if ($this->input()->post('submit') == __('scaffold_submit_order')) {
                $this->redirect(sprintf('/%s/index/%d/%d/%s/%d/%d/', $this->type, 1, $this->limit, $this->layout, $this->input()->post('order'), $this->input()->post('dir')));            
            }
            
            $this->view->selection = $this->input()->post('selection');
            $counter = $this->applyToSelection($this->view->selection[$this->type], $this->input()->post('action'));
            if ($counter) {
                $message = __('scaffold_apply_to_selection_success', array($counter, __('action_'.$this->input()->post('action'))), null, null, 'This takes two parameters where the first one is the number of records and the second is the translation of the applied action token');
                with(new Cinnebar_Messenger)->notify($this->user(), $message, 'success');
            }

            $this->trigger('index', 'after');
            $this->redirect(sprintf('/%s/index/%d/%d/%s/%d/%d/', $this->type, 1, $this->limit, $this->layout, $this->order, $this->dir));
        }

        $this->collection();
        
        $this->view->pagination = new Cinnebar_Pagination(
            $this->view->url(sprintf('/%s/index/', $this->type)),
            $this->page,
            $this->limit,
            $this->layout,
            $this->order,
            $this->dir,
            $this->view->total
        );
        
        $this->trigger('index', 'after');
        
        echo $this->view->render();
    }
    
    /**
     * Prints selection of beans.
     *
     * This is basically the same as index but limit and offset are manipulated to start from
     * zero (0) offset and use all records possible.
     *
     * @param int $page
     * @param int $limit
     * @param string $layout
     * @param int $order
     */
    public function press($page = 1, $limit = self::LIMIT, $layout = self::LAYOUT, $order = 0, $dir = 0)
    {
        $this->cache()->deactivate();
        
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/'.$this->router()->internalUrl())));
        
        if ( ! $this->permission()->allowed($this->user(), $this->type, 'index')) {
			return $this->error('403');
		}

        $this->env($page, $limit, $layout, $order, $dir, null, 'index');
        
        // memorize limit and offset
        $real_limit = $this->limit;
        $real_offset = $this->offset;
        // use offset and limit...
        $this->limit = R::count($this->type);//10000000;
        $this->offset = 0;
        // ... to get a collection of all records
        $this->collection();
        // and then go back to what is used on the screen
        $this->limit = $real_limit;
        $this->offset = $real_offset;
        
        $data = array();
        //$data[] = $this->view->record->exportToCSV(true);
        foreach ($this->view->records as $id => $record) {
            $data[] = $record->exportToCSV(false, $layout);
        }
        
        require_once BASEDIR.'/vendors/parsecsv-0.3.2/parsecsv.lib.php';
        $csv = new ParseCSV();
        $csv->output(true, __($this->view->record->getMeta('type').'_head_title').'.csv', $data, $this->view->record->exportToCSV(true, $layout));
        exit;
    }
    
    /**
     * dup(licate) a record and redirect to edit it
     */
    public function duplicate($id)
    {
        $this->cache()->deactivate();
        
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/'.$this->router()->internalUrl())));
        
        if ( ! $this->permission()->allowed($this->user(), $this->type, 'add')) {
			return $this->error('403');
		}
		
		$record = R::load($this->type, $id);
		$dup = R::dup($record);
		//error_log($dup->name . ' Copy');
		$dup->name = $dup->name . ' Kopie';
		try {
		    $dup->validationMode(Cinnebar_Model::VALIDATION_MODE_IMPLICIT);
		    $dup->prepareForDuplication();
		    R::store($dup);
		} catch (Exception $e) {
            Cinnebar_Logger::instance()->log($e, 'exceptions');
		}
	    // goto to edit the duplicate
	    $this->redirect(sprintf('/%s/edit/%d/', $dup->getMeta('type'), $dup->getId()));
    }
    
    /**
     * Displays the bean in a form so it can be added.
     *
     * @param int $page
     * @param int $limit
     * @param string $layout
     * @param int $order
     * @param int $dir
     */
    public function add($id = 0, $page = 1, $limit = self::LIMIT, $layout = self::LAYOUT, $order = 0, $dir = 0)
    {
        $this->cache()->deactivate();
        
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/'.$this->router()->internalUrl())));
        
        if ( ! $this->permission()->allowed($this->user(), $this->type, 'add')) {
			return $this->error('403');
		}
        
        $this->env($page, $limit, $layout, $order, $dir, $id, 'add');
        
        $this->trigger('add', 'before');
        
        if ($this->input()->post()) {
            $this->view->record = R::graph($this->input()->post('dialog'), true);
            $_SESSION['scaffold']['lasttab'] = $this->input()->post('lasttab');
            try {
                R::store($this->view->record);
                
                $_SESSION['scaffold']['add']['followup'] = $followup = $this->input()->post('action');
                
                $message = __('action_add_success');
                with(new Cinnebar_Messenger)->notify($this->user(), $message, 'success');
                
                $this->trigger('add', 'after');
                
                if ($followup == 'list') {
                    $this->redirect(sprintf('/%s/index/%d/%d/%s/%d/%d/', $this->type, 1, self::LIMIT, $this->layout, $this->order, $this->dir));
                }

                if ($followup == 'update') {
                    $this->redirect(sprintf('/%s/edit/%d/%d/%d/%s/%d/%d/', $this->type, $this->view->record->getId(), 1, self::LIMIT, $this->layout, $this->order, $this->dir));
                }
                
                $this->redirect(sprintf('/%s/add', $this->type));

            } catch (Exception $e) {
                
                Cinnebar_Logger::instance()->log($e, 'exceptions');
                $message = __('action_add_error');
                with(new Cinnebar_Messenger)->notify($this->user(), $message, 'error');
                
            }
        }
        else {
            if ($this->view->record->getId()) {
                $message = __('action_clone_success', array($this->view->url(sprintf('/%s/edit/%d/%d/%d/%s/%d/%d/', $this->type, $this->view->record->getId(), 1, self::LIMIT, $this->layout, $this->order, $this->dir))));
                with(new Cinnebar_Messenger)->notify($this->user(), $message, 'info');
                //$this->view->record = R::dup($this->view->record);
            }
        }
        
        $this->view->records = array();
        
        $this->trigger('add', 'after');
        
        echo $this->view->render();
    }
    
    /**
     * Displays a form to import csv into beans.
     *
     * @param int (optional) $id of the import bean used to import type of scaffold bean
     * @param int (optional) $page is the index of the current import record to view
     */
    public function import($id = null, $page = 0)
    {
        $this->cache()->deactivate();
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/'.$this->router()->internalUrl())));
        
        if ( ! $this->permission()->allowed($this->user(), $this->type, 'import')) {
			return $this->error('403');
		}

        $this->env($page, 0, 0, 0, 0, $id, 'import'); // horrible!!
        
        //$this->trigger('import', 'before');
        $this->view->record = R::load('import', $id);
        $this->view->record->model = $this->type; // always re-inforce bean type
        $this->view->csv = $this->view->record->csv($this->view->page);

        if ($this->input()->post()) {
            $this->view->record = R::graph($this->input()->post('dialog'), true);
            try {
                R::store($this->view->record);

                if ($this->input()->post('submit') == __('scaffold_submit_import')) {
                    $message = __('action_import_success');
                    with(new Cinnebar_Messenger)->notify($this->user(), $message, 'success');
            
                    //$this->trigger('import', 'after');
                    //$this->redirect(sprintf('/%s/import/%d', $this->type, $this->view->record->getId()));
                }
                elseif ($this->input()->post('submit') == __('import_submit_prev')) {
                    $this->view->page = max(0, $this->view->page - 1);
                }
                elseif ($this->input()->post('submit') == __('import_submit_next')) {
                    $this->view->page = min($this->view->csv['max_records'] - 1, $this->view->page + 1);
                }
                elseif ($this->input()->post('submit') == __('import_submit_execute')) {
                    // tries to import from csv with rollback
                    R::begin();
                    try {
                        $imported_ids = $this->view->record->execute(); // will throw exception if it fails
                        $message = __('action_imported_n_of_m_success', array(count($imported_ids), count($this->view->csv['records'])));
                        with(new Cinnebar_Messenger)->notify($this->user(), $message, 'success');
                        R::commit();
                        $this->redirect(sprintf('/%s/index', $this->view->record->model));
                    } catch (Exception $e) {
                        R::rollback();
                        $message = __('action_import_error_invalid_data');
                        with(new Cinnebar_Messenger)->notify($this->user(), $message, 'error');
                        $this->redirect(sprintf('/%s/import/%d/%d', $this->type, $this->view->record->getId(), $this->view->page));
                    }
                }
                else {
                    Cinnebar_Logger::instance()->log('scaffold import unkown post request', 'warn');
                }
                $this->redirect(sprintf('/%s/import/%d/%d', $this->type, $this->view->record->getId(), $this->view->page));

            } catch (Exception $e) {
            
                $message = __('action_import_error');
                Cinnebar_Logger::instance()->log($e, 'scaffold');
                with(new Cinnebar_Messenger)->notify($this->user(), $message, 'error');

            }
        }
        
        $this->view->records = array();
        //$this->trigger('import', 'after');
        
        echo $this->view->render();
    }
    
    /**
     * Displays the bean in a form so it can be edited.
     *
     * @param int $page
     * @param int $limit
     * @param string $layout
     * @param int $order
     * @param int $dir
     */
    public function edit($id, $page = 1, $limit = self::LIMIT, $layout = self::LAYOUT, $order = 0, $dir = 0)
    {
        $this->cache()->deactivate();
        
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/'.$this->router()->internalUrl())));
        
        if ( ! $this->permission()->allowed($this->user(), $this->type, 'edit')) {
			return $this->error('403');
		}

        $this->env($page, $limit, $layout, $order, $dir, $id, 'edit');
        
        $this->trigger('edit', 'before');
        if ($this->input()->post()) {
            $this->view->record = R::graph($this->input()->post('dialog'), true);
            $_SESSION['scaffold']['lasttab'] = $this->input()->post('lasttab');
            try {
                R::store($this->view->record);
                
                $message = __('action_edit_success');
                with(new Cinnebar_Messenger)->notify($this->user(), $message, 'success');

                $_SESSION['scaffold']['edit']['followup'] = $followup = $this->input()->post('action');
                
                $this->trigger('edit', 'after');
                
                if ($followup == 'next' && $id = $this->id_at_offset($this->page + 1)) {
                    $this->redirect(sprintf('/%s/edit/%d/%d/%d/%s/%d/%d/', $this->type, $id, $this->page + 1, 1, $this->layout, $this->order, $this->dir));
                }
                if ($followup == 'prev' && $id = $this->id_at_offset($this->page - 1)) {
                    $this->redirect(sprintf('/%s/edit/%d/%d/%d/%s/%d/%d/', $this->type, $id, $this->page - 1, 1, $this->layout, $this->order, $this->dir));
                }
                if ($followup == 'list') {
                    $this->redirect(sprintf('/%s/index/%d/%d/%s/%d/%d/', $this->type, 1, self::LIMIT, $this->layout, $this->order, $this->dir));
                }
                
                if ($followup == 'listandreset') {
                    unset($_SESSION['filter'][$this->type]['id']);
                    $this->redirect(sprintf('/%s/index/%d/%d/%s/%d/%d/', $this->type, 1, self::LIMIT, $this->layout, $this->order, $this->dir));
                }
                
                $this->redirect(sprintf('/%s/edit/%d/%d/%d/%s/%d/%d/', $this->type, $this->view->record->getId(), $this->page, $this->limit, $this->layout, $this->order, $this->dir));

            } catch (Exception $e) {
                Cinnebar_Logger::instance()->log($e, 'exceptions');
                $message = __('action_edit_error');
                with(new Cinnebar_Messenger)->notify($this->user(), $message, 'error');

            }
        }
        $this->view->records = array();
        $this->make_pageflip();
        $this->trigger('edit', 'after');
        
        echo $this->view->render();
    }
    
    /**
     * Creates a pageflip menu.
     *
     * @uses Cinnebar_Menu to build a prev and next page navigation for edit or other views
     */
    protected function make_pageflip()
    {
        // add pageflipper, did you like flipper? I eat it a lot.
        $next_id = $this->id_at_offset($this->page + 1);
        $prev_id = $this->id_at_offset($this->page - 1);
        
        $this->view->pageflip = new Cinnebar_Menu();
        
        if ($prev_id) {
            $page = $this->page - 1;
            $id = $prev_id;
        } else {
            $page = $this->page;
            $id = $this->view->record->getId();
        }
        $this->view->pageflip->add(__('pagination_page_prev'), $this->view->url(sprintf('/%s/edit/%d/%d/%d/%s/%d/%d/', $this->getType(), $id, $page, $this->limit, $this->layout, $this->order, $this->dir)));

        if ($next_id) {
            $page = $this->page + 1;
            $id = $next_id;
        } else {
            $page = $this->page;
            $id = $this->view->record->getId();
        }
        $this->view->pageflip->add(__('pagination_page_next'), $this->view->url(sprintf('/%s/edit/%d/%d/%d/%s/%d/%d/', $this->getType(), $id, $page, $this->limit, $this->layout, $this->order, $this->dir)));
    }
    
    /**
     * Returns the current type or alias as a string.
     *
     * @uses $typeAlias, $type
     * @return string
     */
    public function getType()
    {
        if ( ! $this->typeAlias) return $this->type;
        return $this->typeAlias;
    }
    
    /**
     * Returns the id of a bean at a certain (filtered) list position or the id of
     * the current bean if the query failed.
     *
     * @uses buildWhereClause() to check for an active filter
     * @uses Model::sqlForFilters() to gather the SQL for selection a selection of this beans
     * @param int $offset
     * @return mixed $idOfTheBeanAtPositionInFilteredListOrFalse
     */
    protected function id_at_offset($offset)
    {
        $offset--; //because we count page 1..2..3.. where the offset has to be 0..1..2..
        if ($offset < 0) return false;
        $whereClause = $this->view->filter->buildWhereClause();
		$orderClause = $this->view->attributes[$this->order]['orderclause'].' '.$this->sortdir($this->dir);

    	$sql = $this->view->record->sqlForFilters($whereClause, $orderClause, $offset, 1);

    	try {
    		return R::getCell($sql, $this->view->filter->filterValues());
    	} catch (Exception $e) {
            return false;
    	}
    }

    /**
     * Pushes setting bean to the view.
     *
     * @return void
     */
    protected function pushSettingToView()
    {
        global $config;
        if ( ! $this->view->basecurrency = R::findOne('currency', ' iso = ? LIMIT 1', array($config['currency']['base']))) {
            $this->view->basecurrency = R::dispense('currency');
            $this->view->basecurrency->iso = $config['currency']['base'];
            $this->view->basecurrency->exchangerate = 1.0000;
        }
        $this->view->setting = R::load('setting', 1);
    }

    /**
     * Pushes enabled language beans to the view.
     *
     * @return void
     */
    protected function pushEnabledLanguagesToView()
    {
        $this->view->languages = R::dispense('language')->enabled();
    }
}


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
  * A basic command.
  *
  * To add your own comman simply add a php file to the command directory of your Cinnebar
  * installation. Name the command after the scheme Command_* extends Cinnebar_Command and
  * implement methods as you wish. You will not call a command directly, instead it is called
  * from the {@link Cinnebar_Facade} while a cli cycle runs.
  *
  * Example controller may look like this:
  * <code>
  * 
  * class Command_Example extends Cinnebar_Command
  * {
  *     public function execute() {
  *         // ...
  *         // ... your code here
  *         // ...
  *     }
  * }
  * 
  * </code>
  *
  * Look at {@link Command_Welcome} as an example.
  *
  * @package Cinnebar
  * @subpackage Command
  * @version $Id$
  */
abstract class Cinnebar_Command
{
    /**
     * Holds the arguments.
     *
     * @var array
     */
    public $args = array();
    
    /**
     * Holds the flags.
     *
     * @var array
     */
    public $flags = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
    }
    
    /**
     * Execute the command.
     *
     * Every command class has to implement this.
     *
     * @return bool
     */
    abstract public function execute();
    
    /**
     * Parse the command line arguments into a an array for later use.
     *
     * 
     * @see http://code.google.com/p/tylerhall/source/browse/trunk/class.args.php
     *
     * Single letter options should be prefixed with a single
     * dash and can be grouped together. Examples:
     *
     * cmd -a
     * cmd -ab
     *
     * Values can be assigned to single letter options like so:
     *
     * cmd -a foo (a will be set to foo.)
     * cmd -a foo -b (a will be set to foo.)
     * cmd -ab foo (a and b will simply be set to true. foo is only listed as an argument.)
     *
     * You can also use the double-dash syntax. Examples:
     *
     * cmd --value
     * cmd --value foo (value is set to foo)
     * cmd --value=foo (value is set to foo)
     *
     * Single dash and double dash syntax may be mixed.
     *
     * Trailing arguments are treated as such. Examples:
     *
     * cmd -abc foo bar (foo and bar are listed as arguments)
     * cmd -a foo -b bar charlie (only bar and charlie are arguments)
     *
     * @param array $argv
     */
    public function parse(array $argv = array())
    {
        $this->flags = array();
        $this->args  = array();
        array_shift($argv);
        for($i = 0; $i < count($argv); $i++)
        {
            $str = $argv[$i];
            if(strlen($str) > 2 && substr($str, 0, 2) == '--') {
                $str = substr($str, 2);
                $parts = explode('=', $str);
                $this->flags[$parts[0]] = true;
                if(count($parts) == 1 && isset($argv[$i + 1]) && preg_match('/^--?.+/', $argv[$i + 1]) == 0) {
                    $this->flags[$parts[0]] = $argv[$i + 1];
                } elseif (count($parts) == 2) {
                    $this->flags[$parts[0]] = $parts[1];
                }
            } elseif (strlen($str) == 2 && $str[0] == '-') {
                $this->flags[$str[1]] = true;
                if (isset($argv[$i + 1]) && preg_match('/^--?.+/', $argv[$i + 1]) == 0) {
                    $this->flags[$str[1]] = $argv[$i + 1];
                }
            } elseif (strlen($str) > 1 && $str[0] == '-') {
                for ($j = 1; $j < strlen($str); $j++) {
                    $this->flags[$str[$j]] = true;
                }
            }
        }
        for ($i = count($argv) - 1; $i >= 0; $i--) {
            if (preg_match('/^--?.+/', $argv[$i]) == 0) {
                $this->args[] = $argv[$i];
            } else {
                break;
            }
        }
        $this->args = array_reverse($this->args);
    }
    
    /**
     * Returns the arguments value or false if not set.
     *
     * @return mixed
     */
    public function flag($name)
    {
        return isset($this->flags[$name]) ? $this->flags[$name] : false;
    }
    
    /**
     * Fetches user input from STDIN.
     *
     * @param string ($optional) $message
     * @return mixed
     */
    public function input($message = '')
    {
        fwrite(STDOUT, $message);
        return trim(fgets(STDIN));
    }
    
    /**
     * Displays the error page.
     *
     * @uses View
     */
    public function error($error)
    {
        $view = $this->makeView('command/error');
        $view->error = $error;
        echo $view->render();
    }
    
    /**
     * Instantiates a new View and returns it.
     *
     * The code here is pretty much the same as {@link Cinnebar_Controller::makeView()}, but it
     * lacks the options to add javascripts and stylesheets, which are not needed on command line.
     * Instead you can define the iso language code, because that usually derives from the
     * router, which we not have in a command.
     * This is a convenience method (factory) so you do not have to flood your commands
     * with all these calls to view methods like set_*, add_*, load* because you may give all
     * these stuff as parameters here.
     *
     * Usage from within a command method:
     * <code>
     * 
     * // ...
     * // ... code in your command method
     * // ...
     * $view = $this->makeView('command/inspector');
     * // ...
     * // ... more code
     * // ...
     * 
     * </code>
     *
     * @uses Cinnebar_View::__construct() to instantiate a new view
     * @param string $template
     * @return Cinnebar_View an instance of {@link Cinnebar_View}
     */
    public function makeView($template)
    {
        $view = new Cinnebar_View($template);
        return $view;
    }
}


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
 * Handles the user input giving you access to parameters send by post or get requests.
 *
 * @package Cinnebar
 * @subpackage Input
 * @version $Id$
 */
class Cinnebar_Input
{
    /**
     * Constructor.
     *
     * @uses gpc_magic_quotes_repair() to clean up all input arrays
     */
    public function __construct()
    {
        $this->gpc_magic_quotes_repair();
    }
    
    /**
     * Disables magic quotes at runtime.
     *
     * It is a <b>good thing to disable gpc magic quotes at server level</b> or at least in your .htaccess
     * or with a drop-in php.ini if that is an option for you.
     * Anyway, this code will strip the slashes if gpc_magic_quotes was not disabled.
     * See {@link http://php.net/manual/en/security.magicquotes.disabling.php}.
     *
     * @uses $_GET
     * @uses $_POST
     * @uses $_COOKIE
     * @uses $_REQUEST
     * @return void
     */
    protected function gpc_magic_quotes_repair()
    {
        if ( ! get_magic_quotes_gpc()) return; // no need to repair
        Cinnebar_Logger::instance()->log('gpc_magic_quotes should be OFF, but are ON', 'warn');
        $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
        while (list($key, $val) = each($process)) {
            foreach ($val as $k => $v) {
                unset($process[$key][$k]);
                if (is_array($v)) {
                    $process[$key][stripslashes($k)] = $v;
                    $process[] = &$process[$key][stripslashes($k)];
                } else {
                    $process[$key][stripslashes($k)] = stripslashes($v);
                }
            }
        }
        unset($process);
    }
    
    /**
     * Returns a value from POST or null if token is not set.
     *
     * If token is null this method returns wether there is something in the POST array or not.
     *
     * @uses $_POST
     * @uses sanatized()
     * @param string (optional) $token
     * @return mixed
     */
    public function post($token = null)
    {
        if ($token === null && ! empty($_POST)) return true;
        if ( ! isset($_POST[$token])) return null;
        return $this->sanatized($_POST[$token]);
    }

    /**
     * Returns a value from GET or null if token is not set.
     *
     * If token is null this method returns wether there is something in the GET array or not.
     *
     * @uses $_GET
     * @uses sanatized()
     * @param string (optional) $token
     * @return mixed
     */
    public function get($token = null)
    {
        if ($token === null && ! empty($_GET)) return true;
        if ( ! isset($_GET[$token])) return null;
        return $this->sanatized($_GET[$token]);
    }
    
    /**
     * Returns the sanatized value.
     *
     * @todo Implement sanatization of given value, regardless of being an array or single value
     * @param mixed $value
     * @return mixed $sanatized_value
     */
    public function sanatized($value)
    {
        return $value;
    }
}


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
 * Manages role based access control on user beans.
 *
 * @uses $_SESSION to cache current users permission after the first usage for the active session
 *
 * @package Cinnebar
 * @subpackage Permission
 * @version $Id$
 */
class Cinnebar_Permission implements iPermission
{
	/**
	 * Constructs a new Permission
	 */
	public function __construct()
	{
	}

	/**
	 * Returns wether user is allowed to do action on domain or not.
	 *
	 * @param mixed $user
	 * @param string $domain
	 * @param string $action
	 * @return bool
	 */
	public function allowed($user = null, $domain, $action)
	{
		if ( ! $user || ! $user->getId()) return false;
		if ($user->admin) return true;
		$this->load($user);
		if (isset($_SESSION['permissions'][$domain][$action]))
			return (bool)$_SESSION['permissions'][$domain][$action];
		return false;
	}
	
	/**
	 * returns an key/value array of all domains where user can do action.
	 *
	 * @param mixed $user
	 * @param string $action
	 * @return array
	 */
	public function domains($user, $action)
	{
		if ( ! $user || ! $user->getId()) {
			return array();
		}
		$this->load($user);
		$ret = array();
		foreach ($_SESSION['permissions'] as $domain=>$actions) {
			if (isset($actions[$action]) && $actions[$action]) {
				$ret[$domain] = __('domain_'.$domain); // localized name
			}
		}
		asort($ret);
		return $ret;
	}
	
	/**
	 * Loads the users permissions and caches them in users session.
	 *
	 * @param mixed $user
	 */
	public function load($user = null)
	{
		if (isset($_SESSION['permissions']) && is_array($_SESSION['permissions'])) return;
		$_SESSION['permissions'] = array();
		$sql = <<<SQL

			SELECT
				domain.name AS domain,
				action.name AS action,
				permission.allow AS allow
			FROM
				user

			LEFT JOIN role_user ON role_user.user_id = user.id
			LEFT JOIN role ON role.id = role_user.role_id
			LEFT JOIN rbac ON rbac.role_id = role.id
			LEFT JOIN permission ON permission.rbac_id = rbac.id
			LEFT JOIN domain ON domain.id = rbac.domain_id
			LEFT JOIN action ON action.id = permission.action_id

			WHERE
				user.id = ?

			ORDER BY
				role.sequence
SQL;
		$rbacs = R::getAll($sql, array($user->getId()));
		foreach ($rbacs as $n=>$rbac) {
			$_SESSION['permissions'][$rbac['domain']][$rbac['action']] = $rbac['allow'];
		}
	}
}


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
 * The basic plugin class of the cinnebar system.
 *
 * To add your own plugin simply add a php file to the plugin directory of your Cinnebar
 * installation. Name the plugin after the scheme Plugin_* extends Cinnebar_Plugin and
 * implement a execute() method. You will not call a plugin directly, but you will use it from
 * a controller.
 *
 * @package Cinnebar
 * @subpackage Plugin
 * @version $Id$
 */
class Cinnebar_Plugin
{
    /**
     * Holds the instance of the controller in which this plugin runs.
     *
     * @var Cinnebar_Controller
     */
    public $controller;

    /**
     * Constructor.
     * @param Cinnebar_View $view
     */
    public function __construct(Cinnebar_Controller $controller)
    {
        $this->controller = $controller;
    }
    
    /**
     * Returns an instance of the controller from which this plugin was called.
     *
     * @return Cinnebar_Controller
     */
    public function controller()
    {
        return $this->controller;
    }
    
    /**
     * Executes the plugin.
     *
     * @return bool $alwaysTrue
     */
    public function execute()
    {
        echo 'Hello, i am a plugin.';
    }
}


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
 * Dispenses a new bean, own(ed) or shared of a master bean and displays its template.
 *
 * @package Cinnebar
 * @subpackage Plugin
 * @version $Id$
 */
class Plugin_Attach extends Cinnebar_Plugin
{
	/**
	 * Dispenses a blank Bean as either own or shared and outputs the template.
	 *
	 * @uses controller() to fetch the calling controller
	 * @param string $prefix
	 * @param string $type
	 * @param mixed (optional) $id
	 * @return void
	 */
	public function execute($prefix, $type, $id = 0)
	{
        session_start();
        $this->controller()->cache()->deactivate();
        
		$n = md5(microtime(true));
        $record = R::dispense($type);
        $this->controller()->view = $this->controller()->makeView(sprintf('model/%s/form/%s/%s', $this->controller()->type, $prefix, $type));
        $this->controller()->view->n = $n;
        $this->controller()->view->$type = $record;
        $this->controller()->trigger('edit', 'before');
        echo $this->controller()->view->render();
		return true;
	}
}


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
 * Drops an existing bean own(ed) or shared by a master bean.
 *
 * @package Cinnebar
 * @subpackage Plugin
 * @version $Id$
 */
class Plugin_Detach extends Cinnebar_Plugin
{
	/**
	 * Deletes a Bean and drops the sub form from the clients view.
	 *
	 * @param string $prefix has to be either own or shared
	 * @param string $type
	 * @param mixed (optional) $id
	 * @param mixed $(optional) master_id
	 */
	public function execute($prefix, $type, $id = 0, $master_id = 0)
	{
        session_start();
        $this->controller()->cache()->deactivate();
        
		return $this->$prefix($type, $id, $master_id);
	}
	
	/**
	 * Deletes a own(ed) Bean.
	 *
	 * @param string $type
	 * @param mixed $id
	 * @param mixed $master_id
	 */
	protected function own($type, $id, $master_id)
	{
		$detachable = R::load($type, $id);
		if ( ! $detachable->getId()) return;
		try {
			R::trash($detachable);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	/**
	 * Disconnects a shared Bean from its master.
	 *
	 * @todo Find a better way to get rid of shared relationship.
	 *
	 * @param string $type
	 * @param mixed $id
	 * @param mixed $master_id
	 */
	protected function shared($type, $id, $master_id)
	{
		try {
			$sharedContainer = 'shared'.ucfirst($type);
			$master = R::load($this->controller()->type, $master_id);
			if ( ! $master->getId()) return;
			$shared = $master->$sharedContainer;
			if ( ! isset($shared[$id])) return;
			unset($shared[$id]);
			$master->$sharedContainer = $shared;
			R::store($master);
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
}


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
 * Displays an error page with the given error code.
 *
 * @package Cinnebar
 * @subpackage Plugin
 * @version $Id$
 */
class Plugin_Error extends Cinnebar_Plugin
{
    /**
     * Renders a error page to the client.
     *
     * @uses Cinnebar_Cache::deactivate() to turn off caching for the errornous URL
     * @uses Cinnebar_Controller::makeView() to factory the error view
     * @param string (optional) $code The error code you want to render, defaults to 404
     * @return void
     */
    public function execute($code = '404')
    {
        $this->controller()->cache()->deactivate();
        $view = $this->controller()->makeView('error/index');
        $view->title = __('error_head_title');
        $view->nav = with(new Cinnebar_Menu)->add(__('domain_app'), $view->url('/home'));
        $view->code = $code;
        echo $view->render();
    }
}

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
 * The hash class of the cinnebar system.
 *
 * This class is super simple and it is much better to use phpass, which can be configured in
 * your Cinnebar installations config.php file. See {@link config.example.php} for more information
 * about how to activate phpass, which is recommended over this class.
 *
 * @package Cinnebar
 * @subpackage Hash
 * @version $Id$
 */
class Cinnebar_Hash
{
    /**
     * Holds the algorithm for hashing.
     *
     * @var string
     */
    public $hash_algo;
    
    /**
     * Holds the salt for our hash.
     *
     * @todo Implement a much better default hash thingy
     * @var string
     */
    public $salt = '&5889Hghgjhj5%&%/ftddsop==9987897';

    /**
     * Constructor.
     *
     * @param string $hash_algo
     */
    public function __construct($hash_algo = null)
    {
        if (null === $hash_algo) $hash_algo = 'md5';
        $this->hash_algo = $hash_algo;
    }
    
    /**
     * Returns a salted hash for a given string.
     *
     * @param string $pw
     * @return string $hash
     */
    public function HashPassword($pw)
    {
        $callback = $this->hash_algo;
        return $callback($this->salt.$pw);
    }
    
    /**
     * Returns wether the password given matches the stored password.
     *
     * @param string $pw
     * @param string $pw_stored
     * @return bool $WetherThePasswordMatchesOrNot
     */
    public function CheckPassword($pw, $pw_stored)
    {
        return ($this->HashPassword($pw) == $pw_stored);
    }
}


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
 * Provides some useful methods to maninulate arrays and strings and so on.
 *
 * @package Cinnebar
 * @subpackage Core
 * @version $Id$
 */
class Cinnebar_Element
{
    /**
     * Container for dependencies.
     *
     * @var array
     */
    public $deps = array();

    /**
     * Defines the checked attribute for checkboxes.
     *
     * @var string
     */
    const CHECKED = ' checked="checked"';

    /**
     * Defines the selected attribute for select options.
     *
     * @var string
     */
    const SELECTED = ' selected="selected"';
    
    /**
     * Defines the disabled attribute for input tags.
     *
     * @var string
     */
    const DISABLED = ' disabled="disabled"';
    
    /**
     * Defines the reda-only attribute for input tags.
     *
     * @var string
     */
    const READONLY = ' readonly="readonly"';
    
    /**
     * Defines the value of CSS style display block.
     *
     * @var string
     */
    const DISPLAY_BLOCK = 'block';

    /**
     * Defines the value of CSS style display none.
     *
     * @var string
     */
    const DISPLAY_NONE = 'none';

    /**
     * Holds errors messages for this element.
     *
     * @var array
     */
    public $errors = array();
    
    /**
     * Container for attributes.
     *
     * @var array
     */
    public $data = array();

    /**
     * Constructor.
     *
     */
    public function __construct()
    {
    }
    
    /**
     * Sets the value of attribute.
     *
     * @uses $data
     * @param string $attribute
     * @param mixed (optional) $value
     */
    public function __set($attribute, $value = null)
    {
        $this->data[$attribute] = $value;
    }
    
    /**
     * Unsets the value of attribute.
     *
     * @param string $attribute
     */
    public function __unset($attribute)
    {
        unset($this->data[$attribute]);
    }
    
    /**
     * Returns wether a value is set or not.
     *
     * @param string $attribute
     * @return bool
     */
    public function __isset($attribute)
    {
        return isset($this->data[$attribute]);
    }
    
    /**
     * Returns the value of an attribute or NULL if value is not set.
     *
     * @param string $attribute
     * @return mixed
     */
    public function __get($attribute)
    {
        if (array_key_exists($attribute, $this->data)) return $this->data[$attribute];
        return null;
    }
    
    /**
     * Inject dependencies.
     *
     * @uses $deps
     * @param array $deps
     */
    public function di(array $deps)
    {
        $this->deps = $deps;
    }

    /**
     * Adds an error to our error container.
     *
     * @param string $error_text
     * @param string (optional) $error_type
     * @return bool
     */
    public function addError($error_text, $error_type = '')
    {
        $this->errors[$error_type][] = $error_text;
        return true;
    }
    
    /**
     * Returns true if there are errors.
     *
     * @return bool
     */
    public function hasErrors()
    {
        return count($this->errors);
    }
    
    /**
     * Returns this elements errors.
     *
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }
    
    /**
     * Returns a the given string safely to use as filename or url.
     *
     * @link http://stackoverflow.com/questions/2668854/sanitizing-strings-to-make-them-url-and-filename-safe
     *
     * What it does:
     * - Replace all weird characters with dashes
     * - Only allow one dash separator at a time (and make string lowercase)
     *
     * @param string $string the string to clean
     * @param bool $is_filename false will allow additional filename characters
     * @return string
     */
    public function sanitizeFilename($string = '', $is_filename = false)
    {
        $string = preg_replace('/[^\w\-'. ($is_filename ? '~_\.' : ''). ']+/u', '-', $string);
        return mb_strtolower(preg_replace('/--+/u', '-', $string));
    }

    /**
     * Glues together an array of key/values as a string and returns it.
     *
     * Usage Example:
     * <code>
     * 
     * $text = glue(array('title' => 'Test', 'lenght' => '4'));
     * 
     * </code>
     *
     * @param mixed (required) $dict
     * @param string (optional) $glueOpen
     * @param string (optional) $glueClose
     * @param string (optional) $pre
     * @param string (optional) $impChar
     * @return string $gluedString
     */
    public static function glue($dict, $glueOpen = '="', $glueClose = '"', $pre = ' ', $impChar = ' ')
    {
    	if (empty($dict)) return '';
    	$stack = array();
    	foreach ($dict as $key=>$value) {
    		$stack[] = $key.$glueOpen.htmlspecialchars($value).$glueClose;
    	}
    	return $pre.implode($impChar, $stack);
    }
    
    /**
     * Replaces some charactes with others and returns the stripped string.
     *
     * @param string $source
     * @param array (optional) $tokens
     * @param array (optional) $replacements
     * @return string
     */
    public static function stripped($source, array $tokens = array('[', ']'), array $replacements = array('-', ''))
    {
        return str_replace($tokens, $replacements, $source);
    }
}


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
 * Provides some useful methods to handle file uploads.
 *
 * @package Cinnebar
 * @subpackage Core
 * @version $Id$
 */
class Cinnebar_Upload extends Cinnebar_Element
{
    /**
     * Container for upload configuration.
     *
     * @var array
     */
    public $config;

    /**
     * Holds the raw filename.
     *
     * @var string
     */
    public $filename;
    
    /**
     * Holds the sanitized filename.
     *
     * @var string
     */
    public $sanitizedFilename;
    
    /**
     * Holds the uploaded file exentsion.
     *
     * @var string
     */
    public $extension;

    /**
     * Holds the path to upload dir.
     *
     * @var string
     */
    public $dir;
    
    /**
     * Flag to indicate that an existing file was not replaced by a new upload.
     *
     * @var bool
     */
    public $unchanged = false;

    /**
     * Constructor.
     *
     */
    public function __construct()
    {
        global $config;
        $this->config = $config['upload'];
    }
    
    /**
     * Returns wether the file was changed or not.
     *
     * @return bool
     */
    public function unchanged()
    {
        return $this->unchanged;
    }

    /**
     * Returns a user uploaded file.
     *
     * If there is already a current file and upload error is 4 the current file is returned.
     *
     * @param string (optional) $container is the file input name, defaults to 'upload'
     * @param mixed (optional) $allowedType defaults to null, which means any
     * @param mixed (optional) $cfilename is the current filename
     * @return string $filename
     */
    public function get($container = 'upload', $allowed = null, $cfilename = null)
    {
        if ( ! empty($cfilename) && $_FILES[$container]['error'] == 4) {
            $this->unchanged = true;
            return $cfilename;
        }
        if ($_FILES[$container]['error'] != 0) {
            $this->addError(__('upload_error_' . $_FILES[$container]['error']));
            return null;
        }
        $this->analyzeFilename($container);
        if ( ! $this->allowedExtension($allowed, $this->extension)) {
            $this->addError(__('upload_error_extension_not_allowed'));
            return null;
        }
        $pathtofile = $this->config['dir'].$this->sanitizedFilename.'.'.$this->extension;
        if ( ! move_uploaded_file($_FILES[$container]['tmp_name'], $pathtofile)) {
            $this->addError(__('upload_error_move_uploaded_file'));
            return null;
        }
        $this->dir = $this->config['dir'];
        $this->filesize = filesize($pathtofile);
        return $this->sanitizedFilename.'.'.$this->extension;
    }
    
    /**
     * Returns wether the uploaded file extension is allowed or not.
     *
     * @param mixed $allowed
     * @param string $extension
     * @return bool
     */
    public function allowedExtension($allowed, $extension)
    {
        if ($allowed === null) return true;
        if ( ! is_array($allowed)) $allowed = array($allowed);
        return (in_array($extension, $allowed));
    }
    
    /**
     * Analyzes the filename and extension of the uploaded file.
     *
     * @uses $filename
     * @uses $extension
     * @uses $sanitizedFilename
     * @param string (optional) $container is the file input name, defaults to 'upload'
     */
    protected function analyzeFilename($container = 'upload')
    {
        $file_parts = pathinfo($_FILES[$container]['name']);
        $this->filename = $file_parts['filename'];
        $this->extension = mb_strtolower($file_parts['extension']);
        $this->sanitizedFilename = $this->sanitizeFilename($this->filename);
    }
}


/**
 * Cinnebar.
 *
 * @package Cinnebar
 * @author $Author$
 * @version $Id$
 */
 
/**
 * Renders a template with data.
 *
 * @package Cinnebar
 * @subpackage View
 * @version $Id$
 */
class Cinnebar_View extends Cinnebar_Element
{
    /**
     * Holds the controller instance.
     *
     * @var Cinnebar_Controller
     */
    public $controller;

    /**
     * Holds the template name.
     *
     * @var string
     */
    public $template;
    
    /**
     * Holds the stylesheets assigned to this view.
     *
     * @var array
     */
    public $stylesheets = array();
    
    /**
     * Holds the javascript-(files) assigned to this view.
     *
     * @var array
     */
    public $javascripts = array();

    /**
     * Constructs a new View Manager.
     *
     * @param string $template
     */
    public function __construct($template)
    {
        $this->template = $template;
    }
    
    /**
     * Returns the controller or sets it when the optional parameter is given.
     *
     * @param mixed (optional) $controller which must be an Cinnebar_Controller
     * @return Cinnebar_Controller
     */
    public function controller($controller = null)
    {
        if ($controller === null) return $this->controller;
        $this->controller = $controller;
        return $this->controller;
    }
    
    /**
     * Returns the current user that uses this view.
     *
     * @return RedBean_OODBBean $user may be an empty guest user
     */
    public function user()
    {
        return $this->controller()->user();
    }
    
    /**
     * Returns the language code.
     *
     * @uses Cinnebar_Router::language()
     * @return string
     */
    public function language()
    {
        return $this->controller()->router()->language();
    }
    
    /**
     * Returns a string containing either the URL or a page title.
     *
     * @return string
     */
    public function title()
    {
        if (isset($this->title)) return $this->title;
        return $this->controller()->router()->basehref();
    }
    
    /**
     * Returns the base href.
     *
     * @uses Cinnebar_Router::basehref()
     * @return string
     */
    public function basehref()
    {
        return $this->controller()->router()->basehref();
    }
    
    /**
     * Resets the stylesheets array.
     *
     * @uses Cinnebar_View::$stylesheets empty array
     * @return bool $reset
     */
    public function resetStyles()
    {
        $this->stylesheets = array();
        return true;
    }
    
    /**
     * Add a file or an array of files to the stylesheets array.
     *
     * @uses Cinnebar_View::$stylesheets
     * @param mixed $files
     * @return bool $added
     */
    public function addStyle($files)
    {
        if ( ! is_array($files)) $files = array($files);
        foreach ($files as $file) {
            $this->stylesheets[] = $file;
        }
        return true;
    }

    /**
     * Returns the stylesheets array.
     *
     * @return array $stylesheets
     */
    public function styles()
    {
        return $this->stylesheets;
    }
    
    /**
     * Resets the javascripts array.
     *
     * @uses Cinnebar_View::$javascripts empty array
     * @return bool $resetted
     */
    public function resetJs()
    {
        $this->javascripts = array();
        return true;
    }
    
    /**
     * Add a file or an array of files to the javascripts array.
     *
     * @uses Cinnebar_View::$javascripts
     * @param mixed $files
     * @return bool $added
     */
    public function addJs($files)
    {
        if ( ! is_array($files)) $files = array($files);
        foreach ($files as $file) {
            $this->javascripts[] = $file;
        }
        return true;
    }
    
    /**
     * Returns the javascripts array.
     *
     * @return array $javascripts
     */
    public function js()
    {
        return $this->javascripts;
    }
    
    /**
     * Runs a viewhelper from the viewhelper directory.
     *
     * @param string $method
     * @param array (optional) $params
     * @return mixed
     */
    public function __call($method, array $params = array())
    {
        $helper_name = 'Viewhelper_'.ucfirst(strtolower($method));
        $helper = new $helper_name($this);
        return call_user_func_array(array($helper, 'execute'), $params);
    }
    
    /**
     * Renders a partial template.
     *
     * @uses workhorse_render()
     * @param string $partial
     * @param array (optional) $values
     * @return mixed
     */
    public function partial($partial, array $values = array())
    {
        return $this->render_workhorse($partial, array_merge($this->data, $values));
    }
    
    /**
     * Renders a template.
     *
     * @uses workhorse_render()
     * @param string ($optional) $template
     * @return string
     */
    public function render($template = null)
    {
        if ( $template === null) $template = $this->template;
        return $this->render_workhorse($template, $this->data);
    }
    
    /**
     * Returns true or false wether the template exists or not.
     *
     * @param string $template
     * @return bool
     */
    public function exists($template)
    {
        $file = BASEDIR.'/themes/'.S_THEME.'/templates/'.$template.'.php';
        if ( ! is_file($file)) {
            Cinnebar_Logger::instance()->log(sprintf('Template "%s" not found', $template), 'warn');
            return false;
        }
        return $file;
    }
    
    /**
     * Returns a string where template and data are mixed.
     *
     * @param string $template
     * @param array (optional) $values
     * @return string
     * @throws Exception if a template is missing
     */
    public function render_workhorse($template, array $values = array())
    {
        if ( ! $file = $this->exists($template)) return '';
        extract($values);
        ob_start();
        include $file;
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
    }
}


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
 * Manages hierarchical menus.
 *
 * Parts of this code was taken from {@link http://coreyworrell.com}.
 *
 * @todo Fuzzy check of current url, e.g. /token/index should be active when /token/index/x/y is the url
 *
 * @package Cinnebar
 * @subpackage Menu
 * @version $Id$
 */
class Cinnebar_Menu extends Cinnebar_Element
{
    /**
     * Container for templates.
     *
     * @var array 
     */
    public $templates = array(
        'list-open' => '<ul%s>',
        'item-open' => '<li %s %s><a href="%s">%s</a>', // id | class | url | linktext
        'item-close' => '</li>',
        'list-close' => '</ul>'
    );

    /**
     * Container for menu entries.
     *
     * @var array
     */
    public $items = array();
    
    /**
     * Renders the menu when echoed or printed.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
    
    /**
     * Sets a certain template string.
     *
     * @param string $name
     * @param string $template
     * @return $this for chaining
     */
    public function setTemplate($name, $template)
    {
        $this->templates[$name] = $template;
        return $this;
    }
    
    /**
     * Add a item to the menu.
     *
     * @param string $title
     * @param string $url
     * @param string $class
     * @param Cinnebar_Menu (optional) $sub
     * @param string (optional) $id
     * @return $this
     */
    public function add($title, $url, $class = null, Cinnebar_Menu $sub = null, $id = null)
    {
        $this->items[] = array(
            'id' => $id,
            'class' => $class,
            'title' => $title,
            'url' => $url,
            'children' => is_object($sub) ? $sub->items : null
        );
        return $this;
    }
    
	/**
	 * Renders the HTML output for the menu
	 *
	 * @param array $attrs associative array of html attributes
	 * @param array $current associative array containing the key and value of current url
	 * @param array $items the parent item's array, only used internally
	 * @return string HTML unordered list
	 */
	public function render(array $attrs = null, $current = null, array $items = null)
	{
		static $i;
		
		$items = empty($items) ? $this->items : $items;
		$current = empty($current) ? $this->current : $current;
		$attrs = empty($attrs) ? $this->attrs : $attrs;
		
		$i++;
		$menu = sprintf($this->templates['list-open'], ($i == 1 ? self::glue($attrs) : null));
		
		foreach ($items as $key => $item)
		{
			$has_children =  ! empty($item['children']);
			
			$class = array($item['class']);
			
			$has_children ? $class[] = 'parent sm2_liOpen' : null;
			
			if ( ! empty($current))
			{
				if ($current_class = self::current($current, $item))
				{
					$class[] = $current_class;
				}
			}
			$classes = ! empty($class) ? self::glue(array('class' => implode(' ', $class))) : null;
			$id = null;
			if (isset($item['id']) && $item['id']) $id = ' id="'.$item['id'].'"';
			$menu .= sprintf($this->templates['item-open'], $id, $classes, $item['url'], $item['title']);
			$menu .= $has_children ? $this->render(null, $current, $item['children']) : null;
			$menu .= $this->templates['item-close'];
		}
		
		$menu .= $this->templates['list-close'];
		
		$i--;
		
		return $menu;
	}
	
	/**
	 * Figures out if items are parents of the active item.
	 *
	 * @param array $current the current url array (key, match)
	 * @param array $item the array to check against
	 * @return bool
	 */
	protected static function current($current, array $item)
	{
		if ($current === $item['url']) {
			return 'active current';
		} else {
		    if (self::active($item, $current, 'url')) return 'active';
		}
		return '';
	}
	
	/**
	 * Recursive function to check if active item is child of parent item
	 *
	 * @param array $array the list item
	 * @param string $value the current active item
	 * @param string $key to match current against
	 * @return bool
	 */
	public static function active($array, $value, $key)
	{
		foreach ($array as $val) {
			if (is_array($val)) {
				if (self::active($val, $value, $key)) return true;
			} else {
				if ($array[$key] === $value) return true;
			}
		}
		return false;
	}
}


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
 * Calculates and renders pagination links.
 *
 * @package Cinnebar
 * @subpackage Pagination
 * @version $Id$
 */
class Cinnebar_Pagination
{
    /**
	 * Holds the base url to use for pagination links.
	 * 
	 * @var string
	 */
	public $url;
	
	/**
	 * Holds the order index value.
	 *
	 * @var int
	 */
	public $order;

	/**
	 * Holds the order direction.
	 *
	 * @var int
	 */
	public $dir;

	/**
	 * Holds the current page number.
	 *
	 * @var int
	 */
	public $page;
	
	/**
	 * Holds the number of maximum pages.
	 *
	 * @var int
	 */
	public $max_pages;
	
	/**
	 * Holds the number of rows per page.
	 *
	 * @var int
	 */
	public $limit;
	
	/**
	 * Holds the number or total rows.
	 *
	 * @var int
	 */
	public $total_rows;
	
	/**
	 * Holds the number of pages to adjance when skipping.
	 *
	 * @var int $adjacents
	 */
	public $adjacents = 2;
	
	/**
	 * Flag to decide wether there is a previous page or not.
	 *
	 * @var bool
	 */
	public $has_previous_page = false;
	
	/**
	 * Flag to decide wether there is a next page or not.
	 *
	 * @var bool
	 */
	public $has_next_page = false;
	
	/**
	 * Flag to decide wether to generate page links or not.
	 *
	 * @var bool
	 */
	public $page_links = true;
	
	/**
	 * Constructor.
	 *
	 * @param string (required) $url
	 * @param int (required) $offset
	 * @param int (required) $limit
	 * @param int (required) $layout
	 * @param int (required) $order
	 * @param int (required) $dir
 	 * @param int (required) $total_rows
  	 * @param bool (optional) $page_links decides wether to build page links or not, defaults to true
	 */
	public function __construct($url, $page, $limit, $layout, $order, $dir, $total_rows, $page_links = true)
	{
		$this->url = $url;
		$this->page = $page;
		$this->limit = $limit;
		$this->layout = $layout;
		$this->order = $order;
		$this->dir = $dir;
		$this->total_rows = $total_rows;
		$this->page_links = $page_links;
	}
	
	/**
	 * Render the pagination on string output.
	 *
	 * @return string
	 */
	public function __toString()
	{
        return $this->render();
	}
	
	/**
	 * Renders the pagination links and returns a string.
	 *
	 * @return string
	 */
	public function render()
	{
		$this->calculate();
		return $this->build_html_pagination();
	}
	
	/**
	 * Calculate the pagination values.
	 */
	protected function calculate()
	{	
		if ($this->limit)
		{
			$this->max_pages = ceil($this->total_rows / $this->limit);
		}
		else
		{
			$this->max_pages = 1;
		}
		$this->page = max(1, $this->page);
		$this->page = min($this->max_pages, $this->page);
		$this->has_previous_page = $this->page > 1;
		$this->has_next_page = $this->page < $this->max_pages;
	}
	
	/**
	 * Returns a string with HTML.
	 *
	 * @return string
	 */
	protected function build_html_pagination()
	{
		if ($this->max_pages == 1)
		{
			// uups, there is only one page in total
			return '';
		}
		$s = '<ul>'."\n";
		
		// prev page
		if ($this->has_previous_page)
		{
			// with link
			$query = array(
				$this->page - 1,
				$this->limit,
				$this->layout,
				$this->order,
				$this->dir
			);
			$s .= '<li class="prev">'.$this->ahref($this->url.implode('/', $query), __('pagination_page_prev')).'</li>'."\n";
		}
		else
		{
			// without link
			$s .= '<li class="prev">'.__('pagination_page_prev').'</li>'."\n";
		}
		
		// digg style pagination list
		if ($this->page_links) $s .= $this->build_html_page_links();
		
		// next page
		if ($this->has_next_page)
		{
			// with link
			$query = array(
				$this->page + 1,
				$this->limit,
				$this->layout,
				$this->order,
				$this->dir
			);
			$s .= '<li class="next">'.$this->ahref($this->url.implode('/', $query), __('pagination_page_next')).'</li>'."\n";
		}
		else
		{
			// without link
			$s .= '<li class="next">'.__('pagination_page_next').'</li>'."\n";
		}
		
		$s .= '</ul>'."\n";
		return $s;
	}
	
	/**
	 * Renders the page links and returns a string with HTML.
	 *
     * Portions of this code from {@link http://www.strangerstudios.com/sandbox/pagination/diggstyle.php}
     *
	 * @todo refactor code so DRY applies in this code
	 * @todo get rid of magic numbers
	 *
	 * @return string
	 */
	protected function build_html_page_links()
	{
		$s = '';
		if ($this->max_pages < 7 + ($this->adjacents * 2))
		{
			// not so much possible pages
	        for ($n = 1; $n <= $this->max_pages; $n++)
			{
				$s .= '<li>';
	            if ($n != $this->page)
				{
					// other than current page
					$query = array(
						$n,
						$this->limit,
        				$this->layout,
						$this->order,
						$this->dir
					);
					$s .= $this->ahref($this->url.implode('/', $query), $n);
				}
				else
				{	
					// current page
					$s .= '<span class="current">'.$n.'</span>';
				}
				$s .= '</li>'."\n";
			}
		}
		elseif ($this->max_pages >= 7 + ($this->adjacents * 2))
		{
			// hide some pages
			if ($this->page < 1 + ($this->adjacents * 3))
			{
				// At the beginning, hide pages at the end
	            for ($n = 1; $n < 4 + ($this->adjacents * 2); $n++)
				{
					$s .= '<li>';
	                if ($n != $this->page)
					{
						// other than current page
						$query = array(
							$n,
							$this->limit,
            				$this->layout,
							$this->order,
							$this->dir
						);
						$s .= $this->ahref($this->url.implode('/', $query), $n);
					}
					else
					{
						$s .= '<span class="current">'.$n.'</span>';
					}
					$s .= '</li>'."\n";
				}
				// ellipsis
				$s .= '<li>&#8230</li>'."\n";
				$s .= '<li>';
				$query = array(
					$this->max_pages - 1,
					$this->limit,
    				$this->layout,
					$this->order,
					$this->dir
				);
				$s .= $this->ahref($this->url.implode('/', $query), $this->max_pages - 1);				
				$s .= '</li>'."\n";
				$s .= '<li>';
				$query = array(
					$this->max_pages,
					$this->limit,
    				$this->layout,
					$this->order,
					$this->dir
				);
				$s .= $this->ahref($this->url.implode('/', $query), $this->max_pages);				
				$s .= '</li>'."\n";
			}
			elseif ($this->max_pages - ($this->adjacents * 2) > $this->page && $this->page > ($this->adjacents * 2))
			{
				
				// In the middle, hide pages beginning and end
				$s .= '<li>';
				$query = array(
					1,
					$this->limit,
    				$this->layout,
					$this->order,
					$this->dir
				);
				$s .= $this->ahref($this->url.implode('/', $query), '1');				
				$s .= '</li>'."\n";
				$s .= '<li>';
				$query = array(
					2,
					$this->limit,
    				$this->layout,
					$this->order,
					$this->dir
				);
				$s .= $this->ahref($this->url.implode('/', $query), '2');				
				$s .= '</li>'."\n";
				$s .= '<li>&#8230</li>'."\n";

	            for ($n = $this->page - $this->adjacents; $n <= $this->page + $this->adjacents; $n++)
				{
					$s .= '<li>';
	                if ($n != $this->page)
					{
						// other than current page
						$query = array(
							$n,
							$this->limit,
            				$this->layout,
							$this->order,
							$this->dir
						);
						$s .= $this->ahref($this->url.implode('/', $query), $n);
					}
					else
					{
						$s .= '<span class="current">'.$n.'</span>';
					}
					$s .= '</li>'."\n";
				}
				
				$s .= '<li>&#8230</li>'."\n";
				
				$s .= '<li>';
				$query = array(
					$this->max_pages - 1,
					$this->limit,
    				$this->layout,
					$this->order,
					$this->dir
				);
				$s .= $this->ahref($this->url.implode('/', $query), $this->max_pages - 1);				
				$s .= '</li>'."\n";
				$s .= '<li>';
				$query = array(
					$this->max_pages,
					$this->limit,
    				$this->layout,
					$this->order,
					$this->dir
				);
				$s .= $this->ahref($this->url.implode('/', $query), $this->max_pages);				
				$s .= '</li>'."\n";
				
			}
			else
			{
				// At the end, hide pages from the beginning
				$s .= '<li>';
				$query = array(
					1,
					$this->limit,
    				$this->layout,
					$this->order,
					$this->dir
				);
				$s .= $this->ahref($this->url.implode('/', $query), '1');				
				$s .= '</li>'."\n";
				$s .= '<li>';
				$query = array(
					2,
					$this->limit,
    				$this->layout,
					$this->order,
					$this->dir
				);
				$s .= $this->ahref($this->url.implode('/', $query), '2');				
				$s .= '</li>'."\n";
				$s .= '<li>&#8230</li>'."\n";
			
	            for ($n = $this->max_pages - (1 + ($this->adjacents * 3)); $n <= $this->max_pages; $n++)
				{
					$s .= '<li>';
	                if ($n != $this->page)
					{
						// other than current page
						$query = array(
							$n,
							$this->limit,
            				$this->layout,
							$this->order,
							$this->dir
						);
						$s .= $this->ahref($this->url.implode('/', $query), $n);
					}
					else
					{
						$s .= '<span class="current">'.$n.'</span>';
					}
					$s .= '</li>'."\n";
				}

			}
		}
		return $s;
	}
	
	/**
	 * Returns a string with an ahref tag.
	 *
	 * @param string $url
	 * @param string $text
	 * @return string
	 */
	public function ahref($url, $text)
	{
        return sprintf('<a href="%s">%s</a>', $url, $text);
	}
}


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
 * The basic viewhelper class of the cinnebar system.
 *
 * To add your own viewhelper simply add a php file to the viewhelper directory of your Cinnebar
 * installation. Name the viewhelper after the scheme Viewhelper_* extends Cinnebar_Viewhelper and
 * implement a execute() method. You will not call a viewhelper directly, but you will use it from
 * a view or a template. As an example see {@link Viewhelper_Textile}.
 *
 * Example usage of the textile viewhelper in an template of a view:
 * <code>
 * 
 * echo $this->textile('h1. Hello _World_, how are _you_?');
 * 
 * </code>
 *
 * @package Cinnebar
 * @subpackage Viewhelper
 * @version $Id$
 */
class Cinnebar_Viewhelper
{
    /**
     * Holds the instance of the view in which this viewhelper runs.
     *
     * @var Cinnebar_View
     */
    public $view;

    /**
     * Constructor.
     * @param Cinnebar_View $view
     */
    public function __construct(Cinnebar_View $view)
    {
        $this->view = $view;
    }
    
    /**
     * Returns an instance of the view from which this helper was called.
     *
     * @return Cinnebar_View
     */
    public function view()
    {
        return $this->view;
    }
    
    /**
     * Executes the Viewhelper.
     *
     * @return bool $alwaysTrue
     */
    public function execute()
    {
        echo 'Hello, i am a viewhelper.';
    }
}


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
 * The url viewhelper class of the cinnebar system.
 *
 * @package Cinnebar
 * @subpackage Viewhelper
 * @version $Id$
 */
class Viewhelper_Url extends Cinnebar_Viewhelper
{
    /**
     * Renders a url.
     *
     * The optional parameter specifies wether to generate a normal href url or when it is
     * of value 'css' or 'js' it will return a url to the given resource.
     *
     * @uses Cinnebar_Router::basehref()
     * @todo Get rid of the /../ in the path because that throws a warning in the html validators
     *
     * @param string $url
     * @param string (optional) $type defaults to href and may be href, css or js
     * @return string
     */
    public function execute($url = '', $type = 'href')
    {
        if ($type == 'href') return $this->view()->basehref().$url;
        return $this->view()->basehref().'/../themes/'.S_THEME.'/'.$type.'/'.$url.'.'.$type;
    }
}


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
 * Require the textile library.
 */
require_once BASEDIR.'/vendors/textile/classTextile.php';

/**
 * The textile viewhelper class of the cinnebar system.
 *
 * @package Cinnebar
 * @subpackage Viewhelper
 * @version $Id$
 */
class Viewhelper_Textile extends Cinnebar_Viewhelper
{
    /**
     * Renders a string with Textile.
     *
     * If the optional parameter is set to true, Textile will render content as
     * untrusted input.
     *
     * @uses Textile
     * @param string (optional) $text
     * @param bool (optional) $restricted
     * @return string $htmlizedTextile
     */
    public function execute($text = '', $restricted = false)
    {
        if (empty($text)) return '';
        $textile = new Textile();
        if ( ! $restricted) return trim($textile->TextileThis($text));
        return trim($textile->TextileRestricted($text));
    }
}


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
 * The messenger class of the cinnebar system.
 *
 * @package Cinnebar
 * @subpackage Messenger
 * @version $Id$
 */
class Cinnebar_Messenger
{
    /**
     * Constructor.
     */
    public function __construct()
    {
    }

    /**
     * Deliver an (internal) message to a certain user.
     *
     * @param mixed $user
     * @param string $msg
     * @param string $type can be 'alert', 'error', 'warn' or alike
     * @param mixed $sender
     * @return bool
     */
    public function notify($user, $msg = 'This is a test message. Ignore.', $type = 'alert', $from = null)
    {
        $notification = R::dispense('notification');
        $notification->payload = $msg;
        $notification->template = $type;
        try {
            R::store($notification);
            if (is_a($user, 'RedBean_OODBBean')) R::associate($user, $notification);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Returns an array with this users notifications and dismisses them.
     *
     * The notifications are ony fetchable once. If you do not want to trash them you have to
     * set the optional parameter to skip the trashing of retrieved notification beans.
     *
     * @param RedBean_OODBBean $bean
     * @param string $sql optional $sql
     * @param array $values optional array with values for the sql jokers
     * @param bool (optional) $trash defaults to true, so notifications are only fetchable once
     * @return array $arrayOfNotifications
     */
    public function notifications(RedBean_OODBBean $bean, $sql = '', array $values = array(), $trash = true)
    {
        $all = R::related($bean, 'notification', $sql, $values);
        if ($trash) R::trashAll($all);
        return $all;
    }
}


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
 * Handles all belongings of your business model.
 *
 * Cinnebar models depend on RedBeanPHP as an ORM or Database Abstraction Layer. To learn more
 * about RedBeanPHP visit the RedBean website at {@link http://redbeanphp.com/}.
 *
 * To add your own models:
 * - Add a new file named after your model to the model directory
 * - Name that class after the scheme Model_*
 * - Your own model must extend this class
 * - Implement methods like dispense, update, etc. to react on RedBean Signals
 * - Implement any other method you need in your business model
 * - Do not instantiate your class, but instead R::dispense() or other RedBean methods are used
 * - RedBean will FUSE to your model while it provides persistence and db abstraction
 * - As an example see {@link Model_User}
 *
 * Here is an example:
 * <code>
 * 
 * require_once 'vendor/redbean/rb.php';
 * require_once 'model/user.php';
 * R::setup();
 * $p = R::dispense('person');
 * $p->first_name = 'John';
 * $p->last_name = 'Doe';
 * $name = $p->getName(); // this is a method you have implemented
 * R::store($p);
 * 
 * </code>
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Cinnebar_Model extends RedBean_SimpleModel
{
    /**
     * Defines the validation mode to throw an exception.
     */
    const VALIDATION_MODE_EXCEPTION = 1;

    /**
     * Defines the validation mode to store an valid or invalid state with the bean.
     */
    const VALIDATION_MODE_IMPLICIT = 2;

    /**
     * Defines the validation mode to store the valid or invalid state as a shared bean.
     */
    const VALIDATION_MODE_EXPLICIT = 4;
 
    /**
     * Switch to decide if this model will be automactially tagged or not.
     *
     * @var bool
     */
    private $auto_tag = false;
    
    /**
     * Switch to decide if this models history will be logged.
     *
     * @var bool
     */
    private $auto_info = false;

    /**
     * Contains errors of this model.
     *
     * @var array
     */
    protected $errors = array();
    
    /**
     * Holds the validation mode where 1 = Exception, 2 = Implicit attribute, 4 = Explicit which
     * effects all beans.
     *
     * @var int
     */
    protected static $validation_mode = self::VALIDATION_MODE_EXCEPTION;

    /**
     * Default template when rendering the model.
     *
     * @var string
     */
    private $template = 'default';
    
    /**
     * Container for list of callback validators.
     *
     * @var array
     */
    private $validators = array();
    
    /**
     * State of validation.
     *
     * @var bool
     */
    private $valid = true;
    
    /**
     * Container for a list of converters.
     *
     * @var array
     */
    private $converters = array();
    
    /**
     * Constructs a new model.
     *
     */
    public function __construct()
    {
    }
    
    /**
     * Returns a short text describing the bean for humans.
     *
     * @param Cinnebar_View $view
     * @return string
     */
    public function hitname(Cinnebar_View $view)
    {
        $template = '<a href="%s">%s</a>'."\n";
        return sprintf($template, $view->url(sprintf('/%s/edit/%d', $this->bean->getMeta('type'), $this->bean->getId())), $this->bean->getId());
    }
    
    /**
     * Returns a string where this bean was rendered into a model template.
     *
     * @param string $template
     * @param Cinnebar_View $view
     * @return string
     */
    public function render($template, Cinnebar_View $view)
    {
        return $view->partial(sprintf('model/%s/%s', $this->bean->getMeta('type'), $template), array('record' => $this->bean));
    }
    
    /**
     * Returns own(ed) beans that belong to this bean.
     *
     * @uses R
     * @param string $type
     * @param bool (optional) $add defaults to false
     * @return array $arrayOfOwnedBeans
     */
    public function own($type, $add = false)
    {
        $own_type = 'own'.ucfirst(strtolower($type));
        if (method_exists($this, 'get'.$own_type)) {
            $own_type = 'get'.$own_type;
            return $this->$own_type($add);
        }
        $own = $this->bean->$own_type;
        if ($add) $own[] = R::dispense($type);
        return $own;
    }
    
    /**
     * Returns shareded beans that belong to this bean.
     *
     * @uses R
     * @param string $type
     * @param bool (optional) $add defaults to false
     * @return array $arrayOfOwnedBeans
     */
    public function shared($type, $add = false)
    {
        $shared_type = 'shared'.ucfirst(strtolower($type));
        $shared = $this->bean->$shared_type;
        if ($add) $shared[] = R::dispense($type);
        return $shared;
    }
    
    /**
     * Returns wether the bean is mulilingual or not.
     *
     * @return bool
     */
    public function isI18n()
    {
        return false;
    }
    
    /**
     * Returns a i18n bean for this bean.
     *
     * A i18n bean means an internationalized version of a bean where the localizeable fields
     * are stored in a bean that extends the original beans name with the string 'i18n'.
     * If there is no i18n version for the asked language then the default language is
     * looked up and duplicated.
     *
     * @todo get rid of global language code
     *
     * @global $language
     * @global $config
     * @param mixed $iso code of the wanted translation language or null to use current language
     * @return RedBean_OODBBean $translation
     */
    public function i18n($iso = null)
    {
        global $language, $config;
        if ($iso === null && isset($_SESSION['backend']['language'])) {
            $iso = $_SESSION['backend']['language'];
        } elseif ($iso === null) {
            $iso = $language;
        }
        $i18n_type = $this->bean->getMeta('type').'i18n';
        if ( ! $i18n = R::findOne($i18n_type, $this->bean->getMeta('type').'_id = ? AND iso = ? LIMIT 1', array($this->bean->getId(), $iso))) {
            $i18n = R::dispense($i18n_type);
            $i18n->iso = $iso;
        }
        return $i18n;
    }
    
    /**
     * Returns an array with words splitters from a text.
     *
     * I found this regex on the web, but i can not remember where.
     *
     * @param string $text
     * @return array
     */
    public function splitToWords($text)
    {
    	return preg_split('/((^\p{P}+)|(\p{P}*\s+\p{P}*)|(\p{P}+$))/', $text, -1, PREG_SPLIT_NO_EMPTY);
    }
    
    /**
     * Returns only alphanumeric characters of the given string.
     *
     * @see http://stackoverflow.com/questions/5199133/function-to-return-only-alpha-numeric-characters-from-string
     *
     * @param string $text
     * @return string
     */
    public function alphanumericonly($text)
    {
        return preg_replace("/[^a-zA-Z0-9]+/", "", $text);
    }
    
    /**
     * Returns the content of an localized attribute.
     *
     * @param string $attribute
     * @param string (optional) $iso code of the language to translate to
     * @return string
     */
    public function translated($attribute, $iso = null)
    {
        return $this->i18n($iso)->$attribute;
    }
    
    /**
     * Returns the validation mode or sets it if optional parameter is given.
     *
     * @uses $validation_mode
     * @param int (optional) $mode the new validation mode
     * @return int $currentValidationMode
     */
    public function validationMode($mode = null)
    {
        if ($mode !== null) self::$validation_mode = $mode;
        return self::$validation_mode;
    }
    
    /**
     * Deletes the bean from the database.
     *
     * @return void
     */
    public function expunge()
    {
        R::trash($this->bean);
    }

    /**
     * This is called before the bean is updated.
     *
     * @uses validate()
     * @return void
     */
    public function update()
    {
        $this->convert();
        $this->validate();
    }
    
    /**
     * This is called after the bean was updated.
     *
     * @return void
     */
    public function after_update()
    {
        $this->info_workhorse();
        $this->tag_workhorse();
    }


    /**
     * Checks if the bean has a flag deleted which is true.
     *
     * @return bool
     */
    public function deleted()
    {
        return $this->bean->deleted;
    }
    
    /**
     * This is called when a bean was loaded.
     *
     * @return void
     */
    public function open()
    {
    }
    
    /**
     * This is called before a bean will be deleted.
     *
     * @return void
     */
    public function delete()
    {
    }
    
    /**
     * This is called after a bean has been deleted.
     *
     * @return void
     */
    public function after_delete()
    {
    }
    
    /**
     * This is called when a bean is dispended.
     *
     * This is the place where you would add validator callbacks or preset values in your model.
     *
     * @return void
     */
    public function dispense()
    {
    }
    
    /**
     * Sets the auto tag mode.
     *
     * @param int $flag
     * @return bool
     */
    public function setAutoTag($flag)
    {
        return $this->auto_tag = $flag;
    }

    /**
     * Returns the current auto tag flag.
     *
     * @return bool $autoTagOrNot
     */
    public function autoTag()
    {
        return $this->auto_tag;
    }

    /**
     * Sets the auto info mode.
     *
     * @param int $flag
     * @return bool
     */
    public function setAutoInfo($flag)
    {
        return $this->auto_info = $flag;
    }

    /**
     * Returns the current auto info flag.
     *
     * @return bool $autoInfoOrNot
     */
    public function autoInfo()
    {
        return $this->auto_info;
    }
    
	/**
	 * Returns an array with possible attributes for order clauses and such.
	 *
	 * @param string (optional) $layout defaults to table and can be of any value
	 * @return array
	 */
	public function attributes($layout = 'table')
	{
	    switch ($layout) {
	        default:
    	        $ret = array(
        			array(
        				'attribute' => 'id',
        				'orderclause' => 'id',
        				'class' => 'number',
        				'filter' => array(
        				    'type' => 'number'
        				)
        			)
        		);
	    }
        return $ret;
	}
	
	/**
	 * Returns an array of the bean.
	 *
	 * @param bool $header defaults to false, if true then column headers are returned
	 * @return array
	 */
	public function exportToCSV($header = false)
	{
	    if ($header === true) {
	        return array(
	        );
	    }
        return $this->bean->export();
	}
	
    /**
     * Returns a message string for an action on this bean.
     *
     * @param string (optional) $action defaults to 'idle'
     * @param string $type may be 'success', 'failure' or whatever you fancy, defaults to 'success'
     * @param RedBean_OODBBean (optional) $user
     * @return string $message
     */
    public function actionAsHumanText($action = 'idle', $type = 'success', $user = null)
    {
        $subject = __('you');
        if ( is_a($user, 'RedBean_OODBBean')) $subject = $user->name();
        return __('action_'.$action.'_on_'.$this->bean->getMeta('type').'_'.$type, array($subject));
    }
    
    /**
     * Returns an array with possible actions for scaffolding.
     *
     * @param array (optional) $presetActions
     * @return array
     */
    public function makeActions(array $actions = array())
    {
        return $actions;
    }
	
	/**
	 * Returns a menu object.
	 *
	 * Overwrite this method in your models to achieve a custom menu for any bean you want.
	 *
	 * @param string $action
	 * @param Cinnebar_View $view
	 * @param Cinnebar_Menu (optional) $menu
	 * @return Cinnebar_Menu
	 */
	public function makeMenu($action, Cinnebar_View $view, Cinnebar_Menu $menu = null)
	{
        $menu = new Cinnebar_Menu();
        $layouts = $this->layouts();
        if (count($layouts) > 1) {
            foreach ($layouts as $layout) {
                $menu->add(__('layout_'.$layout), $view->url(sprintf('/%s/index/%d/%d/%s/%d/%d', $this->bean->getMeta('type'), 1, Controller_Scaffold::LIMIT, $layout, $view->order, $view->dir)), 'scaffold-layout');
            }
        }
        $menu->add(__('scaffold_add'), $view->url(sprintf('/%s/add', $this->bean->getMeta('type'))), 'scaffold-add');
        $menu->add(__('scaffold_browse'), $view->url(sprintf('/%s/index', $this->bean->getMeta('type'))), 'scaffold-browse');
        return $menu;
	}
	
	/**
	 * Returns an array with possible layout for list view (index).
	 *
	 * @return array
	 */
	public function layouts()
	{
        return array('table');
	}
	
    /**
     * Returns SQL for total of all beans.
     *
     * @todo why sqlForThis and sqlForThat, unify!
     *
     * @uses R
     * @param string $where_clause
     * @return string $SQL
     */
    public function sqlForTotal($where_clause = '1')
    {
		$sql = <<<SQL
		SELECT
			COUNT(DISTINCT({$this->bean->getMeta('type')}.id)) as total
		FROM
			{$this->bean->getMeta('type')}

		WHERE {$where_clause}
SQL;
        return $sql;
    }
	
    /**
     * Returns SQL for filtering these beans.
     *
     * @uses R
     * @param string $where_clause
     * @param string $order_clause
     * @param int $offset
     * @param int $limit
     * @return string $SQL
     */
    public function sqlForFilters($where_clause = '1', $order_clause = 'id', $offset = 0, $limit = 1)
    {
		$sql = <<<SQL
		SELECT
            DISTINCT(id)  

		FROM
			{$this->bean->getMeta('type')}

		WHERE {$where_clause}

		ORDER BY {$order_clause}

		LIMIT {$offset}, {$limit}
SQL;
        return $sql;
    }
    
    /**
     * Returns array with strings or empty array.
     *
     * You must implement this method into your own model so that it returns keywords if you
     * want to auto tag your beans. Do not forget to setAutoTag(true) or no tags will be added.
     *
     * This is how an implementation in your own class could look like:
     * <code>
     * 
     * public funtion keywords() {
     *    $keywords = array(
     *        $this->bean->firstname,
     *        $this->bean->lastname
     *    );
     *    $keywords = array_merge($keywords, $this->split_text_into_words($this->long_desc));
     *    return $keywords;
     * }
     * 
     * </code>
     *
     * @return array
     */
    public function keywords()
    {
        return array();
    }
    
    /**
     * Searches for given searchterm within bean and returns the result-set as an multi-dim array
     * after the given layout.
     *
     * @param string $term contains the searchterm
     * @param string (optional) $layout defaults to "default"
     */
    public function clairvoyant($term, $layout = 'default')
    {
        $result = R::getAll(sprintf('select id as id, id as label, id as value from %s', $this->bean->getMeta('type')));
        return $result;
    }
    
    /**
     * Adds an error to the general errors or to a certain attribute if the optional parameter is set.
     *
     * @param string $errorText
     * @param string (optional) $attribute
     * @return void
     */
    public function addError($errorText, $attribute = '')
    {
        $this->errors[$attribute][] = $errorText;
    }

    /**
     * Sets the complete errors array at once.
     *
     * @param array $errors
     */
    public function setErrors(array $errors = array())
    {
        $this->errors = $errors;
    }

    /**
     * Returns the errors of this model.
     *
     * @return array $errors
     */
    public function errors()
    {
        return $this->errors;
    }
    
    /**
     * Returns the latest info bean of this bean.
     *
     * This uses a SQL query instead of R::relatedOne() because that was darn slow when
     * a bean has a lot of related info beans.
     *
     * @return RedBean_OODBBean $info
     */
    public function info()
    {
        if ( ! $this->autoInfo()) return R::dispense('info');
        if ( ! $this->bean->getId()) return R::dispense('info');
        try {
            $relation = array($this->bean->getMeta('type'), 'info');
            asort($relation); // because RB orders the table names
            $info_relation = implode('_', $relation);
            $bean_id_column = $this->bean->getMeta('type').'_id';
    		$sql = <<<SQL
    		SELECT
    			info.id AS info_id
    		FROM
    			{$this->bean->getMeta('type')}
		
    		LEFT JOIN {$info_relation} AS rinfo ON rinfo.{$bean_id_column} = {$this->bean->getMeta('type')}.id
    		LEFT JOIN info ON rinfo.info_id = info.id

    		WHERE
    		    {$this->bean->getMeta('type')}.id = ?
    		ORDER BY
    		    info.stamp DESC
    		LIMIT 1
SQL;
            $info_id = R::getCell($sql, array($this->bean->getId()));
        } catch (Exception $e) {
            Cinnebar_Logger::instance()->log($e, 'exceptions');
        }
        $info = R::load('info', $info_id);
    	if ( ! $info->getId()) {
    		$info = R::dispense('info');
    	}
    	return $info;
    }
    
    /**
     * Import data from csv array using a import map(per).
     *
     * @todo use a "splitter" to refer to a "nested" bean, e.g. optin: email and person.lastname
     *
     * @param RedBean_OODBBean $import is the import bean
     * @param array $data is an array of csv records
     * @param array $mappers is an array of map beans
     * @return void
     */
    public function csvImport(RedBean_OODBBean $import, array $data, array $mappers)
    {
        foreach ($mappers as $id=>$map) {
            if ($map->target == '__none__') continue; // we skip unsoliciated ?? import fields
            if ( empty($data[$map->source]) && ! empty($map->default)) {
                $this->bean->{$map->target} = $map->default;
            } else {
                $this->bean->{$map->target} = $data[$map->source];
            }
        }
    }
    
    /**
     * Returns wether the bean is invalid or not.
     *
     * A bean may have the attribute invalid if it ever has been used together with
     * validation mode implicit and validation a been failed.
     *
     * @return bool
     */
    public function invalid()
    {
        if ( isset($this->bean->invalid) && $this->bean->invalid) return true;
        return false;
    }
    
    /**
     * Returns the meta bean of this bean.
     *
     * @return RedBean_OODBBean $meta
     */
    public function meta()
    {
        if ( ! $this->bean->meta) $this->bean->meta = R::dispense('meta');
    	return $this->bean->meta;
    }
    
    /**
     * Returns the parent bean of this bean or an empty bean of same type if there is no parent.
     *
     * @uses R::load()
     * @return RedBean_OODBBean
     */
    public function parent()
    {
        $fn_parent = $this->bean->getMeta('type').'_id';
        if ( ! $this->bean->$fn_parent) return R::dispense($this->bean->getMeta('type'));
        return R::load($this->bean->getMeta('type'), $this->bean->$fn_parent);
    }
    
    /**
     * Returns an array of beans that are subordinated to this bean, aka children.
     *
     * @param string $orderfields
     * @param string $criteria
     * @return array $itemsFoundOrEmptyArray
     */
    public function children($orderfields = 'id', $criteria = null)
    {
        $fn_parent = $this->bean->getMeta('type').'_id';
        return R::find($this->bean->getMeta('type'), sprintf('%s = ? %s ORDER BY %s', $fn_parent, $criteria, $orderfields), array($this->bean->getId()));
    }
    
    /**
     * Returns the contents of an attribute from either this bean or the next bean up the tree.
     *
     * There is a check if this bean has attribute set. If so, that attribute will be returned.
     * Otherwise it starts to bubble up in the tree and looks for that attribute being set in
     * the next bean up in the tree. If there are no more parents and the attribute is still not
     * set NULL is returned.
     *
     * @uses bubble()
     * @uses R::load()
     * @param string $attribute
     * @return mixed
     */
    public function bubble($attribute)
    {
        $fn_parent = $this->bean->getMeta('type').'_id';
        if ( ! $this->bean->$fn_parent) return $this->bean->$attribute;
        if ($this->bean->$attribute) return $this->bean->$attribute;
        $parent = R::load($this->bean->getMeta('type'), $this->bean->$fn_parent);
        if ( ! $parent->getId()) return null;
        return $parent->bubble($attribute);
    }

    /**
     * Returns true if model has errors.
     *
     * If the optional parameter is set a certain attribute is tested for having an error or not.
     *
     * @uses Cinnebar_Model::$errors
     * @param string (optional) $attribute
     * @return bool $hasErrorOrHasNoError
     */
    public function hasError($attribute = '')
    {
        if ($attribute === '') return ! empty($this->errors);
        return isset($this->errors[$attribute]);
    }

    /**
     * Alias for {@link hasError()} call without an special attribute.
     *
     * @return bool $hasErrorsOrNone
     */
    public function hasErrors()
    {
        return $this->hasError();
    }
    
    /**
     * Returns an array with errors.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
    
    /**
     * Calls the converters of this bean.
     */
    public function convert()
    {
        if (empty($this->converters)) return;
        foreach ($this->converters as $attribute=>$callbacks) {
            foreach ($callbacks as $n=>$param) {
                $converter_name = 'Converter_'.ucfirst(strtolower($param['converter']));
                $converter = new $converter_name($this->bean, $param['options']);
                $this->bean->$attribute = $converter->execute($this->bean->$attribute);
            }
        }
    }
    
    /**
     * Adds a converter callback name for an attribute.
     *
     * @uses Cinnebar_Model::$__converters
     * @param string $attribute
     * @param string $converter
     * @param array (optional) $options
     * @return void
     */
    public function addConverter($attribute, $converter, array $options = array())
    {
        $this->converters[$attribute][] = array(
            'converter' => $converter,
            'options' => $options
        );
    }
    
    /**
     * Validates this model and returns the result or throws an exception if invalid.
     *
     * Wether a exception is thrown or the validation result is returned depends of the
     * validation mode set.
     *
     * @todo Implement validation mode explicit to actually store a shared bean
     *
     * @uses Cinnebar_Model::workhorse_validate()
     * @return bool $validOrInvalid
     * @throws Exception in case the validation mode is set to do so
     */
    public function validate()
    {
        if (isset($this->invalid) && $this->invalid) $this->invalid = false;
        if ($valid = $this->validate_workhorse()) return true;
        if (self::VALIDATION_MODE_EXCEPTION === self::$validation_mode) {
            throw new Exception(__CLASS__.'_invalid: '.$this->bean->getMeta('type'));
        }
        if (self::VALIDATION_MODE_IMPLICIT === self::$validation_mode) {
            $this->invalid = true;
        }
        return false;
    }
    
    /**
     * Adds a validator callback name for an attribute.
     *
     * @uses Cinnebar_Model::$__validators
     * @param string $attribute
     * @param string $validator
     * @param array (optional) $options
     * @return void
     */
    public function addValidator($attribute, $validator, array $options = array())
    {
        $this->validators[$attribute][] = array(
            'validator' => $validator,
            'options' => $options
        );
    }
    
    /**
     * Loop through all validator callbacks and returns the state of validation.
     *
     * If a validator fails an error is added for that attribute. All validators are executed,
     * so you have a complete state of validation afterwards.
     * @uses Cinnebar_Validator
     * @return bool $validOrInvalid
     */
    protected function validate_workhorse()
    {
        if (empty($this->validators)) return true;
        $state = true;
        foreach ($this->validators as $attribute=>$callbacks) {
            foreach ($callbacks as $n=>$param) {
                $validator_name = 'Validator_'.ucfirst(strtolower($param['validator']));
                $validator = new $validator_name($param['options']);
                if ( ! $validator->execute($this->bean->$attribute)) {
                    $state = false;
                    $this->addError(sprintf('%s_invalid', strtolower($param['validator'])), $attribute);
                }
            }
        }
        return $state;
    }
    
    /**
     * If auto info is true a history entry will be added to this bean.
     *
     * If there is a current user with a valid session that guy is linked as a user, otherwise
     * the user relation of the auto info bean is NULL.
     *
     * @return bool $autoInfoAssciatedOrNot
     */
    protected function info_workhorse()
    {
        if ( ! $this->autoInfo()) return false;
        if ( ! $this->bean->getId()) return false;
        $info = R::dispense('info');
        $user = R::dispense('user')->current();
        if ($user->getId()) $info->user = $user;
        $info->stamp = time();
        try {
            R::store($info);
            R::associate($this->bean, $info);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * If auto tag is true a all keywords of this bean will be added as tags.
     *
     * @uses keywords()
     * @return bool $autoTaggedOrNot
     */
    protected function tag_workhorse()
    {
        if ( ! $this->autoTag()) return false;
        if ( ! $this->bean->getId()) return false;
        $tags = array();
        foreach ($this->keywords() as $n=>$keyword) {
            if (empty($keyword)) continue;
            $tags[] = $keyword;
        }
        try {
            R::tag($this->bean, $tags);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}


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
 * Manages setting.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Setting extends Cinnebar_Model
{
    /**
     * Returns a domain bean aliased as blessedfolder.
     *
     * @return RedBean_OODBean
     */
    public function blessedfolder()
    {
        if ( ! $this->bean->fetchAs('domain')->blessedfolder) $this->bean->blessedfolder = R::dispense('domain');
        return $this->bean->blessedfolder;
    }
    
    /**
     * Returns a pricetype bean aliased as feebase.
     *
     * @return RedBean_OODBean
     */
    public function feebase()
    {
        if ( ! $this->bean->fetchAs('pricetype')->feebase) $this->bean->feebase = R::dispense('pricetype');
        return $this->bean->feebase;
    }

	/**
	 * Returns an array with possible attributes for order clauses and such.
	 *
	 * @param string (optional) $layout defaults to table and can be of any value
	 * @return array
	 */
	public function attributes($layout = 'table')
	{
	    switch ($layout) {
	        default:
        		$ret = array(
        			array(
        				'attribute' => 'id',
        				'orderclause' => 'id',
        				'class' => 'number'
        			)
        		);
        }
        return $ret;
	}
	
	/**
	 * Returns an array with possible layout for list view (index).
	 *
	 * @return array
	 */
	public function layouts()
	{
        return array();
	}

    /**
     * Returns keywords from this bean for tagging.
     *
     * @var array
     */
    public function keywords()
    {
        return array(
        );
    }
    
    /**
     * update.
     */
    public function update()
    {
        if ($this->bean->loadexchangerates) {
            $now = time();
            if ( ! R::dispense('currency')->loadExchangeRates($now)) {
                throw new Exception('failed to load exchange rates');
            }
            $this->bean->tsexchangerate = $now;
        }
        parent::update();
    }

    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        $this->addValidator('blessedfolder', 'hasvalue');
        $this->addValidator('feebase', 'hasvalue');
        $this->setAutoInfo(true);
    }
}


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
 * Manages countries.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Country extends Cinnebar_Model
{
    /**
     * Searches for given searchterm within bean and returns the result-set as an multi-dim array
     * after the given layout.
     *
     * @param string $term contains the searchterm as given by jQuery.autocomplete
     * @param string (optional) $layout defaults to "default"
     * @return array
     */
    public function clairvoyant($term, $layout = 'default')
    {   
        switch ($layout) {
            default:
                $sql = <<<SQL

                SELECT
                    country.id AS id,
                    country.name AS label,
                    country.iso AS iso

                FROM
                    country

                WHERE
                    name like ?

                ORDER BY
                    name

SQL;
        }
        return $res = R::getAll($sql, array($term.'%'));
    }

    /**
     * Returns SQL for filtering these beans.
     *
     * @uses R
     * @param string $where_clause
     * @param string $order_clause
     * @param int $offset
     * @param int $limit
     * @return string $SQL
     */
    public function sqlForFilters($where_clause = '1', $order_clause = 'id', $offset = 0, $limit = 1)
    {
		$sql = <<<SQL
		SELECT
			DISTINCT(country.id) as id  

		FROM
			country

		WHERE {$where_clause}

		ORDER BY {$order_clause}

		LIMIT {$offset}, {$limit}
SQL;
        return $sql;
    }

	/**
	 * Returns an array with possible attributes for order clauses and such.
	 *
	 * @param string (optional) $layout defaults to table and can be of any value
	 * @return array
	 */
	public function attributes($layout = 'table')
	{
	    switch ($layout) {
	        default:
        		$ret = array(
        			array(
        				'attribute' => 'iso',
        				'orderclause' => 'iso',
        				'class' => 'text'
        			),
        			array(
        				'attribute' => 'name',
        				'orderclause' => 'name',
        				'class' => 'text'
        			),
        			array(
        				'attribute' => 'enabled',
        				'orderclause' => 'enabled',
        				'class' => 'bool',
        				'viewhelper' => 'bool'
        			)
        		);
        }
        return $ret;
	}
	
	/**
	 * Returns a customized menu.
	 *
	 * @param string $action
	 * @param Cinnebar_View $view
 	 * @param Cinnebar_Menu (optional) $menu
 	 * @return Cinnebar_Menu
 	 */
 	public function makeMenu($action, Cinnebar_View $view, Cinnebar_Menu $menu = null)
	{
        $menu = parent::makeMenu($action, $view, $menu);
        return $menu;
	}
	
	/**
	 * Returns an array with possible layout for list view (index).
	 *
	 * @return array
	 */
	public function layouts()
	{
        return array('table');
	}

    /**
     * Returns keywords from this bean for tagging.
     *
     * @var array
     */
    public function keywords()
    {
        return array(
            $this->bean->iso,
            $this->bean->name
        );
    }

    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        //$this->bean->setMeta('buildcommand.unique', array(array('iso')));
        //$this->setAutoTag(true);
        $this->addValidator('iso', 'hasvalue');
        $this->addValidator('iso', 'isunique', array('bean' => $this->bean, 'attribute' => 'iso'));
    }
}


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
 * Manages tags on other beans.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Tag extends Cinnebar_Model
{
    /**
     * Searches for given searchterm within bean and returns the result-set as an multi-dim array
     * after the given layout.
     *
     * @param string $term contains the searchterm as given by jQuery.autocomplete
     * @param string (optional) $layout defaults to "default"
     */
    public function clairvoyant($term, $layout = 'default')
    {   
        switch ($layout) {
            default:
                $sql = <<<SQL

                SELECT
                    id AS id,
                    title AS label

                FROM
                    tag

                WHERE
                    title like ?

                ORDER BY
                    title

SQL;
        }
        return $res = R::getAll($sql, array($term.'%'));
    }
}


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
 * Manages information on other beans.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Info extends Cinnebar_Model
{
    /**
     * Returns a user bean (might be empty).
     *
     * @return RedBean_OODBBean $user
     */
    public function user()
    {
        if ( ! $this->bean->user) return R::dispense('user');
        return $this->bean->user;
    }
    
    /**
     * dispense.
     */
    public function dispense()
    {
        $this->action = 'edit';
    }
}


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
 * Manages translateable tokens.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Token extends Cinnebar_Model implements iToken
{
    /**
     * Returns the payload of the translation in current language or an empty string.
     *
     * @param string $attribute
     * @return string
     */
    public function translated($attribute)
    {
        return $this->in()->$attribute;
    }
    
    /**
     * Creates a new token or updates an existing one.
     *
     * @param string $name of the token
     * @param array $translations
     * @return bool
     */
    public function createOrUpdate($name, $translations = array())
    {
        if ( ! $token = R::findOne('token', ' name = ? LIMIT 1', array($name))) {
            $token = R::dispense('token');
            $token->name = $name;
        }
        
        $trans = R::dispense('translation', count($translations));
        foreach ($translations as $i => $translation) {
            $trans[$i]->iso = $translation['iso'];
            $trans[$i]->payload = $translation['payload'];
        }
        $token->ownTranslation = $trans;
        try {
            R::store($token);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Returns a translation of the token for the given language.
     *
     * @todo get rid of global language code
     * @todo get rid of this method in favor of Cinnebar_Model::i18n()
     *
     * @param mixed $iso code of the wanted translation language or null to use current language
     * @return RedBean_OODBBean $translation
     */
    public function in($iso = null)
    {
        global $language;
        if ($iso === null) $iso = $language;
        if ( ! $translation = R::findOne('translation', ' token_id = ? AND iso = ? LIMIT 1', array($this->bean->getId(), $iso))) {
            $translation = R::dispense('translation');
            $translation->iso = $iso;
            $translation->payload = $this->bean->name;
        }
        return $translation;
    }

    /**
     * Returns wether the bean is mulilingual or not.
     *
     * Attention: For a token bean this will return false. This is because a token does not
     * use the *i18n bean but instead it uses the translation bean to store mulitlingual content.
     *
     * @return bool
     */
    public function isI18n()
    {
        return false;
    }

    /**
     * Returns SQL for fetch the total of all beans.
     *
     * @todo get rid of global language code
     *
     * @uses R
     * @param string $where_clause
     * @return string $SQL
     */
    public function sqlForTotal($where_clause = '1')
    {
        global $language; /* Oh, how i loathe globals */
		$sql = <<<SQL
		SELECT
			COUNT(DISTINCT(token.id)) as total
		FROM
			token

		LEFT JOIN
		    translation ON translation.token_id = token.id AND translation.iso = '{$language}'

		WHERE {$where_clause}
SQL;
        return $sql;
    }
    
    /**
     * Returns SQL for filtering these beans.
     *
     * @todo get rid of global language code
     *
     * @uses R
     * @param string $where_clause
     * @param string $order_clause
     * @param int $offset
     * @param int $limit
     * @return string $SQL
     */
    public function sqlForFilters($where_clause = '1', $order_clause = 'id', $offset = 0, $limit = 1)
    {
        global $language; /* Oh, how i loathe globals */
		$sql = <<<SQL
		SELECT
			DISTINCT(token.id) as id
		FROM
			token
			
		LEFT JOIN
		    translation ON translation.token_id = token.id AND translation.iso = '{$language}'

		WHERE {$where_clause}

		ORDER BY {$order_clause}

		LIMIT {$offset}, {$limit}
SQL;
        return $sql;
    }
    
	/**
	 * Returns an array with possible attributes for order clauses and such.
	 *
	 * @param string (optional) $layout defaults to table and can be of any value
	 * @return array
	 */
	public function attributes($layout = 'table')
	{
	    switch ($layout) {
	        default:
        		$ret = array(
        			array(
        				'attribute' => 'name',
        				'orderclause' => 'token.name',
        				'class' => 'text',
        				'filter' => array(
        				    'tag' => 'text'
        				)
        			),
        			array(
        				'attribute' => 'payload',
        				'orderclause' => 'translation.payload',
        				'class' => 'text',
        				'callback' => array(
        				    'name' => 'translated'
        				),
        				'filter' => array(
        				    'tag' => 'text'
        				)
        			)
        		);
        }
        return $ret;
	}
	
	/**
	 * Returns a customized menu.
	 *
	 * @param string $action
	 * @param Cinnebar_View $view
 	 * @param Cinnebar_Menu (optional) $menu
 	 * @return Cinnebar_Menu
 	 */
 	public function makeMenu($action, Cinnebar_View $view, Cinnebar_Menu $menu = null)
	{
        $menu = parent::makeMenu($action, $view, $menu);
        return $menu;
	}
	
	/**
	 * Returns an array with possible layout for list view (index).
	 *
	 * @return array
	 */
	public function layouts()
	{
        return array('table');
	}

    /**
     * Returns keywords from this bean for tagging.
     *
     * @var array
     */
    public function keywords()
    {
        return array(
            $this->bean->name
        );
    }

    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        //$this->bean->setMeta('buildcommand.unique', array(array('name')));
        //$this->setAutoTag(true);
        $this->setAutoInfo(true);
        $this->addValidator('name', 'hasvalue');
        $this->addValidator('name', 'isunique', array('bean' => $this->bean, 'attribute' => 'name'));
    }
}


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
 * Manages languages.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Language extends Cinnebar_Model implements iLanguage
{
    /**
     * Returns SQL for filtering these beans.
     *
     * @uses R
     * @param string $where_clause
     * @param string $order_clause
     * @param int $offset
     * @param int $limit
     * @return string $SQL
     */
    public function sqlForFilters($where_clause = '1', $order_clause = 'id', $offset = 0, $limit = 1)
    {
		$sql = <<<SQL
		SELECT
			DISTINCT(language.id) as id  

		FROM
			language

		WHERE {$where_clause}

		ORDER BY {$order_clause}

		LIMIT {$offset}, {$limit}
SQL;
        return $sql;
    }

	/**
	 * Returns an array with possible attributes for order clauses and such.
	 *
	 * @param string (optional) $layout defaults to table and can be of any value
	 * @return array
	 */
	public function attributes($layout = 'table')
	{
	    switch ($layout) {
	        default:
        		$ret = array(
        			array(
        				'attribute' => 'iso',
        				'orderclause' => 'iso',
        				'class' => 'text'
        			),
        			array(
        				'attribute' => 'name',
        				'orderclause' => 'name',
        				'class' => 'text'
        			),
        			array(
        				'attribute' => 'enabled',
        				'orderclause' => 'enabled',
        				'class' => 'bool',
        				'viewhelper' => 'bool'
        			)
        		);
        }
        return $ret;
	}
	
	/**
	 * Returns a customized menu.
	 *
	 * @param string $action
	 * @param Cinnebar_View $view
 	 * @param Cinnebar_Menu (optional) $menu
 	 * @return Cinnebar_Menu
 	 */
 	public function makeMenu($action, Cinnebar_View $view, Cinnebar_Menu $menu = null)
	{
        $menu = parent::makeMenu($action, $view, $menu);
        return $menu;
	}
	
	/**
	 * Returns an array with possible layout for list view (index).
	 *
	 * @return array
	 */
	public function layouts()
	{
        return array('table');
	}

    /**
     * Returns enabled languages.
     *
     * @return array
     */
    public function enabled()
    {
        return R::find('language', ' enabled = ? ORDER BY iso', array(1));
    }

    /**
     * Returns keywords from this bean for tagging.
     *
     * @var array
     */
    public function keywords()
    {
        return array(
            $this->bean->iso,
            $this->bean->name
        );
    }

    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        //$this->bean->setMeta('buildcommand.unique', array(array('iso')));
        //$this->setAutoTag(true);
        $this->addValidator('iso', 'hasvalue');
        $this->addValidator('iso', 'isunique', array('bean' => $this->bean, 'attribute' => 'iso'));
    }
}


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
 * The user model manages user accounts of your application.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_User extends Cinnebar_Model
{
    /**
     * Defines the guest user.
     *
     * @var array
     */
    private $unknown_user = array(
        'nickname' => 'Nobody',
        'name' => 'Nobody',
        'email' => 'nobody@example.com',
        'pw' => 'secret',
        'home' => '/home',
        'admin' => false
    );

    /**
     * Holds an instance of a password hashing class.
     *
     * @var mixed
     */
    private $hasher;

    /**
     * Constructor.
     *
     * Depending on your configuration either phpass or {@link Cinnebar_Hash} will be used as
     * an hashing algorithm.
     *
     * @todo Refactor code to get rid of the global stuff and the implicit instanciating of hash class
     * @see $user_phpass to learn what can be configured
     * @uses parent::__construct()
     * @uses PasswordHash() if phpass is configured
     * @uses Cinnebar_Hash() if phpass is turned down
     */
    public function __construct()
    {
        parent::__construct();
        global $config;
        if (isset($config['user']['phpass']) && $config['user']['phpass']) {
            require_once BASEDIR.'/vendors/phpass/PasswordHash.php';
            $this->hasher = new PasswordHash(8, false);
        } else {
            $this->hasher = new Cinnebar_Hash();
        }
    }
    
    /**
     * Returns wether the bean is mulilingual or not.
     *
     * @return bool
     */
    public function isI18n()
    {
        return true;
    }
    
    /**
     * Returns wether the use has a certain role or not.
     *
     * @param string $role name
     * @return bool
     */
    public function hasRole($role)
    {
        if ( ! $role = R::findOne('role', ' name = ? LIMIT 1', array($role))) return false;
        return R::areRelated($this->bean, $role);
    }
    
    /**
     * Returns an array of active user beans which belong to a certain role.
     *
     * @param mixed $role is either a int or an array of integers
     * @return array
     */
    public function belongsToRole($roles)
    {
        if ( ! is_array($roles)) $roles = array($roles);
        $sql = <<<SQL
		SELECT
			user.id as id  

		FROM
			role_user
		
		LEFT JOIN user ON user.id = role_user.user_id

		WHERE
		    role_user.role_id IN (%s) AND user.deleted = 0 AND user.banned = 0

		ORDER BY user.name
SQL;
        $sql = sprintf($sql, R::genSlots($roles));
        //R::debug(true);
        $assoc = R::$adapter->getAssoc($sql, $roles);
        //R::debug(false);
        return R::batch('user', array_keys($assoc));
    }
    
    /**
     * Returns the current iso language code for backend activities.
     *
     * @return string
     */
    public function language()
    {
        return $_SESSION['backend']['language'];
    }

    /**
     * Returns the current user bean or an (empty) guest user.
     *
     * <b>Attention</b>: A session must have been started.
     *
     * @uses $_SESSION['user']['id'] to determine the current user id
     * @return RedBean_OODBBean
     */
    public function current()
    {
        if ( ! isset($_SESSION['user']['id'])) {
            $this->bean->import($this->unknown_user);
            return $this->bean;
        }
        return $this->bean = R::load('user', $_SESSION['user']['id']);
    }
    
    /**
     * Adds a notification message to this user.
     *
     * @uses R
     * @uses Model_Notification
     * @param string $message
     * @param string (optional) $template Name of the template
     * @return bool $notificationAddedOrNot
     */
    public function notify($message, $template = 'info')
    {
        $notification = R::dispense('notification');
        $notification->payload = $message;
        $notification->template = $template;
        $notification->once = true;
        try {
            R::associate($this->bean, $notification);
            R::store($notification);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Returns an array with this users notifications and dismisses them.
     *
     * @param string $sql optional $sql
     * @param array $values optional array with values for the sql jokers
     * @return array $arrayOfNotifications
     */
    public function notifications($sql = '', array $values = array())
    {
        $all = R::related($this->bean, 'notification', $sql, $values);
        R::trashAll($all);
        return $all;
    }
    
    /**
     * Returns the users email-address or another user attribute working as a user name.
     *
     * If a 'screenname' setting exists for this user it will be used otherwise this will return the
     * users e-mail address.
     *
     * @uses getSetting() to retrieve a setting bean for 'screenname'
     * @return string $nameOrEmailAddressOrScreenname
     */
    public function name()
    {
        return $this->bean->email;
    }

    /**
     * Returns array with strings or empty array.
     *
     * @return array
     */
    public function keywords()
    {
        return array(
            $this->bean->email,
            $this->bean->name
        );
    }
    
    /**
     * Returns the users homepage or URL given in the optional parameter.
     *
     * @param string (optional) $gotoURL If given this is the prefered home URL
     * @return string $homeURL
     */
    public function home($goto = '')
    {
        if ($goto) return $goto;
        return $this->bean->home;
    }
    
    /**
     * This is called before a user bean is updated.
     *
     * If this bean has never been stored the password is hashed. If you want to change
     * password later on you must use changePassword().
     *
     * @uses PasswordHash::HashPassword() is used to hash password on creation
     * @uses parent::update()
     */
    public function update()
    {
        if ( ! $this->bean->getId()) {
            $this->bean->pw = $this->hasher->HashPassword($this->bean->pw);
        }
        $this->bean->ego = md5($this->bean->email);
        parent::update();
    }
    
    /**
     * Define validators and set auto info to true.
     */
    public function dispense()
    {
        //$this->bean->setMeta('buildcommand.unique', array(array('nickname')));
        //$this->bean->setMeta('buildcommand.unique', array(array('email')));
        $this->addValidator('email', 'isemail');
        $this->addValidator('email', 'isunique', array('bean' => $this->bean, 'attribute' => 'email'));
        $this->addValidator('name', 'hasvalue');
        $this->addValidator('name', 'isunique', array('bean' => $this->bean, 'attribute' => 'name'));
        $this->setAutoInfo(true);
    }
    
    /**
     * Changes the password if old password is good and new one matches repetition.
     *
     * @uses PasswordHash::CheckPassword() to compare the users password with the one given
     * @uses PasswordHash::HashPassword() to hash the new password
     * @param string $password The currently active password
     * @param string $new The new password
     * @param string $repeated The new password, repeated for safety
     * @return bool
     */
    public function changePassword($password, $new, $repeated)
    {
        if ( ! trim($new)) {
            $this->addError('error_password_cant_be_empty', 'pw');
            return false;
        }
        if ($new != $repeated) {
            $this->addError('error_passwords_do_not_match', 'pw_new');
            $this->addError('error_passwords_do_not_match', 'pw_repeated');
            return false;
        }
        if ( ! $this->hasher->CheckPassword($password, $this->bean->pw)) {
            $this->addError('error_password_wrong', 'pw');
            return false;
        };
        $this->bean->pw = $this->hasher->HashPassword($new);
        return true;
    }

    /**
     * Returns true if a user was found and the passwords match and the account is not banned.
     *
     * If a user account was found, the passwords match and the account is not banned
     * the user bean is loaded into the model.
     *
     * @uses PasswordHash::CheckPassword()
     * @param string $name Either the users e-mail address or the users nickname
     * @param string $password
     * @return mixed false if no user qualified or the user bean if one was logged
     */
    public function login($name, $password)
    {
        if ( ! $user = R::findOne('user', ' email=:name OR name=:name LIMIT 1', array(':name' => $name))) {
            $this->addError('error_no_user_found', 'name');
            return false;
        }
        if ( ! $this->hasher->CheckPassword($password, $user->pw)) {
            $this->addError('error_wrong_password', 'pw');
            return false;
        }
        if ($user->deleted()) {
            $this->addError('error_user_deleted');
            return false;
        }
        if ($user->banned()) {
            $this->addError('error_user_banned');
            return false;
        }
        return $user;
    }
    
    /**
     * Returns wether the user account is deleted or not.
     *
     * @return bool
     */
    public function deleted()
    {
        if ($this->bean->deleted) return true;
        return false;
    }
    
    /**
     * Returns wether the user account is banned or not.
     *
     * @return bool
     */
    public function banned()
    {
        if ($this->bean->banned) return true;
        return false;
    }
    
    /**
     * Logs out this user and unsets the current user id in the session.
     *
     * @return bool $loggedOutOrNot
     */
    public function logout()
    {
        $this->bean->sid = null;
        return true;
    }
    
    /**
     * Returns SQL for filtering these beans.
     *
     * @uses R
     * @param string $where_clause
     * @param string $order_clause
     * @param int $offset
     * @param int $limit
     * @return string $SQL
     */
    public function sqlForFilters($where_clause = '1', $order_clause = 'id', $offset = 0, $limit = 1)
    {
		$sql = <<<SQL
		SELECT
			DISTINCT(user.id) as id  

		FROM
			user

		WHERE {$where_clause}

		ORDER BY {$order_clause}

		LIMIT {$offset}, {$limit}
SQL;
        return $sql;
    }

	/**
	 * Returns an array with possible attributes for order clauses and such.
	 *
	 * @param string (optional) $layout defaults to table and can be of any value
	 * @return array
	 */
	public function attributes($layout = 'table')
	{
	    switch ($layout) {
	        default:
        		$ret = array(
        			array(
        				'attribute' => 'email',
        				'orderclause' => 'email',
        				'class' => 'text',
        				'filter' => array(
        				    'tag' => 'text'
                        )
        			),
        			array(
        				'attribute' => 'name',
        				'orderclause' => 'name',
        				'class' => 'text',
        				'filter' => array(
        				    'tag' => 'text'
                        )
        			),
        			array(
        				'attribute' => 'admin',
        				'orderclause' => 'admin',
        				'class' => 'bool',
        				'viewhelper' => 'bool',
        				'filter' => array(
        				    'tag' => 'bool'
        				)
        			)
        		);
        }
        return $ret;
	}
	
    /**
     * Searches for given searchterm within bean and returns the result-set as an multi-dim array
     * after the given layout.
     *
     * @param string $term contains the searchterm as given by jQuery.autocomplete
     * @param string (optional) $layout defaults to "default"
     * @return array
     */
    public function clairvoyant($term, $layout = 'default')
    {   
        switch ($layout) {
            case 'email':
                $sql = <<<SQL

                SELECT
                    id AS id,
                    email AS label

                FROM
                    user

                WHERE
                    email like ?

                ORDER BY
                    email

SQL;
                break;
            default:
                $sql = <<<SQL

                SELECT
                    id AS id,
                    name AS label

                FROM
                    user

                WHERE
                    name like ?

                ORDER BY
                    name

SQL;
        }
        return $res = R::getAll($sql, array($term.'%'));
    }

	/**
	 * Returns a customized menu.
	 *
	 * @param string $action
	 * @param Cinnebar_View $view
 	 * @param Cinnebar_Menu (optional) $menu
 	 * @return Cinnebar_Menu
 	 */
 	public function makeMenu($action, Cinnebar_View $view, Cinnebar_Menu $menu = null)
	{
        $menu = parent::makeMenu($action, $view, $menu);
        return $menu;
	}

	/**
	 * Returns an array with possible layout for list view (index).
	 *
	 * @return array
	 */
	public function layouts()
	{
        return array('table');
	}
	
    /**
     * Really sends out the newsletter to given email addresses.
     *
     * @uses PHPMailer
     *
     * @param Cinnebar_Controller $controller
     * @return void
     */
    public function sendInvite(Cinnebar_Controller $controller)
    {
        global $config;
        require_once BASEDIR.'/vendors/PHPMailer_5.2.4/class.phpmailer.php';
        
		$mail = new PHPMailer();
		$mail->CharSet = 'UTF-8';
		$mail->Subject = __('invitation_subject').' '.htmlspecialchars($this->bean->name);
		
		$mail->From = $config['listmanager']['email'];
		$mail->FromName = $config['listmanager']['name'];
  		$mail->AddReplyTo($config['listmanager']['email'], $config['listmanager']['name']);

		$mail->IsSMTP();
		//$mail->SMTPDebug = 2;
		$mail->SMTPAuth = true;
		$mail->SMTPKeepAlive = true;
		$mail->Host = $config['smtp']['host'];
		$mail->Port = $config['smtp']['port'];
		$mail->Username = $config['smtp']['user'];
		$mail->Password = $config['smtp']['pw'];

		$result = true;

        $body_html = $controller->makeView(sprintf('user/mail/%s/html', $controller->router()->language()));
		$body_text = $controller->makeView(sprintf('user/mail/%s/text', $controller->router()->language()));
		$body_html->record = $body_text->record = $this->bean;
		$mail->MsgHTML($body_html->render());
		$mail->AltBody = $body_text->render();
	    
	    $mail->AddAddress($this->bean->email);
        $result = $mail->Send();

		return $result;
    }

	/**
	 * Returns users who are on-line, that is having a session not older than given seconds.
	 *
	 * @param int $period of seconds a user session may have aged
	 * @return array
	 */
	public function whoisonline($period = 120)
	{
		$sql = <<<SQL
			SELECT
				user.id AS id
			FROM user
			LEFT JOIN session on session.token = user.sid
			WHERE
			session.lastupdate >= (unix_timestamp(now()) - ?) AND
			user.id != ?
			ORDER BY user.name
SQL;
		try {
			$assoc = R::$adapter->getAssoc($sql, array($period, $this->bean->getId()));
			return R::batch('user', array_keys($assoc));
		} catch (Exception $e) {
            Cinnebar_Logger::instance()->log('Model_User::whoisonline '.$e);
			return array();
		}
	}
}


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
 * The login model manages user login trials.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Login extends Cinnebar_Model
{
}


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
 * Manages domains.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Domain extends Cinnebar_Model
{  
    /**
     * Return ownDomain(s).
     *
     * @param bool $add if true an empty records gets added.
     * @return array
     */
    public function getownDomain($add)
    {
        $own = R::find('domain', ' domain_id = ? ORDER BY sequence, name', array($this->bean->getId()));
        if ($add) $own[] = R::dispense('domain');
        return $own;
    }

    /**
     * Builds a hierarchical menu from an adjancy bean.
     *
     * @param string (optional) $url_prefix as a kind of basehref, e.g. 'http://localhost/s/de'
     * @param string (optional) $lng code of the language to retrieve
     * @param string (optional) $orderclause defaults to 'sequence'
     * @param bool (optional) $invisibles default to false so that invisible beans wont show up
     * @return Cinnebar_Menu
     */
    public function hierMenu($url_prefix = '', $lng = null, $order = 'sequence ASC', $invisible = false)
    {
        $sql_invisible = 'AND invisible != 1';
        if ($invisible) {
            $sql_invisible = null;
        }
        $sql = sprintf(
            '%s = ? %s ORDER BY %s',
            $this->bean->getMeta('type').'_id',
            $sql_invisible, $order
        );
        $records = R::find(
            $this->bean->getMeta('type'),
            $sql,
            array($this->bean->getId())
        );
        $menu = new Cinnebar_Menu();
        foreach ($records as $record) {
            $menu->add(
                __('domain_'.$record->name),
                $url_prefix.$record->url,
                $record->getMeta('type').'-'.$record->getId(),
                $record->hierMenu($url_prefix, $lng, $order, $invisible)
            );
        }
        return $menu;
    }
    
    /**
     * Returns the (translated) name of the domain.
     *
     * @param string (optional) $lng iso code of the translation
     * @return string
     */
    public function name($lng = null)
    {
        if (empty($lng)) return $this->bean->name;
        return $this->bean->name.'('.$lng.')';
    }
    
    /**
     * Returns SQL for filtering these beans.
     *
     * @uses R
     * @param string $where_clause
     * @param string $order_clause
     * @param int $offset
     * @param int $limit
     * @return string $SQL
     */
    public function sqlForFilters($where_clause = '1', $order_clause = 'id', $offset = 0, $limit = 1)
    {
		$sql = <<<SQL
		SELECT
			DISTINCT(domain.id) as id  

		FROM
			domain

		WHERE {$where_clause}

		ORDER BY {$order_clause}

		LIMIT {$offset}, {$limit}
SQL;
        return $sql;
    }

	/**
	 * Returns an array with possible attributes for order clauses and such.
	 *
	 * @param string (optional) $layout defaults to table and can be of any value
	 * @return array
	 */
	public function attributes($layout = 'table')
	{
	    switch ($layout) {
	        default:
        		$ret = array(
        			array(
        				'attribute' => 'name',
        				'orderclause' => 'name',
        				'class' => 'text',
        				'filter' => array(
        				    'tag' => 'text'
        				)
        			),
        			array(
        				'attribute' => 'url',
        				'orderclause' => 'url',
        				'class' => 'text',
        				'filter' => array(
        				    'tag' => 'text'
        				)
        			),
        			array(
        				'attribute' => 'invisible',
        				'orderclause' => 'invisible',
        				'class' => 'bool',
        				'viewhelper' => 'bool',
        				'filter' => array('tag' => 'bool')
        			),
        			array(
        				'attribute' => 'blessed',
        				'orderclause' => 'blessed',
        				'class' => 'bool',
        				'viewhelper' => 'bool',
        				'filter' => array('tag' => 'bool')
        			)
        		);
        }
        return $ret;
	}
	
	/**
	 * Returns a customized menu.
	 *
	 * @param string $action
	 * @param Cinnebar_View $view
 	 * @param Cinnebar_Menu (optional) $menu
 	 * @return Cinnebar_Menu
 	 */
 	public function makeMenu($action, Cinnebar_View $view, Cinnebar_Menu $menu = null)
	{
        $menu = parent::makeMenu($action, $view, $menu);
        return $menu;
	}
	
	/**
	 * Returns an array with possible layout for list view (index).
	 *
	 * @return array
	 */
	public function layouts()
	{
        return array('table');
	}

    /**
     * Returns keywords from this bean for tagging.
     *
     * @var array
     */
    public function keywords()
    {
        return array($this->bean->name);
    }

    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        if ( ! $this->bean->domain_id) $this->bean->domain_id = null;
        $this->setAutoInfo(true);
        $this->addValidator('name', 'hasvalue');
    }
    
    /**
     * Update.
     */
    public function update()
    {
        if ( ! $this->bean->domain_id) $this->bean->domain_id = null;
        parent::update();
    }
}


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
 * The filter model manages filters you may set on beans.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Filter extends Cinnebar_Model
{
    /**
     * Container for values that are collected on building a where clause.
     *
     * @var array
     */
    public $filter_values = array();
    
    /**
     * Returns wether the attributes array has a key named filter or not.
     *
     * @param array $attributes
     * @return bool
     */
    public function hasFilter(array $attributes)
    {
        foreach ($attributes as $attribute) {
            if (isset($attribute['filter']) && is_array($attribute['filter'])) return true;
        }
        return false;
    }
    
    /**
     * Returns a SQL WHERE clause for usage with another bean.
     *
     * @uses Model_Criteria::makeWherePart() to generate the SQL for a criteria
     * @return string $WhereClauseForSQL
     */
    public function buildWhereClause()
    {
        $criterias = $this->bean->ownCriteria;
        
        if (empty($criterias)) return '1';// find all because there are no criterias
        
    	$where = array();
    	$this->filter_values = array();
    	//$mask = " %s %s %s"; // login, field, op (with masked value)
    	
    	$n = 0;
    	foreach ($criterias as $id=>$criteria) {
    	    if ( ! $criteria->op) continue; // skip all entries that say any!
            if ( $criteria->value === null || $criteria->value === '') continue; // skip all empty
    		$n++;
    		$logic = $this->bean->logic . ' ';
    		if ($n == 1) $logic = '';
    		$where[] = $logic.$criteria->makeWherePart($this);
    	}
    	
    	if (empty($where)) return '1';// find all because there was no active criteria
    	
    	$where = implode(' ', $where);
    	return $where;
    }
    
    /**
     * Returns an array with values that were collected as the where clause was build.
     *
     * @return array
     */
    public function filterValues()
    {
        return $this->filter_values;
    }
    
    /**
     * Masks the criterias value and stacks it into the filter values.
     *
     * @uses $filter_values
     * @param RedBean_OODBBean $criteria
     * @return void
     */
    protected function dep_mask_filter_value(RedBean_OODBBean $criteria)
    {
        $add_to_filter_values = true;
    	switch ($criteria->op) {
    		case 'like':
    			$value = '%'.str_replace($this->pat, $this->rep, $criteria->value).'%';
    			break;
    		case 'notlike':
    			$value = '%'.str_replace($this->pat, $this->rep, $criteria->value).'%';
    			break;
    		case 'bw':
    			$value = str_replace($this->pat, $this->rep, $criteria->value).'%';
    			break;
    		case 'ew':
    			$value = '%'.str_replace($this->pat, $this->rep, $criteria->value);
    			break;
    		case 'in':
    		    $_sharedSubName = 'shared'.ucfirst(strtolower($criteria->substitute));
    		    $ids = array_keys($criteria->{$_sharedSubName});
    		    $value = 'IN ('.implode(', ', $ids).')';
    		    $add_to_filter_values = false;
    		    break;
    		default:
    			$value = $criteria->value;
    	}
    	if ($add_to_filter_values) {
            if ($criteria->tag == 'date') {
                $value = date('Y-m-d', strtotime($criteria->value));
            }
    	    $this->filter_values[] = $value;
    	}
    	return true;
    }
    
    /**
     * Returns array of filter criterias.
     *
     * @return array
     */
    public function criterias()
    {
        /*
        if ( ! $this->bean->ownCriteria) {
            $model = R::dispense($this->bean->model);
            $preset = $model->filters();
            foreach ($preset as $n=>$attr) {
                 $li = R::dispense('criteria');
                 $li->import($attr);
                 $this->bean->ownCriteria[] = $li;
             }
        }
        */
        return $this->bean->ownCriteria;
    }
    
    /**
     * Returns a criteria bean for a certain filter attribute.
     *
     * @param array $attribute
     * @return RedBean_OODBBean
     */
    public function getCriteria(array $attribute)
    {
        $attrName = isset($attribute['filter']['orderclause']) ? $attribute['filter']['orderclause']: $attribute['orderclause'];
        if ( ! $criteria = R::findOne('criteria', ' filter_id = ? AND attribute = ? LIMIT 1', array($this->bean->getId(), $attrName))) {
            $criteria = R::dispense('criteria');
            $criteria->tag = $attribute['filter']['tag'];
            $criteria->attribute = $attrName;
            $operators = $criteria->operators();
            $criteria->op = $operators[0];
        }
        return $criteria;
    }
    
    /**
     * Returns an array with order clause option of the filtered bean type.
     *
     * @return array $orderClauses
     */
    public function deprecated_orderClauses()
    {
        $filtered_bean = R::dispense($this->bean->model);
        return $filtered_bean->orderClauses();
    }
    
    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        $this->addValidator('model', 'hasvalue');
    }
}

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
 * The criteria model belongs to a filter bean and lets you filter other beans.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Criteria extends Cinnebar_Model
{
    /**
     * Container for the map of search operators.
     *
     * @var array
     */
    public $map = array(
 		'like' => '%1$s like ?',
 		'notlike' => '%1$s not like ?',
 		'eq' => '%1$s = ?',
 		'neq' => '%1$s != ?',
 		'bw' => '%1$s like ?',
 		'ew' => '%1$s like ?',
 		'lt' => '%1$s < ?',
 		'gt' => '%1$s > ?',
 		'in' => '%1$s in (%2$s)'
 		//'between' => __('filter_op_between'),
 		//'istrue' => __('filter_op_istrue'),
 		//'isfalse' => __('filter_op_isfalse')
 	);

    /**
     * Holds possible search operators depending on the tag type.
     *
     * @var array
     */
    public $operators = array(
         'text' => array('like', 'ew', 'eq', 'neq', 'bw', 'notlike'),
         'number' => array('eq', 'gt', 'lt', 'neq'),
         'date' => array('eq', 'gt', 'lt', 'neq'),
         'time' => array('eq', 'gt', 'lt', 'neq'),
         'email' => array('bw', 'ew', 'eq', 'neq', 'like', 'notlike'),
         'textarea' => array('bw', 'ew', 'eq', 'neq', 'like', 'notlike'),
         'in' => array('in'),
         'select' => array('eq'),
         'bool' => array('eq'),
         'boolperv' => array('eq')
     );

     /**
      * Container for characters that have to be escaped for usage with SQL.
      *
      * @var array
      */
     public $pat = array('%', '_');

     /**
      * Container for escaped charaters.
      *
      * @var array
      */
     public $rep = array('\%', '\_');
    
    /**
     * Returns a string to use as part of a SQL query.
     *
     * @throws an exception when criteria operator has no template definded in map
     * @uses $map
     * @uses mask_filter_value()
     * @param Model_Filter $filter
     * @return string
     */
    public function makeWherePart(Model_Filter $filter)
    {
        if ( ! isset($this->map[$this->bean->op])) throw new Exception('Filter operator has no template');
        $template = $this->map[$this->bean->op];
        $value = $this->mask_filter_value($filter);
        return sprintf($template, $this->bean->attribute, $value);
    }
    
    /**
     * Masks the criterias value and stacks it into the filter values.
     *
     * @uses Model_Filter::$filter_values where the values of our criterias are stacked up
     * @param Model_Filter $filter
     * @return void
     */
    protected function mask_filter_value(Model_Filter $filter)
    {
        $add_to_filter_values = true;
    	switch ($this->bean->op) {
    		case 'like':
    			$value = '%'.str_replace($this->pat, $this->rep, $this->bean->value).'%';
    			break;
    		case 'notlike':
    			$value = '%'.str_replace($this->pat, $this->rep, $this->bean->value).'%';
    			break;
    		case 'bw':
    			$value = str_replace($this->pat, $this->rep, $this->bean->value).'%';
    			break;
    		case 'ew':
    			$value = '%'.str_replace($this->pat, $this->rep, $this->bean->value);
    			break;
    		case 'in':
    		    $_sharedSubName = 'shared'.ucfirst(strtolower($this->bean->substitute));
    		    $ids = array_keys($this->bean->{$_sharedSubName});
    		    $value = implode(', ', $ids);
    		    $add_to_filter_values = false;
    		    break;
    		default:
    			$value = $this->bean->value;
    	}
        if ($add_to_filter_values) {
            if ($this->bean->tag == 'date') {
                $value = date('Y-m-d', strtotime($value));
            }
    	    $filter->filter_values[] = $value;
    	}
    	return $value;
    }
    
    /**
     * Returns array with possible operators for the given tag type.
     *
     * @return array $operators
     */
    public function operators()
    {
        if (isset($this->operators[$this->bean->tag])) return $this->operators[$this->bean->tag];
        return array();
    }
    
    /**
     * Returns array with possible operators for the given type.
     *
     * @param string $type
     * @return array $operators
     */
    public function getOperators($type = 'text')
    {
        if (isset($this->operators[$type])) return $this->operators[$type];
        return array();
    }
    
    /**
     * Setup validators.
     */
    public function dispense()
    {
        $this->addValidator('attribute', 'hasvalue');
    }
}

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
 * Manages action.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Action extends Cinnebar_Model
{
	/**
	 * Returns an array with possible attributes for order clauses and such.
	 *
	 * @param string (optional) $layout defaults to table and can be of any value
	 * @return array
	 */
	public function attributes($layout = 'table')
	{
	    switch ($layout) {
	        default:
        		$ret = array(
        			array(
        				'attribute' => 'name',
        				'orderclause' => 'name',
        				'class' => 'text'
        			)
        		);
        }
        return $ret;
	}
	
	/**
	 * Returns an array with possible layout for list view (index).
	 *
	 * @return array
	 */
	public function layouts()
	{
        return array('table');
	}

    /**
     * Returns keywords from this bean for tagging.
     *
     * @var array
     */
    public function keywords()
    {
        return array(
            $this->bean->name
        );
    }

    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        //$this->bean->setMeta('buildcommand.unique', array(array('name')));
        $this->addValidator('name', 'hasvalue');
        $this->addValidator('name', 'isunique', array('bean' => $this->bean, 'attribute' => 'name'));
    }
}


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
 * Manages role.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Role extends Cinnebar_Model
{
	/**
	 * Returns an array with possible attributes for order clauses and such.
	 *
	 * @param string (optional) $layout defaults to table and can be of any value
	 * @return array
	 */
	public function attributes($layout = 'table')
	{
	    switch ($layout) {
	        default:
        		$ret = array(
        			array(
        				'attribute' => 'name',
        				'orderclause' => 'name',
        				'class' => 'text'
        			)
        		);
        }
        return $ret;
	}
	
	/**
	 * Returns an array with possible layout for list view (index).
	 *
	 * @return array
	 */
	public function layouts()
	{
        return array('table');
	}

    /**
     * Returns keywords from this bean for tagging.
     *
     * @var array
     */
    public function keywords()
    {
        return array(
            $this->bean->name
        );
    }

    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        //$this->bean->setMeta('buildcommand.unique', array(array('name')));
        $this->addValidator('name', 'hasvalue');
        $this->addValidator('name', 'isunique', array('bean' => $this->bean, 'attribute' => 'name'));
    }
}


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
 * Manages team.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Team extends Cinnebar_Model
{
	/**
	 * Returns an array with possible attributes for order clauses and such.
	 *
	 * @param string (optional) $layout defaults to table and can be of any value
	 * @return array
	 */
	public function attributes($layout = 'table')
	{
	    switch ($layout) {
	        default:
        		$ret = array(
        			array(
        				'attribute' => 'name',
        				'orderclause' => 'name',
        				'class' => 'text'
        			)
        		);
        }
        return $ret;
	}
	
	/**
	 * Returns an array with possible layout for list view (index).
	 *
	 * @return array
	 */
	public function layouts()
	{
        return array('table');
	}

    /**
     * Returns keywords from this bean for tagging.
     *
     * @var array
     */
    public function keywords()
    {
        return array(
            $this->bean->name
        );
    }

    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        //$this->bean->setMeta('buildcommand.unique', array(array('name')));
        $this->addValidator('name', 'hasvalue');
        $this->addValidator('name', 'isunique', array('bean' => $this->bean, 'attribute' => 'name'));
    }
}


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
 * Manages session.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Session extends Cinnebar_Model
{
}


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
 * Manages modules.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Module extends Cinnebar_Model
{
    /**
     * Returns SQL for filtering these beans.
     *
     * @uses R
     * @param string $where_clause
     * @param string $order_clause
     * @param int $offset
     * @param int $limit
     * @return string $SQL
     */
    public function sqlForFilters($where_clause = '1', $order_clause = 'id', $offset = 0, $limit = 1)
    {
		$sql = <<<SQL
		SELECT
			DISTINCT(module.id) as id  

		FROM
			module

		WHERE {$where_clause}

		ORDER BY {$order_clause}

		LIMIT {$offset}, {$limit}
SQL;
        return $sql;
    }

	/**
	 * Returns an array with possible attributes for order clauses and such.
	 *
	 * @param string (optional) $layout defaults to table and can be of any value
	 * @return array
	 */
	public function attributes($layout = 'table')
	{
	    switch ($layout) {
	        default:
        		$ret = array(
        			array(
        				'attribute' => 'name',
        				'orderclause' => 'name',
        				'class' => 'text'
        			),
        			array(
        				'attribute' => 'enabled',
        				'orderclause' => 'enabled',
        				'class' => 'bool',
        				'viewhelper' => 'bool'
        			)
        		);
        }
        return $ret;
	}
	
    /**
     * Returns enabled modules.
     *
     * @return array
     */
    public function enabled()
    {
        return R::find('module', ' enabled = ? ORDER BY name', array(1));
    }
	
	/**
	 * Returns a customized menu.
	 *
	 * @param string $action
	 * @param Cinnebar_View $view
 	 * @param Cinnebar_Menu (optional) $menu
 	 * @return Cinnebar_Menu
 	 */
 	public function makeMenu($action, Cinnebar_View $view, Cinnebar_Menu $menu = null)
	{
        $menu = parent::makeMenu($action, $view, $menu);
        return $menu;
	}
	
	/**
	 * Returns an array with possible layout for list view (index).
	 *
	 * @return array
	 */
	public function layouts()
	{
        return array('table');
	}

    /**
     * Returns keywords from this bean for tagging.
     *
     * @var array
     */
    public function keywords()
    {
        return array(
            $this->bean->name
        );
    }

    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        //$this->bean->setMeta('buildcommand.unique', array(array('name')));
        $this->setAutoInfo(true);
        $this->addValidator('name', 'hasvalue');
        $this->addValidator('name', 'isunique', array('bean' => $this->bean, 'attribute' => 'name'));
    }
}


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
 * Validates a value against a validation rule.
 *
 * To add your own validator simply add a php file to the validator directory of your Cinnebar
 * installation. Name the validator after the scheme Validator_* extends Cinnebar_Validator and
 * implement a execute() method. As an example see {@link Validator_Range}.
 *
 * Example usage of the range validator:
 * <code>
 * 
 * $range = new Validator_Range(array('min' => 1, 'max' => 100));
 * if ($range->execute(77)) echo '1 >= 77 <= 100 is true';
 * if ($range->execute(177)) echo '1 >= 177 <= 100 is false';
 * 
 * </code>
 *
 * @package Cinnebar
 * @subpackage Validator
 * @version $Id$
 */
class Cinnebar_Validator
{
    /**
     * Container for the validators options.
     *
     * @var array
     */
    public $options = array();

    /**
     * Constructor.
     *
     * @uses Cinnebar_Validator::$options
     * @param array (optional) $options
     */
    public function __construct(array $options = array()) {
        $this->options = $options;
    }

    /**
     * Returns wether the validation was good or not.
     *
     * This validator checks if the given value is true or not.
     *
     * @param mixed $value
     * @return bool $validOrInvalid
     */
    public function execute($value)
    {
        return $value;
    }
}


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
 * Validator to check if a value is neither null nor empty.
 *
 * @package Cinnebar
 * @subpackage Validator
 * @version $Id$
 */
class Validator_Hasvalue extends Cinnebar_Validator
{
    /**
     * Returns wether a value has a piece or information or not.
     *
     * @param mixed $value
     * @return bool $hasValueOrNot
     */
    public function execute($value)
    {
        if (null === $value) return false;
        if (empty($value)) return false;
        return true;
    }
}


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
 * Validator to check if there is a file in your upload directory.
 *
 * @package Cinnebar
 * @subpackage Validator
 * @version $Id$
 */
class Validator_Hasupload extends Cinnebar_Validator
{
    /**
     * Returns wether a file is in your upload directory.
     *
     * @param mixed $filename
     * @return bool $filenameIsInUploadDirOrNot
     */
    public function execute($filename)
    {
        global $config;
        $filename = $config['upload']['dir'].$filename;
        return is_file($filename);
    }
}


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
 * Validator to check if a value is a valid date.
 *
 * @package Cinnebar
 * @subpackage Validator
 * @version $Id$
 */
class Validator_Isdate extends Cinnebar_Validator
{
    /**
     * Returns wether the value is a valid date or not.
     *
     * @param mixed $value
     * @return bool $validOrInvalid
     */
    public function execute($value)
    {
        if (preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $value, $matches)) {
            if (checkdate($matches[2], $matches[3], $matches[1])) return true;
        }
        return false;
    }
}


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
 * Validator to check if the value is a valid e-mail address.
 *
 * @package Cinnebar
 * @subpackage Validator
 * @version $Id$
 */
class Validator_Isemail extends Cinnebar_Validator
{
    /**
     * Returns wether the value is a valid email address or not.
     *
     * @param mixed $value
     * @return bool $validOrInvalid
     */
    public function execute($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}


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
 * Validator to check if a value is numeric.
 *
 * @package Cinnebar
 * @subpackage Validator
 * @version $Id$
 */
class Validator_Isnumeric extends Cinnebar_Validator
{
    /**
     * Returns wether the value is numeric or not.
     *
     * @param mixed $value
     * @return bool $validOrInvalid
     */
    public function execute($value)
    {
        return (is_numeric($value));
    }
}


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
 * Validator to check if a value is within a given range.
 *
 * @package Cinnebar
 * @subpackage Validator
 * @version $Id$
 */
class Validator_Range extends Cinnebar_Validator
{
    /**
     * Returns wether the value is in the given range or not.
     *
     * @uses Cinnebar_Validator::$options
     * @param mixed $value
     * @return bool $validOrInvalid
     */
    public function execute($value)
    {
        if ( ! isset($this->options['min']) || ! isset($this->options['max'])) {
            throw new Exception('exception_validator_range_has_no_min_or_max');
        }
        return ($value >= $this->options['min'] && $value <= $this->options['max']);
    }
}


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
 * The basic formatter class of the cinnebar system.
 *
 * @package Cinnebar
 * @subpackage Formatter
 * @version $Id$
 */
class Cinnebar_Formatter
{
    /**
     * Formats attributes of a bean.
     *
     * @param RedBean_OODBBean $bean to format
     * @return string $formattedString
     */
    public function execute(RedBean_OODBBean $bean)
    {
        return $bean;
    }
}

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
 * Convertes a value.
 *
 * To add your own converter simply add a php file to the converter directory of your Cinnebar
 * installation. Name the converter after the scheme Converter_* extends Cinnebar_Converter and
 * implement a execute() method. As an example see {@link Converter_MySQLDate}.
 *
 * Example usage of the MySQLDate converter:
 * <code>
 * 
 * $mysqldate = new Converter_MySQLDate();
 * $attr_date = $mysqldate('01.04.2011'); // will give you '2011-04-01'
 * 
 * </code>
 *
 * @package Cinnebar
 * @subpackage Converter
 * @version $Id$
 */
class Cinnebar_Converter
{
    /**
     * Holds the bean on which the container works.
     *
     * @var RedBean_OODBBean
     */
    public $bean;

    /**
     * Container for the converter options.
     *
     * @var array
     */
    public $options = array();

    /**
     * Constructor.
     *
     * @uses Cinnebar_Converter::$options
     * @param Redbean_OODBBean $bean
     * @param array (optional) $options
     */
    public function __construct(RedBean_OODBBean $bean, array $options = array()) {
        $this->bean = $bean;
        $this->options = $options;
    }
    
    /**
     * Returns the bean instance.
     *
     * @return RedBean_OODBBean
     */
    public function bean()
    {
        return $this->bean;
    }

    /**
     * Returns whatever the converters has converted the input to.
     *
     * @param mixed $value
     * @return mixed $convertedValue
     */
    public function execute($value)
    {
        return $value;
    }
}


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
 * Converter to turn a locale input value into a decimal value.
 *
 * @package Cinnebar
 * @subpackage Converter
 * @version $Id$
 */
class Converter_Decimal extends Cinnebar_Converter
{
    /**
     * Replaces comma against a decimal point and casts the value as float.
     *
     * @param mixed $value
     * @return float $floatingPointValue
     */
    public function execute($value)
    {
        return (float)str_replace(',', '.', $value);
    }
}


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
 * Converter to turn the value into a mysql date formatted value.
 *
 * @package Cinnebar
 * @subpackage Converter
 * @version $Id$
 */
class Converter_Mysqldate extends Cinnebar_Converter
{
    /**
     * Returns the value as a mysql date value.
     *
     * @param mixed $value
     * @return string $mySQLDateValue
     */
    public function execute($value)
    {
        if ( ! $value || empty($value) || $value == '0000-00-00') return '0000-00-00';
        return date('Y-m-d', strtotime($value));
    }
}


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
 * The basic module class of the cinnebar system.
 *
 * A module is used to render a bean (e.g. slice) in a certain fashion
 * where it may be rendered in view or edit style.
 *
 * @package Cinnebar
 * @subpackage Module
 * @version $Id$
 */
class Cinnebar_Module implements iModule
{
    /**
     * Switch to indicate wether to render in frontend or backend mode.
     *
     * @var bool
     */
    public $backend = false;

    /**
     * Container for the bean.
     *
     * @var RedBean_OODBBean
     */
    protected $bean;

    /**
     * Container for the view the module is working in.
     *
     * @var Cinnebar_View
     */
    protected $view;
    
    /**
     * Constructs a new instance of a module.
     *
     * @param Cinnebar_View $view is the view the module works in
     * @param RedBean_OODBean $bean is the bean that the module works on
     */
    public function __construct(Cinnebar_View $view, RedBean_OODBBean $bean)
    {
        $this->view = $view;
        $this->bean = $bean;
    }
    
    /**
     * Returns the instance of the view this modules is running in.
     *
     * @return Cinnebar_View
     */
    public function view()
    {
        return $this->view;
    }
    
    /**
     * Returns the instance of the bean this modules is running in.
     *
     * @return RedBean_OODBBean
     */
    public function bean()
    {
        return $this->bean;
    }
    
    /**
     * Sets the rendering mode to either backend or frontend.
     *
     * @param bool (optional) $switch
     */
    public function backend($switch = null)
    {
        if ( $switch !== null) $this->backend = $switch;
        return $this->backend;
    }

    /**
     * Execute the module by rendering either in back- or frontend mode.
     *
     * @return string
     */
    public function execute()
    {
        if ($this->backend()) return $this->renderBackend();
        return $this->renderFrontend();
    }
    
    /**
     * Renders the slice bean in frontend mode.
     */
    public function renderFrontend()
    {
        return 'Your code should render a slice bean in frontend mode.';
    }

    /**
     * Renders the slice bean in backend mode.
     */
    public function renderBackend()
    {
        return 'Your code should render a slice bean in backend mode.';
    }
}

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
  * A basic migrator.
  *
  * To add your own migrator simply add a php file to the migrator directory of your Cinnebar
  * installation. Name the migrator after the scheme Migrator_* extends Cinnebar_Migrator and
  * implement methods as you wish. You will not call a migrator directly, instead it is called
  * from a cli cycle runs.
  *
  * @package Cinnebar
  * @subpackage Migrator
  * @version $Id$
  */
abstract class Cinnebar_Migrator
{
    /**
     * Holds the instance of our migrator bean.
     *
     * @var RedBean_OODBBean
     */
    public $migrator;
    
    /**
     * Holds the log file name where failed migrations are stored.
     *
     * @var string
     */
    public $logname = 'migrator';

    /**
     * Constructs a new Migrator and adds legacy and heir databases.
     *
     * @param RedBean_OODBBean $migrator
     */
    public function __construct(RedBean_OODBBean $migrator)
    {
        $this->migrator = $migrator;
        R::addDatabase(
            'legacy', 'mysql:host='.
            $this->migrator->legacy_host.
            ';dbname='.
            $this->migrator->legacy_db,
            $this->migrator->legacy_user,
            $this->migrator->legacy_pw);
            
        R::addDatabase(
            'heir', 'mysql:host='.
            $this->migrator->heir_host.
            ';dbname='.
            $this->migrator->heir_db,
            $this->migrator->heir_user,
            $this->migrator->heir_pw);
    }
    
    /**
     * Migrates a legacy to a heir.
     *
     * A migration cycle is bracketed by the {@see open()} and {@see close()} and consists
     * of the methods each migrator has to implement before we have a really functional
     * migrator beast.
     *
     * @uses open()
     * @uses prepare()
     * @uses basic_claims()
     * @uses dynamic_claims()
     * @uses clean_up()
     * @uses close()
     * @return void
     */
    public function migrate()
    {
        $this->open();
        $this->prepare();
        $this->basic_claims();
        $this->dynamic_claims();
        $this->cleanup();
        $this->close();
        return;
    }
    
    /**
     * Open the migrator.
     *
     * @return void
     */
    public function open()
    {
        $this->migrator->start = time();
        return true;
    }
    
    /**
     * Close the migrator.
     *
     * @return bool $trueOrFalse
     */
    public function close()
    {
        $this->useDefaultDB();
        $this->migrator->finish = time();
        try {
            R::store($this->migrator);
            return true;
        } catch (Exception $e) {
            Cinnebar_Logger::instance()->log($e, 'exceptions');
            return false;
        }
    }
    
    /**
     * Makes the legacy database the current.
     *
     * @return bool
     */
    public function useLegacyDB()
    {
        return R::selectDatabase('legacy');
    }
    
    /**
     * Makes the heir database the current.
     *
     * @return bool
     */
    public function useHeirDB()
    {
        return R::selectDatabase('heir');
    }
    
    /**
     * Makes the default database the current.
     *
     * @return bool
     */
    public function useDefaultDB()
    {
        return R::selectDatabase('default');
    }
    
    /**
     * Prepare for migration.
     */
    abstract protected function prepare();
    
    /**
     * Cleans up after migration.
     */
    abstract protected function cleanup();
    
    /**
     * Migrate basic claims of legacy to heir.
     */
    abstract protected function basic_claims();

    /**
     * Migrate basic claims of legacy to heir.
     */
    abstract protected function dynamic_claims();
}


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
 * Manages a user session.
 *
 * To add your own sessionhandler simply add a php file to the sessionhandler directory of your Cinnebar
 * installation. Name the sessionhandler after the scheme Sessionhandler_* extends Cinnebar_Sessionhandler
 * and implement all methods seen here. You can than use it by defining it as your default sessionhandler
 * in your configuration file {@link config.example.php}. As an example see {@link Sessionhandler_Apc}.
 *
 * @package Cinnebar
 * @subpackage Sessionhandler
 * @version $Id$
 */
class Cinnebar_Sessionhandler
{   
	/**
	 * opens a new session and returns true.
	 *
	 * @param string $path
	 * @param string $id
	 * @return bool
	 */
	public function open($path, $id)
    {
        return true;
    }
    
	/**
	 * closes the session.
	 *
	 * @return bool
	 */
    public function close()
	{
	    return true;
	}
    
	/**
	 * returns the session data or an empty string.
	 *
	 * @param string $id
	 * @return string
	 */
	public function read($id)
	{
        return '';
	}
	
	/**
	 * writes the session data.
	 *
	 * @param string $id
	 * @param string $data
	 * @return bool
	 */
    public function write($id, $data)
	{
        return true;
	}

	/**
	 * deletes the session.
	 *
	 * @param string $id
	 * @return bool
	 */
    public function destroy($id)
	{
        return true;
	}

	/**
	 * deletes all old and outdated sessions.
	 *
	 * @param int $max_lifetime
	 * @return bool
	 */
    public function gc($max_lifetime)
	{
        return true;
	}
}


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
 * The APC(Another PHP Cache) sessionhandler class.
 *
 * This sessionhandler uses an installed APC extension to handle user sessions. 
 *
 * @package Cinnebar
 * @subpackage Sessionhandler
 * @version $Id$
 */
class Sessionhandler_Apc extends Cinnebar_Sessionhandler
{
    /**
     * Holds a (optional) prefix to keep APC user cache land tidy.
     *
     * @var string
     */
    public $prefix = 'CINNEBAR_SESS_';

	/**
	 * Opens a new session.
	 *
	 * @param string $path
	 * @param string $id
	 * @return bool
	 */
	public function open($path, $id)
    {
        return true;
    }
    
	/**
	 * Closes the session.
	 *
	 * @return bool
	 */
    public function close()
	{
	    return true;
	}
    
	/**
	 * Returns the session data or an empty string.
	 *
	 * @param string $id
	 * @return string
	 */
	public function read($id)
	{
        if ( false == $session = apc_fetch($this->prefix.$id)) return '';
        return $session;
	}
	
	/**
	 * Writes the session to APC user values.
	 *
	 * @param string $id
	 * @param string $data
	 * @return bool
	 */
    public function write($id, $data)
	{
        return apc_store($this->prefix.$id, $data);
	}

	/**
	 * Deletes the session record from APC user values.
	 *
	 * @param string $id
	 * @return bool
	 */
    public function destroy($id)
	{
        return apc_delete($this->prefix.$id);
	}

	/**
	 * Perform a garbage collection for outdated sessions.
	 *
	 * @param int $max_lifetime
	 * @return bool
	 */
    public function gc($max_lifetime)
	{
        return true;
	}
}


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
 * The database sessionhandler class.
 *
 * This sessionhandler uses the database to keep track of user sessions.
 *
 * @package Cinnebar
 * @subpackage Sessionhandler
 * @version $Id$
 */
class Sessionhandler_Database extends Cinnebar_Sessionhandler
{   
	/**
	 * Opens a new session.
	 *
	 * @param string $path
	 * @param string $id
	 * @return bool
	 */
	public function open($path, $id)
    {
        return true;
    }
    
	/**
	 * Closes the session.
	 *
	 * @return bool
	 */
    public function close()
	{
	    return true;
	}
    
	/**
	 * Returns the session data or an empty string.
	 *
	 * @uses R
	 *
	 * @param string $id
	 * @return string
	 */
	public function read($id)
	{
	    if ( ! $session = R::findOne('session', ' token = ? LIMIT 1', array($id))) return '';
        return $session->data;
	}
	
	/**
	 * Writes the session to the database.
	 *
	 * @uses R
	 *
	 * @param string $id
	 * @param string $data
	 * @return bool
	 */
    public function write($id, $data)
	{
        if (! $session = R::findOne('session', ' token = ? LIMIT 1', array($id))) {
            $session = R::dispense('session');
        }
        $session->token = $id;
        $session->data = $data;
        $session->lastupdate = time();
        try {
            R::store($session);
            return true;
        } catch (Exception $e) {
            return false;
        }
	}

	/**
	 * Deletes the session record from the database.
	 *
	 * @uses R
	 *
	 * @param string $id
	 * @return bool
	 */
    public function destroy($id)
	{
	    if ( ! $session = R::findOne('session', ' token = ? LIMIT 1', array($id))) return true;
	    try {
	        R::trash($session);
	        return true;
	    } catch (Exception $e) {
	        return false;
	    }
	}

	/**
	 * Perform garbage collection for outdated sessions.
	 *
	 * @param int $max_lifetime
	 * @return bool
	 */
    public function gc($max_lifetime)
	{
        return true;
	}
}


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
 * The Memory sessionhandler class.
 *
 * This sessionhandler uses PHP memory to handle user sessions. While is it of no use for
 * production sites or applications it will be useful in unit and integration tests.
 *
 * @package Cinnebar
 * @subpackage Sessionhandler
 * @version $Id$
 */
class Sessionhandler_Memory extends Cinnebar_Sessionhandler
{
    /**
     * Container for sessions.
     *
     * @var array
     */
    public $sessions = array();

	/**
	 * Opens a new session.
	 *
	 * @param string $path
	 * @param string $id
	 * @return bool
	 */
	public function open($path, $id)
    {
        return true;
    }
    
	/**
	 * Closes the session.
	 *
	 * @return bool
	 */
    public function close()
	{
	    return true;
	}
    
	/**
	 * Returns the session data or an empty string.
	 *
	 * @param string $id
	 * @return string
	 */
	public function read($id)
	{
        if ( ! isset($this->sessions[$id])) return '';
        return $this->sessions[$id];
	}
	
	/**
	 * Writes the session to APC user values.
	 *
	 * @param string $id
	 * @param string $data
	 * @return bool
	 */
    public function write($id, $data)
	{
	    $this->sessions[$id] = $data;
        return true;
	}

	/**
	 * Deletes the session record from APC user values.
	 *
	 * @param string $id
	 * @return bool
	 */
    public function destroy($id)
	{
        if (isset($this->sessions[$id])) unset ($this->sessions[$id]);
        return true;
	}

	/**
	 * Perform a garbage collection for outdated sessions.
	 *
	 * @param int $max_lifetime
	 * @return bool
	 */
    public function gc($max_lifetime)
	{
        return true;
	}
}



/**
 * Cinnebar.
 */
class Cinnebar extends Cinnebar_Facade
{
}

