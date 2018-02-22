<?php
// Written by Stephan A. Hombergs, Copyright 2012-2013. Licensed @see license.txt
 
interface iLanguage
{
    public function enabled();
}
interface iToken
{
    public function in($iso = 'de');
}
interface iPermission
{
    public function allowed($user = null, $domain, $action);
    public function domains($user, $action);
    public function load($user = null);
}
interface iModule
{
    public function renderFrontend();
    public function renderBackend();
}
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
function with($object)
{
    return $object;
}
class Cinnebar_Factory
{
    public function __construct()
    {
    }
    public static function make($class, $prefix = 'Cinnebar')
    {
        $class_name = ucfirst(strtolower($prefix)).'_'.ucfirst(strtolower($class));
        if (class_exists($class_name)) return new $class_name();
        throw new Exception(sprint(__('Unable to make a new "%s"-class.'), $class_name));
    }
}
class Cinnebar_Facade extends Cinnebar_Element
{
    const RELEASE = '1.12';
    private $cycle;
    public function cli()
    {
        return (php_sapi_name() == 'cli');
    }
    public function run()
    {
        if ($this->cli()) {
            return $this->run_cli();
        }
        return $this->run_http();
    }
    protected function run_cli()
    {
        global $language;
        $language = 'en';
        $options = getopt('c:');
        if (! $options) {
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
                $this->deps['response']->addReplacement('remote_addr', $_SERVER['REMOTE_ADDR']);
        $this->deps['response']->addReplacement(
            'memory_usage',
                                        round(memory_get_peak_usage(true)/1048576, 2)
        );
        $this->deps['response']->addReplacement(
            'execution_time',
                                        $this->deps['stopwatch']->mark('stop')->laptime('start', 'stop')
        );
                echo $payload = $this->deps['response']->flush();
        if ($this->deps['cache']->isActive()) {
            $this->deps['cache']->savePage($this->deps['request']->url(), $payload);
        }
    }
    public function stop(array $config = array())
    {
        if (isset($config['logger']['active']) && $config['logger']['active']) {
            $writer_name = 'Writer_'.ucfirst(strtolower($config['logger']['writer']));
            Cinnebar_Logger::instance()->write(new $writer_name);
        }
    }
}
class Cinnebar_Config
{
    public $config = array();
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    public function getSetting($token)
    {
        if ( ! isset($this->config[$token])) return null;
        return $this->config[$token];
    }
}
class Cinnebar_Logger
{
    const DEFAULT_LOG = 'general';
    private static $instance;
    public $logs = array();
    public static function instance()
    {
        if ( ! isset(self::$instance)) self::$instance = new Cinnebar_Logger();
        return self::$instance;
    }
    private function __construct()
    {
    }
    protected function __clone()
    {
    }
    public function clearAll()
    {
        $this->logs = array();
    }
    public function log($message, $log = self::DEFAULT_LOG)
    {
        $this->logs[$log][] = $message;
        return true;
    }
    public function write(Cinnebar_Writer $writer)
    {
        return $writer->write($this->logs);
    }
}
class Cinnebar_Writer
{   
    public $options = array();
    public function __construct(array $options = array())
    {
        $this->options = $options;
    }
    public function write(array $logs = array())
    {
        return true;
    }
}
class Writer_Errorlog extends Cinnebar_Writer
{
    public function write(array $logs = array())
    {
        foreach ($logs as $section=>$lines) {
            foreach ($lines as $n=>$line)
            error_log(sprintf('%s: %s', $section, $line));
        }
        return true;
    }
}
class Writer_File extends Cinnebar_Writer
{
    const LOGFILE_EXTENSION = '_log';
    public $folder = 'logs';
    public function setFolder($path_to_logs)
    {
        $this->folder = $path_to_logs;
    }
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
class Cinnebar_Stopwatch
{
    public $marks = array();
    public function __construct()
    {
    }
    public function __get($mark)
    {
        if ( ! isset($this->marks[$mark])) return null;
        return $this->marks[$mark];
    }
    public function start()
    {
        $this->marks['start'] = microtime(true);
        return $this;
    }
    public function mark($mark)
    {
        $this->marks[$mark] = microtime(true);
        return $this;
    }
    public function laptime($mark1 = 'start', $mark2 = 'stop', $digits = 5)
    {
        if ( ! isset($this->marks[$mark1]) || ! isset($this->marks[$mark2])) return 0.0000;
        return round($this->marks[$mark2] - $this->marks[$mark1], $digits);
    }
}
class Cinnebar_Autoloader
{
    public $dirs = array(
        APPDIR,
        'cinnebar'
    );
    public function __construct()
    {
    }
    public function addDirectory($dir)
    {
        $this->dirs[] = $dir;
    }
    public function load($class)
    {
        $path = strtr(strtolower($class), '_\\', '//');
        if ($path_to_file = $this->load_workhorse($path)) {
            require $path_to_file;
            return true;
        }
        return false;
    }
    public function load_workhorse($path)
    {
        foreach ($this->dirs as $dir) {
            $fullpath = $dir.'/'.$path.'.php';
            if (is_file($fullpath)) return $fullpath;
        }
        return false;
    }
    public function register()
    {
        return spl_autoload_register(array($this, 'load'));
    }
    public function unregister()
    {
        return spl_autoload_unregister(array($this, 'load'));    
    }
}
class Cinnebar_Request
{
    const PROTOCOL_HTTP = 'http://';
    const PROTOCOL_HTTPS = 'https://';
    public function __construct()
    {
    }
    public function protocol()
    {
        if ( ! isset($_SERVER['HTTPS']) || ! $_SERVER['HTTPS']) return self::PROTOCOL_HTTP;
        return self::PROTOCOL_HTTPS;
    }
    public function host()
    {
        return $_SERVER['HTTP_HOST'];
    }
    public function port()
    {
        return '';
    }
	public function getOrPost()
	{
		if (count($_POST) == 0) return 'get';
		return 'post';
	}
	public function url()
	{
        return $this->protocol().$this->host().$_SERVER['REQUEST_URI'];
	}
	public function isAjax() {
	    return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
	}
}
class Cinnebar_Response
{
    public $headers = array();
    public $replacements = array();
    public $payload = '';
    public function __construct()
    {
    }
    public function start()
    {
		ob_start();
    }
    public function flush()
    {
        $this->payload = ob_get_contents();
        $this->replacements();
		ob_end_clean();
		$this->headers();
        return $this->payload;
    }
    public function addHeader($header, $content)
    {
        $this->headers[$header] = $content;
        return true;
    }
    public function addReplacement($token, $value)
    {
        $this->replacements[$token] = $value;
        return true;
    }
	public function headers()
	{
		foreach ($this->headers as $header=>$value)
		{
			header("{$header}: {$value}");
		}
	}
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
class Cinnebar_Router
{
    public $settings;
    public $scheme = 'http';
    public $host = 'localhost';
    public $directory = '';
    public $url;
    public $internal_url;
    public $slices = array();
    public $params = array();
    public $language = 'de';
    public $controller = 'welcome';
    public $method = 'index';
    public $map = array();
    public function __construct(array $settings = array())
    {
        $this->settings = $settings;
        if (isset($this->settings['map']) && is_array($this->settings['map'])) {
            $this->map = $this->settings['map'];
        }
    }
    public function setMap(array $map = array())
    {
        $this->map = $map;
        return true;
    }
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
    public function language()
    {
        return $this->language;
    }
    public function controller()
    {
        return $this->controller;
    }
    public function method()
    {
        return $this->method;
    }
    public function setMethod($method)
    {
        $this->method = $method;
    }
    public function params()
    {
        return $this->params;
    }
    public function setParams(array $params = array())
    {
        $this->params = $params;
    }
    public function scheme()
    {
        return $this->scheme;
    }
    public function basehref($omit = false)
    {
        if (true === $omit) return '/'.$this->directory.'/'.$this->language;
        return $this->scheme.'://'.$this->host().'/'.$this->directory().'/'.$this->language();
    }
    public function host()
    {
        return $this->host;
    }
    public function directory()
    {
        return $this->directory;
    }
    public function internalUrl()
    {
        return $this->internal_url;
    }
	protected function slice($index)
	{
		if ( ! isset($this->slices[$index])) return null;
		return $this->slices[$index];
	}
}
class Cinnebar_Controller extends Cinnebar_Element
{
    public $user = null;
    public function __construct()
    {
    }
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
    public function makeView($template)
    {
        $view = new Cinnebar_View($template);
        $view->controller($this);
        return $view;
    }
    public function redirect($url, $http_response_code = 302, $raw = false)
    {
        if ( ! $raw) $url = $this->router()->basehref().$url;
        header("Expires: 0"); 
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
        header("Cache-Control: private", false);         header('Location: '.$url, true, $http_response_code);
        exit;
    }
    public function request()
    {
        return $this->deps['request'];
    }
    public function response()
    {
        return $this->deps['response'];
    }
    public function router()
    {
        return $this->deps['router'];
    }
    public function input()
    {
        return $this->deps['input'];
    }
    public function cache()
    {
        return $this->deps['cache'];
    }
    public function permission()
    {
        return $this->deps['permission'];
    }
    public function user()
    {
        if ( ! $this->user || ! is_a($this->user, 'RedBean_OODBBean')) return R::dispense('user');
        return $this->user;
    }
    public function index()
    {
        echo 'It works?! Start writing your own controller, now.';
    }
}
class Controller_Scaffold extends Cinnebar_Controller
{
    const LIMIT = 23;
    const LAYOUT = 'table';
    public $type = 'token';
    public $typeAlias = null;
    public $action;
    public $path = 'shared/scaffold/';
    public $view;
    public $page;
    public $limit;
    public $layout;
    public $order;
    public $dir;
    public $sortdirs = array(
        'ASC',
        'DESC'
    );
    public $actions = array(
        'table' => array('expunge'),
        'edit' => array('next', 'prev', 'update', 'list'),
        'add' => array('continue', 'update', 'list')
    );
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
                $this->view->followup = null;
        if (isset($_SESSION['scaffold'][$this->view->action]['followup'])) {
            $this->view->followup = $_SESSION['scaffold'][$this->view->action]['followup'];
        }
                $this->view->nav = R::findOne('domain', ' blessed = ? LIMIT 1', array(1))->hierMenu($this->view->url());
        $this->view->navfunc = $this->view->record->makeMenu($action, $this->view, $this->view->nav);
        $this->view->urhere = with(new Cinnebar_Menu())->add(__($this->type.'_head_title'), $this->view->url(sprintf('/%s/index/%d/%d/%s/%d/%d', $this->type, 1, self::LIMIT, $this->view->layout, $this->view->order, $this->view->dir)));
    }
    protected function make_filter()
    {
        if ( ! isset($_SESSION['filter'][$this->type]['id'])) {
            $_SESSION['filter'][$this->type]['id'] = 0;
        }
        $filter = R::load('filter', $_SESSION['filter'][$this->type]['id']);
        if ( ! $filter->getId()) {
            $filter->rowsperpage = self::LIMIT;
            $filter->model = $this->type;             $filter->user = $this->view->user;             $filter->logic = 'AND';
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
    protected function record($id = null)
    {
        return R::load($this->type, $id);
    }
    protected function collection()
    {
    	$whereClause = $this->view->filter->buildWhereClause();
		$orderClause = $this->view->attributes[$this->order]['orderclause'].' '.$this->sortdir($this->dir);
		$sql = $this->view->record->sqlForFilters($whereClause, $orderClause, $this->offset($this->page, $this->limit), $this->limit);
		$this->view->total = 0;
		try {
						$assoc = R::$adapter->getAssoc($sql, $this->view->filter->filterValues());
						$this->view->records = R::batch($this->type, array_keys($assoc));
			            $this->view->total = R::getCell($this->view->record->sqlForTotal($whereClause), $this->view->filter->filterValues());
									return true;
		} catch (Exception $e) {
            Cinnebar_Logger::instance()->log('Scaffold Collection has issues: '.$e.' '.$sql, 'sql');
			$this->view->records = array();
			return false;
		}    
    }
    protected function offset($page, $limit)
    {
        return ($page - 1) * $limit;
    }
    protected function sortdir($dir = 0)
    {
        if ( ! isset($this->sortdirs[$dir])) return 'ASC';
        return $this->sortdirs[$dir];
    }
	public function trigger($action, $condition)
	{
	    $callback = $condition.'_'.$action;
        if ( ! method_exists($this, $callback)) return;
        $this->$callback();
	}
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
            R::begin();
            try {
                $record->$action();
                R::commit();
            } catch (Exception $e) {
                R::rollback();
                $valid = false;
            }
        }
        if ($valid) return count($pointers);
        return false;
	}
    public function report($page = 1, $limit = self::LIMIT, $layout = self::LAYOUT, $order = 0, $dir = 0)
    {
        $this->cache()->deactivate();
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/'.$this->router()->internalUrl())));
        if ( ! $this->permission()->allowed($this->user(), $this->type, 'index')) {
			return $this->error('403');
		}
        $this->env($page, $limit, $layout, $order, $dir, null, 'report');
        $this->trigger('report', 'before');
        if ($this->input()->post()) {
            if ($this->input()->post('otherreport')) {
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
                                    }
            }
            if ($this->input()->post('submit') == __('filter_submit_clear') && $_SESSION['filter'][$this->type]['id']) {
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
            $this->view->url(sprintf('/%s/index/', $this->typeOrTypeAlias() )),
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
    public function typeOrTypeAlias()
    {
        if ( ! $this->typeAlias ) return $this->type;
        return $this->typeAlias;
    }
    public function htmlpdf($page = 1, $limit = self::LIMIT, $layout = self::LAYOUT, $order = 0, $dir = 0)
    {
        $this->cache()->deactivate();
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/'.$this->router()->internalUrl())));
        if ( ! $this->permission()->allowed($this->user(), $this->type, 'index')) {
			return $this->error('403');
		}
        $this->env($page, $limit, $layout, $order, $dir, null, 'htmlpdf');
                $real_limit = $this->limit;
        $real_offset = $this->offset;
                $this->limit = R::count($this->type);        $this->offset = 0;
                $this->collection();
                $this->limit = $real_limit;
        $this->offset = $real_offset;
    	require_once BASEDIR.'/vendors/mpdf/mpdf.php';
        $docname = 'Cards as PDF';
        $filename = 'Liste.pdf';
        $mpdf = new mPDF('c', 'A4');
        $mpdf->SetTitle($docname);
        $mpdf->SetAuthor( 'von Rohr' );
        $mpdf->SetDisplayMode('fullpage');
        $html = $this->view->render();
        $mpdf->WriteHTML( $html );
        $mpdf->Output($filename, 'D');
        exit;
    }
    public function press($page = 1, $limit = self::LIMIT, $layout = self::LAYOUT, $order = 0, $dir = 0)
    {
        $this->cache()->deactivate();
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/'.$this->router()->internalUrl())));
        if ( ! $this->permission()->allowed($this->user(), $this->type, 'index')) {
			return $this->error('403');
		}
        $this->env($page, $limit, $layout, $order, $dir, null, 'index');
                $real_limit = $this->limit;
        $real_offset = $this->offset;
                $this->limit = R::count($this->type);        $this->offset = 0;
                $this->collection();
                $this->limit = $real_limit;
        $this->offset = $real_offset;
        $data = array();
                foreach ($this->view->records as $id => $record) {
            $data[] = $record->exportToCSV(false, $layout);
        }
        require_once BASEDIR.'/vendors/parsecsv-0.3.2/parsecsv.lib.php';
        $csv = new ParseCSV();
        $csv->output(true, __($this->view->record->getMeta('type').'_head_title').'.csv', $data, $this->view->record->exportToCSV(true, $layout));
        exit;
    }
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
				$dup->name = $dup->name . ' Kopie';
		try {
		    $dup->validationMode(Cinnebar_Model::VALIDATION_MODE_IMPLICIT);
		    $dup->prepareForDuplication();
		    R::store($dup);
		} catch (Exception $e) {
            Cinnebar_Logger::instance()->log($e, 'exceptions');
		}
	    	    $this->redirect(sprintf('/%s/edit/%d/', $dup->getMeta('type'), $dup->getId()));
    }
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
                            }
        }
        $this->view->records = array();
        $this->trigger('add', 'after');
        echo $this->view->render();
    }
    public function import($id = null, $page = 0)
    {
        $this->cache()->deactivate();
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/'.$this->router()->internalUrl())));
        if ( ! $this->permission()->allowed($this->user(), $this->type, 'import')) {
			return $this->error('403');
		}
        $this->env($page, 0, 0, 0, 0, $id, 'import');         
                $this->view->record = R::load('import', $id);
        $this->view->record->model = $this->type;         $this->view->csv = $this->view->record->csv($this->view->page);
        if ($this->input()->post()) {
            $this->view->record = R::graph($this->input()->post('dialog'), true);
            try {
                R::store($this->view->record);
                if ($this->input()->post('submit') == __('scaffold_submit_import')) {
                    $message = __('action_import_success');
                    with(new Cinnebar_Messenger)->notify($this->user(), $message, 'success');
                                                        }
                elseif ($this->input()->post('submit') == __('import_submit_prev')) {
                    $this->view->page = max(0, $this->view->page - 1);
                }
                elseif ($this->input()->post('submit') == __('import_submit_next')) {
                    $this->view->page = min($this->view->csv['max_records'] - 1, $this->view->page + 1);
                }
                elseif ($this->input()->post('submit') == __('import_submit_execute')) {
                                        R::begin();
                    try {
                        $imported_ids = $this->view->record->execute();                         $message = __('action_imported_n_of_m_success', array(count($imported_ids), count($this->view->csv['records'])));
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
        echo $this->view->render();
    }
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
    protected function make_pageflip()
    {
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
    public function getType()
    {
        if ( ! $this->typeAlias) return $this->type;
        return $this->typeAlias;
    }
    protected function id_at_offset($offset)
    {
        $offset--;         if ($offset < 0) return false;
        $whereClause = $this->view->filter->buildWhereClause();
		$orderClause = $this->view->attributes[$this->order]['orderclause'].' '.$this->sortdir($this->dir);
    	$sql = $this->view->record->sqlForFilters($whereClause, $orderClause, $offset, 1);
    	try {
    		return R::getCell($sql, $this->view->filter->filterValues());
    	} catch (Exception $e) {
            return false;
    	}
    }
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
    protected function pushEnabledLanguagesToView()
    {
        $this->view->languages = R::dispense('language')->enabled();
    }
}
abstract class Cinnebar_Command
{
    public $args = array();
    public $flags = array();
    public function __construct()
    {
    }
    abstract public function execute();
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
    public function flag($name)
    {
        return isset($this->flags[$name]) ? $this->flags[$name] : false;
    }
    public function input($message = '')
    {
        fwrite(STDOUT, $message);
        return trim(fgets(STDIN));
    }
    public function error($error)
    {
        $view = $this->makeView('command/error');
        $view->error = $error;
        echo $view->render();
    }
    public function makeView($template)
    {
        $view = new Cinnebar_View($template);
        return $view;
    }
}
class Cinnebar_Cache
{
    public $settings = array();
    public function __construct(array $settings = array('active' => false, 'ttl' => 300))
    {
        $this->settings = $settings;
    }
    public function hashUrl($url)
    {
        return md5($url);
    }
    public function clearAll()
    {
        return $this->clear('page_*.html');
    }
    public function clear($pattern)
    {
        return true;
    }
    public function isActive()
    {
        if ( ! isset($this->settings['active'])) return false;
        if ( ! $this->settings['active']) return false;
        if ( ! isset($this->settings['ttl'])) return false;
        if ($this->settings['ttl'] <= 0) return false;
        return true;
    }
    public function setActive($switch)
    {
        $this->settings['active'] = $switch;
    }
    public function deactivate()
    {
        $this->settings['active'] = false;
    }
    public function activate()
    {
        $this->settings['active'] = true;
    }
    public function isCached($url)
    {
        if ( ! $this->isActive()) return false;
        $file = $this->filename($url);
		if ( ! is_file($file)) return false;
		clearstatcache();
        if (filemtime($file) <= (time() - $this->settings['ttl'])) return false;
        return $file;
    }
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
    public function filename($url)
    {
        return BASEDIR.'/cache/page_'.$this->hashUrl($url).'.html';
    }
}
class Cinnebar_Input
{
    public function __construct()
    {
        $this->gpc_magic_quotes_repair();
    }
    protected function gpc_magic_quotes_repair()
    {
        if ( ! get_magic_quotes_gpc()) return;         Cinnebar_Logger::instance()->log('gpc_magic_quotes should be OFF, but are ON', 'warn');
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
    public function post($token = null)
    {
        if ($token === null && ! empty($_POST)) return true;
        if ( ! isset($_POST[$token])) return null;
        return $this->sanatized($_POST[$token]);
    }
    public function get($token = null)
    {
        if ($token === null && ! empty($_GET)) return true;
        if ( ! isset($_GET[$token])) return null;
        return $this->sanatized($_GET[$token]);
    }
    public function sanatized($value)
    {
        return $value;
    }
}
class Cinnebar_Permission implements iPermission
{
	public function __construct()
	{
	}
	public function allowed($user = null, $domain, $action)
	{
		if ( ! $user || ! $user->getId()) return false;
		if ($user->admin) return true;
		$this->load($user);
		if (isset($_SESSION['permissions'][$domain][$action]))
			return (bool)$_SESSION['permissions'][$domain][$action];
		return false;
	}
	public function domains($user, $action)
	{
		if ( ! $user || ! $user->getId()) {
			return array();
		}
		$this->load($user);
		$ret = array();
		foreach ($_SESSION['permissions'] as $domain=>$actions) {
			if (isset($actions[$action]) && $actions[$action]) {
				$ret[$domain] = __('domain_'.$domain); 			}
		}
		asort($ret);
		return $ret;
	}
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
class Cinnebar_Plugin
{
    public $controller;
    public function __construct(Cinnebar_Controller $controller)
    {
        $this->controller = $controller;
    }
    public function controller()
    {
        return $this->controller;
    }
}
class Plugin_Attach extends Cinnebar_Plugin
{
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
class Plugin_Detach extends Cinnebar_Plugin
{
	public function execute($prefix, $type, $id = 0, $master_id = 0)
	{
        session_start();
        $this->controller()->cache()->deactivate();
		return $this->$prefix($type, $id, $master_id);
	}
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
class Plugin_Error extends Cinnebar_Plugin
{
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
class Cinnebar_Hash
{
    public $hash_algo;
    public $salt = '&5889Hghgjhj5%&%/ftddsop==9987897';
    public function __construct($hash_algo = null)
    {
        if (null === $hash_algo) $hash_algo = 'md5';
        $this->hash_algo = $hash_algo;
    }
    public function HashPassword($pw)
    {
        $callback = $this->hash_algo;
        return $callback($this->salt.$pw);
    }
    public function CheckPassword($pw, $pw_stored)
    {
        return ($this->HashPassword($pw) == $pw_stored);
    }
}
class Cinnebar_Element
{
    public $deps = array();
    const CHECKED = ' checked="checked"';
    const SELECTED = ' selected="selected"';
    const DISABLED = ' disabled="disabled"';
    const READONLY = ' readonly="readonly"';
    const DISPLAY_BLOCK = 'block';
    const DISPLAY_NONE = 'none';
    public $errors = array();
    public $data = array();
    public function __construct()
    {
    }
    public function __set($attribute, $value = null)
    {
        $this->data[$attribute] = $value;
    }
    public function __unset($attribute)
    {
        unset($this->data[$attribute]);
    }
    public function __isset($attribute)
    {
        return isset($this->data[$attribute]);
    }
    public function __get($attribute)
    {
        if (array_key_exists($attribute, $this->data)) return $this->data[$attribute];
        return null;
    }
    public function di(array $deps)
    {
        $this->deps = $deps;
    }
    public function addError($error_text, $error_type = '')
    {
        $this->errors[$error_type][] = $error_text;
        return true;
    }
    public function hasErrors()
    {
        return count($this->errors);
    }
    public function errors()
    {
        return $this->errors;
    }
    public function sanitizeFilename($string = '', $is_filename = false)
    {
        $string = preg_replace('/[^\w\-'. ($is_filename ? '~_\.' : ''). ']+/u', '-', $string);
        return mb_strtolower(preg_replace('/--+/u', '-', $string));
    }
    public static function glue($dict, $glueOpen = '="', $glueClose = '"', $pre = ' ', $impChar = ' ')
    {
    	if (empty($dict)) return '';
    	$stack = array();
    	foreach ($dict as $key=>$value) {
    		$stack[] = $key.$glueOpen.htmlspecialchars($value).$glueClose;
    	}
    	return $pre.implode($impChar, $stack);
    }
    public static function stripped($source, array $tokens = array('[', ']'), array $replacements = array('-', ''))
    {
        return str_replace($tokens, $replacements, $source);
    }
}
class Cinnebar_Upload extends Cinnebar_Element
{
    public $config;
    public $filename;
    public $sanitizedFilename;
    public $extension;
    public $dir;
    public $unchanged = false;
    public function __construct()
    {
        global $config;
        $this->config = $config['upload'];
    }
    public function unchanged()
    {
        return $this->unchanged;
    }
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
    public function allowedExtension($allowed, $extension)
    {
        if ($allowed === null) return true;
        if ( ! is_array($allowed)) $allowed = array($allowed);
        return (in_array($extension, $allowed));
    }
    protected function analyzeFilename($container = 'upload')
    {
        $file_parts = pathinfo($_FILES[$container]['name']);
        $this->filename = $file_parts['filename'];
        $this->extension = mb_strtolower($file_parts['extension']);
        $this->sanitizedFilename = $this->sanitizeFilename($this->filename);
    }
}
class Cinnebar_View extends Cinnebar_Element
{
    public $controller;
    public $template;
    public $stylesheets = array();
    public $javascripts = array();
    public function __construct($template)
    {
        $this->template = $template;
    }
    public function controller($controller = null)
    {
        if ($controller === null) return $this->controller;
        $this->controller = $controller;
        return $this->controller;
    }
    public function user()
    {
        return $this->controller()->user();
    }
    public function language()
    {
        return $this->controller()->router()->language();
    }
    public function title()
    {
        if (isset($this->title)) return $this->title;
        return $this->controller()->router()->basehref();
    }
    public function basehref()
    {
        return $this->controller()->router()->basehref();
    }
    public function resetStyles()
    {
        $this->stylesheets = array();
        return true;
    }
    public function addStyle($files)
    {
        if ( ! is_array($files)) $files = array($files);
        foreach ($files as $file) {
            $this->stylesheets[] = $file;
        }
        return true;
    }
    public function styles()
    {
        return $this->stylesheets;
    }
    public function resetJs()
    {
        $this->javascripts = array();
        return true;
    }
    public function addJs($files)
    {
        if ( ! is_array($files)) $files = array($files);
        foreach ($files as $file) {
            $this->javascripts[] = $file;
        }
        return true;
    }
    public function js()
    {
        return $this->javascripts;
    }
    public function __call($method, array $params = array())
    {
        $helper_name = 'Viewhelper_'.ucfirst(strtolower($method));
        $helper = new $helper_name($this);
        return call_user_func_array(array($helper, 'execute'), $params);
    }
    public function partial($partial, array $values = array())
    {
        return $this->render_workhorse($partial, array_merge($this->data, $values));
    }
    public function render($template = null)
    {
        if ( $template === null) $template = $this->template;
        return $this->render_workhorse($template, $this->data);
    }
    public function exists($template)
    {
        $file = BASEDIR.'/themes/'.S_THEME.'/templates/'.$template.'.php';
        if ( ! is_file($file)) {
            Cinnebar_Logger::instance()->log(sprintf('Template "%s" not found', $template), 'warn');
            return false;
        }
        return $file;
    }
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
class Cinnebar_Menu extends Cinnebar_Element
{
    public $templates = array(
        'list-open' => '<ul%s>',
        'item-open' => '<li %s %s><a href="%s">%s</a>',         'item-close' => '</li>',
        'list-close' => '</ul>'
    );
    public $items = array();
    public function __toString()
    {
        return $this->render();
    }
    public function setTemplate($name, $template)
    {
        $this->templates[$name] = $template;
        return $this;
    }
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
	protected static function current($current, array $item)
	{
		if ($current === $item['url']) {
			return 'active current';
		} else {
		    if (self::active($item, $current, 'url')) return 'active';
		}
		return '';
	}
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
class Cinnebar_Pagination
{
	public $url;
	public $order;
	public $dir;
	public $page;
	public $max_pages;
	public $limit;
	public $total_rows;
	public $adjacents = 2;
	public $has_previous_page = false;
	public $has_next_page = false;
	public $page_links = true;
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
	public function __toString()
	{
        return $this->render();
	}
	public function render()
	{
		$this->calculate();
		return $this->build_html_pagination();
	}
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
	protected function build_html_pagination()
	{
		if ($this->max_pages == 1)
		{
						return '';
		}
		$s = '<ul>'."\n";
				if ($this->has_previous_page)
		{
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
						$s .= '<li class="prev">'.__('pagination_page_prev').'</li>'."\n";
		}
				if ($this->page_links) $s .= $this->build_html_page_links();
				if ($this->has_next_page)
		{
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
						$s .= '<li class="next">'.__('pagination_page_next').'</li>'."\n";
		}
		$s .= '</ul>'."\n";
		return $s;
	}
	protected function build_html_page_links()
	{
		$s = '';
		if ($this->max_pages < 7 + ($this->adjacents * 2))
		{
				        for ($n = 1; $n <= $this->max_pages; $n++)
			{
				$s .= '<li>';
	            if ($n != $this->page)
				{
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
		elseif ($this->max_pages >= 7 + ($this->adjacents * 2))
		{
						if ($this->page < 1 + ($this->adjacents * 3))
			{
					            for ($n = 1; $n < 4 + ($this->adjacents * 2); $n++)
				{
					$s .= '<li>';
	                if ($n != $this->page)
					{
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
			elseif ($this->max_pages - ($this->adjacents * 2) > $this->page && $this->page > ($this->adjacents * 2))
			{
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
	public function ahref($url, $text)
	{
        return sprintf('<a href="%s">%s</a>', $url, $text);
	}
}
class Cinnebar_Viewhelper
{
    public $view;
    public function __construct(Cinnebar_View $view)
    {
        $this->view = $view;
    }
    public function view()
    {
        return $this->view;
    }
}
class Viewhelper_Url extends Cinnebar_Viewhelper
{
    public function execute($url = '', $type = 'href')
    {
        if ($type == 'href') return $this->view()->basehref().$url;
        return $this->view()->basehref().'/../themes/'.S_THEME.'/'.$type.'/'.$url.'.'.$type;
    }
}
require_once BASEDIR.'/vendors/textile/classTextile.php';
class Viewhelper_Textile extends Cinnebar_Viewhelper
{
    public function execute($text = '', $restricted = false)
    {
        if (empty($text)) return '';
        $textile = new Textile();
        if ( ! $restricted) return trim($textile->TextileThis($text));
        return trim($textile->TextileRestricted($text));
    }
}
class Cinnebar_Messenger
{
    public function __construct()
    {
    }
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
    public function notifications(RedBean_OODBBean $bean, $sql = '', array $values = array(), $trash = true)
    {
        $all = R::related($bean, 'notification', $sql, $values);
        if ($trash) R::trashAll($all);
        return $all;
    }
}
class Cinnebar_Model extends RedBean_SimpleModel
{
    const VALIDATION_MODE_EXCEPTION = 1;
    const VALIDATION_MODE_IMPLICIT = 2;
    const VALIDATION_MODE_EXPLICIT = 4;
    private $auto_tag = false;
    private $auto_info = false;
    protected $errors = array();
    protected static $validation_mode = self::VALIDATION_MODE_EXCEPTION;
    private $template = 'default';
    private $validators = array();
    private $valid = true;
    private $converters = array();
    public function __construct()
    {
    }
    public function hitname(Cinnebar_View $view)
    {
        $template = '<a href="%s">%s</a>'."\n";
        return sprintf($template, $view->url(sprintf('/%s/edit/%d', $this->bean->getMeta('type'), $this->bean->getId())), $this->bean->getId());
    }
    public function render($template, Cinnebar_View $view)
    {
        return $view->partial(sprintf('model/%s/%s', $this->bean->getMeta('type'), $template), array('record' => $this->bean));
    }
    public function own($type, $add = false)
    {
        $own_type = 'own'.ucfirst(strtolower($type));
        if (method_exists($this, 'get'.$own_type)) {
            $own_type = 'get'.$own_type;
            return $this->$own_type($add);
        }
        $own = $this->bean->$own_type;
        if ($add) {
            $own[] = R::dispense($type);
        }
        return $own;
    }
    public function shared($type, $add = false)
    {
        $shared_type = 'shared'.ucfirst(strtolower($type));
        $shared = $this->bean->$shared_type;
        if ($add) {
            $shared[] = R::dispense($type);
        }
        return $shared;
    }
    public function isI18n()
    {
        return false;
    }
    public function i18n($iso = null)
    {
        global $language, $config;
        if ($iso === null && isset($_SESSION['backend']['language'])) {
            $iso = $_SESSION['backend']['language'];
        } elseif ($iso === null) {
            $iso = $language;
        }
        $i18n_type = $this->bean->getMeta('type').'i18n';
        if (! $i18n = R::findOne($i18n_type, $this->bean->getMeta('type').'_id = ? AND iso = ? LIMIT 1', array($this->bean->getId(), $iso))) {
            $i18n = R::dispense($i18n_type);
            $i18n->iso = $iso;
        }
        return $i18n;
    }
    public function splitToWords($text)
    {
        return preg_split('/((^\p{P}+)|(\p{P}*\s+\p{P}*)|(\p{P}+$))/', $text, -1, PREG_SPLIT_NO_EMPTY);
    }
    public function alphanumericonly($text)
    {
        return preg_replace("/[^a-zA-Z0-9]+/", "", $text);
    }
    public function translated($attribute, $iso = null)
    {
        return $this->i18n($iso)->$attribute;
    }
    public function validationMode($mode = null)
    {
        if ($mode !== null) {
            self::$validation_mode = $mode;
        }
        return self::$validation_mode;
    }
    public function expunge()
    {
        R::trash($this->bean);
    }
    public function update()
    {
        $this->convert();
        $this->validate();
    }
    public function after_update()
    {
        $this->info_workhorse();
        $this->tag_workhorse();
    }
    public function deleted()
    {
        return $this->bean->deleted;
    }
    public function open()
    {
    }
    public function delete()
    {
    }
    public function after_delete()
    {
    }
    public function dispense()
    {
    }
    public function setAutoTag($flag)
    {
        return $this->auto_tag = $flag;
    }
    public function autoTag()
    {
        return $this->auto_tag;
    }
    public function setAutoInfo($flag)
    {
        return $this->auto_info = $flag;
    }
    public function autoInfo()
    {
        return $this->auto_info;
    }
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
    public function exportToCSV($header = false)
    {
        if ($header === true) {
            return array(
            );
        }
        return $this->bean->export();
    }
    public function actionAsHumanText($action = 'idle', $type = 'success', $user = null)
    {
        $subject = __('you');
        if (is_a($user, 'RedBean_OODBBean')) {
            $subject = $user->name();
        }
        return __('action_'.$action.'_on_'.$this->bean->getMeta('type').'_'.$type, array($subject));
    }
    public function makeActions(array $actions = array())
    {
        return $actions;
    }
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
    public function layouts()
    {
        return array('table');
    }
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
    public function keywords()
    {
        return array();
    }
    public function clairvoyant($term, $layout = 'default')
    {
        $result = R::getAll(sprintf('select id as id, id as label, id as value from %s', $this->bean->getMeta('type')));
        return $result;
    }
    public function addError($errorText, $attribute = '')
    {
        $this->errors[$attribute][] = $errorText;
    }
    public function setErrors(array $errors = array())
    {
        $this->errors = $errors;
    }
    public function errors()
    {
        return $this->errors;
    }
    public function info()
    {
        if (! $this->autoInfo()) {
            return R::dispense('info');
        }
        if (! $this->bean->getId()) {
            return R::dispense('info');
        }
        try {
            $relation = array($this->bean->getMeta('type'), 'info');
            asort($relation);             $info_relation = implode('_', $relation);
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
        if (! $info->getId()) {
            $info = R::dispense('info');
        }
        return $info;
    }
    public function csvImport(RedBean_OODBBean $import, array $data, array $mappers)
    {
        foreach ($mappers as $id=>$map) {
            if ($map->target == '__none__') {
                continue;
            }             if (empty($data[$map->source]) && ! empty($map->default)) {
                $this->bean->{$map->target} = $map->default;
            } else {
                $this->bean->{$map->target} = $data[$map->source];
            }
        }
    }
    public function invalid()
    {
        if (isset($this->bean->invalid) && $this->bean->invalid) {
            return true;
        }
        return false;
    }
    public function meta()
    {
        if (! $this->bean->meta) {
            $this->bean->meta = R::dispense('meta');
        }
        return $this->bean->meta;
    }
    public function parent()
    {
        $fn_parent = $this->bean->getMeta('type').'_id';
        if (! $this->bean->$fn_parent) {
            return R::dispense($this->bean->getMeta('type'));
        }
        return R::load($this->bean->getMeta('type'), $this->bean->$fn_parent);
    }
    public function children($orderfields = 'id', $criteria = null)
    {
        $fn_parent = $this->bean->getMeta('type').'_id';
        return R::find($this->bean->getMeta('type'), sprintf('%s = ? %s ORDER BY %s', $fn_parent, $criteria, $orderfields), array($this->bean->getId()));
    }
    public function bubble($attribute)
    {
        $fn_parent = $this->bean->getMeta('type').'_id';
        if (! $this->bean->$fn_parent) {
            return $this->bean->$attribute;
        }
        if ($this->bean->$attribute) {
            return $this->bean->$attribute;
        }
        $parent = R::load($this->bean->getMeta('type'), $this->bean->$fn_parent);
        if (! $parent->getId()) {
            return null;
        }
        return $parent->bubble($attribute);
    }
    public function hasError($attribute = '')
    {
        if ($attribute === '') {
            return ! empty($this->errors);
        }
        return isset($this->errors[$attribute]);
    }
    public function hasErrors()
    {
        return $this->hasError();
    }
    public function getErrors()
    {
        return $this->errors;
    }
    public function convert()
    {
        if (empty($this->converters)) {
            return;
        }
        foreach ($this->converters as $attribute=>$callbacks) {
            foreach ($callbacks as $n=>$param) {
                $converter_name = 'Converter_'.ucfirst(strtolower($param['converter']));
                $converter = new $converter_name($this->bean, $param['options']);
                $this->bean->$attribute = $converter->execute($this->bean->$attribute);
            }
        }
    }
    public function addConverter($attribute, $converter, array $options = array())
    {
        $this->converters[$attribute][] = array(
            'converter' => $converter,
            'options' => $options
        );
    }
    public function validate()
    {
        if (isset($this->invalid) && $this->invalid) {
            $this->invalid = false;
        }
        if ($valid = $this->validate_workhorse()) {
            return true;
        }
        if (self::VALIDATION_MODE_EXCEPTION === self::$validation_mode) {
            throw new Exception(__CLASS__.'_invalid: '.$this->bean->getMeta('type'));
        }
        if (self::VALIDATION_MODE_IMPLICIT === self::$validation_mode) {
            $this->invalid = true;
        }
        return false;
    }
    public function addValidator($attribute, $validator, array $options = array())
    {
        $this->validators[$attribute][] = array(
            'validator' => $validator,
            'options' => $options
        );
    }
    protected function validate_workhorse()
    {
        if (empty($this->validators)) {
            return true;
        }
        $state = true;
        foreach ($this->validators as $attribute=>$callbacks) {
            foreach ($callbacks as $n=>$param) {
                $validator_name = 'Validator_'.ucfirst(strtolower($param['validator']));
                $validator = new $validator_name($param['options']);
                if (! $validator->execute($this->bean->$attribute)) {
                    $state = false;
                    $this->addError(__(sprintf('%s_%s_%s_invalid', $this->bean->getMeta('type'), $attribute, strtolower($param['validator']))), $attribute);
                }
            }
        }
        return $state;
    }
    protected function info_workhorse()
    {
        if (! $this->autoInfo()) {
            return false;
        }
        if (! $this->bean->getId()) {
            return false;
        }
        $info = R::dispense('info');
        $user = R::dispense('user')->current();
        if ($user->getId()) {
            $info->user = $user;
        }
        $info->stamp = time();
        try {
            R::store($info);
            R::associate($this->bean, $info);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    protected function tag_workhorse()
    {
        if (! $this->autoTag()) {
            return false;
        }
        if (! $this->bean->getId()) {
            return false;
        }
        $tags = array();
        foreach ($this->keywords() as $n=>$keyword) {
            if (empty($keyword)) {
                continue;
            }
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
class Model_Setting extends Cinnebar_Model
{
    public function blessedfolder()
    {
        if ( ! $this->bean->fetchAs('domain')->blessedfolder) $this->bean->blessedfolder = R::dispense('domain');
        return $this->bean->blessedfolder;
    }
    public function feebase()
    {
        if ( ! $this->bean->fetchAs('pricetype')->feebase) $this->bean->feebase = R::dispense('pricetype');
        return $this->bean->feebase;
    }
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
	public function layouts()
	{
        return array();
	}
    public function keywords()
    {
        return array(
        );
    }
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
    public function dispense()
    {
        $this->addValidator('blessedfolder', 'hasvalue');
        $this->addValidator('feebase', 'hasvalue');
        $this->setAutoInfo(true);
    }
}
class Model_Country extends Cinnebar_Model
{
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
 	public function makeMenu($action, Cinnebar_View $view, Cinnebar_Menu $menu = null)
	{
        $menu = parent::makeMenu($action, $view, $menu);
        return $menu;
	}
	public function layouts()
	{
        return array('table');
	}
    public function keywords()
    {
        return array(
            $this->bean->iso,
            $this->bean->name
        );
    }
    public function dispense()
    {
                        $this->addValidator('iso', 'hasvalue');
        $this->addValidator('iso', 'isunique', array('bean' => $this->bean, 'attribute' => 'iso'));
    }
}
class Model_Tag extends Cinnebar_Model
{
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
class Model_Info extends Cinnebar_Model
{
    public function user()
    {
        if ( ! $this->bean->user) return R::dispense('user');
        return $this->bean->user;
    }
    public function dispense()
    {
        $this->action = 'edit';
    }
}
class Model_Token extends Cinnebar_Model implements iToken
{
    public function translated($attribute, $iso = null)
    {
        return $this->in($iso)->$attribute;
    }
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
    public function isI18n()
    {
        return false;
    }
    public function sqlForTotal($where_clause = '1')
    {
        global $language; 
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
    public function sqlForFilters($where_clause = '1', $order_clause = 'id', $offset = 0, $limit = 1)
    {
        global $language; 
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
 	public function makeMenu($action, Cinnebar_View $view, Cinnebar_Menu $menu = null)
	{
        $menu = parent::makeMenu($action, $view, $menu);
        return $menu;
	}
	public function layouts()
	{
        return array('table');
	}
    public function keywords()
    {
        return array(
            $this->bean->name
        );
    }
    public function dispense()
    {
                        $this->setAutoInfo(true);
        $this->addValidator('name', 'hasvalue');
        $this->addValidator('name', 'isunique', array('bean' => $this->bean, 'attribute' => 'name'));
    }
}
class Model_Language extends Cinnebar_Model implements iLanguage
{
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
 	public function makeMenu($action, Cinnebar_View $view, Cinnebar_Menu $menu = null)
	{
        $menu = parent::makeMenu($action, $view, $menu);
        return $menu;
	}
	public function layouts()
	{
        return array('table');
	}
    public function enabled()
    {
        return R::find('language', ' enabled = ? ORDER BY iso', array(1));
    }
    public function keywords()
    {
        return array(
            $this->bean->iso,
            $this->bean->name
        );
    }
    public function dispense()
    {
                        $this->addValidator('iso', 'hasvalue');
        $this->addValidator('iso', 'isunique', array('bean' => $this->bean, 'attribute' => 'iso'));
    }
}
class Model_User extends Cinnebar_Model
{
    private $unknown_user = array(
        'nickname' => 'Nobody',
        'name' => 'Nobody',
        'email' => 'nobody@example.com',
        'pw' => 'secret',
        'home' => '/home',
        'admin' => false
    );
    private $hasher;
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
    public function isI18n()
    {
        return true;
    }
    public function hasRole($role)
    {
        if ( ! $role = R::findOne('role', ' name = ? LIMIT 1', array($role))) return false;
        return R::areRelated($this->bean, $role);
    }
    public function getFirstTeam()
    {
        $teams = $this->bean->sharedTeam;
        return array_shift( $teams );
    }
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
                $assoc = R::$adapter->getAssoc($sql, $roles);
                return R::batch('user', array_keys($assoc));
    }
    public function language()
    {
        return $_SESSION['backend']['language'];
    }
    public function current()
    {
        if ( ! isset($_SESSION['user']['id'])) {
            $this->bean->import($this->unknown_user);
            return $this->bean;
        }
        return $this->bean = R::load('user', $_SESSION['user']['id']);
    }
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
    public function notifications($sql = '', array $values = array())
    {
        $all = R::related($this->bean, 'notification', $sql, $values);
        R::trashAll($all);
        return $all;
    }
    public function name()
    {
        return $this->bean->email;
    }
    public function keywords()
    {
        return array(
            $this->bean->email,
            $this->bean->name
        );
    }
    public function home($goto = '')
    {
        if ($goto) return $goto;
        return $this->bean->home;
    }
    public function update()
    {
        if ( ! $this->bean->getId()) {
            $this->bean->pw = $this->hasher->HashPassword($this->bean->pw);
        }
        $this->bean->ego = md5($this->bean->email);
        parent::update();
    }
    public function dispense()
    {
                        $this->addValidator('email', 'isemail');
        $this->addValidator('email', 'isunique', array('bean' => $this->bean, 'attribute' => 'email'));
        $this->addValidator('name', 'hasvalue');
        $this->addValidator('name', 'isunique', array('bean' => $this->bean, 'attribute' => 'name'));
        $this->setAutoInfo(true);
    }
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
    public function deleted()
    {
        if ($this->bean->deleted) return true;
        return false;
    }
    public function banned()
    {
        if ($this->bean->banned) return true;
        return false;
    }
    public function logout()
    {
        $this->bean->sid = null;
        return true;
    }
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
 	public function makeMenu($action, Cinnebar_View $view, Cinnebar_Menu $menu = null)
	{
        $menu = parent::makeMenu($action, $view, $menu);
        return $menu;
	}
	public function layouts()
	{
        return array('table');
	}
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
class Model_Login extends Cinnebar_Model
{
}
class Model_Domain extends Cinnebar_Model
{  
    public function getownDomain($add)
    {
        $own = R::find('domain', ' domain_id = ? ORDER BY sequence, name', array($this->bean->getId()));
        if ($add) $own[] = R::dispense('domain');
        return $own;
    }
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
    public function name($lng = null)
    {
        if (empty($lng)) return $this->bean->name;
        return $this->bean->name.'('.$lng.')';
    }
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
 	public function makeMenu($action, Cinnebar_View $view, Cinnebar_Menu $menu = null)
	{
        $menu = parent::makeMenu($action, $view, $menu);
        return $menu;
	}
	public function layouts()
	{
        return array('table');
	}
    public function keywords()
    {
        return array($this->bean->name);
    }
    public function dispense()
    {
        if ( ! $this->bean->domain_id) $this->bean->domain_id = null;
        $this->setAutoInfo(true);
        $this->addValidator('name', 'hasvalue');
    }
    public function update()
    {
        if ( ! $this->bean->domain_id) $this->bean->domain_id = null;
        parent::update();
    }
}
class Model_Filter extends Cinnebar_Model
{
    public $filter_values = array();
    public function hasFilter(array $attributes)
    {
        foreach ($attributes as $attribute) {
            if (isset($attribute['filter']) && is_array($attribute['filter'])) return true;
        }
        return false;
    }
    public function buildWhereClause()
    {
        $criterias = $this->bean->ownCriteria;
        if (empty($criterias)) return '1';        
    	$where = array();
    	$this->filter_values = array();
    	$n = 0;
    	foreach ($criterias as $id=>$criteria) {
    	    if ( ! $criteria->op) continue;             if ( $criteria->value === null || $criteria->value === '') continue;     		$n++;
    		$logic = $this->bean->logic . ' ';
    		if ($n == 1) $logic = '';
    		$where[] = $logic.$criteria->makeWherePart($this);
    	}
    	if (empty($where)) return '1';    	
    	$where = implode(' ', $where);
    	return $where;
    }
    public function filterValues()
    {
        return $this->filter_values;
    }
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
    public function criterias()
    {
        return $this->bean->ownCriteria;
    }
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
    public function deprecated_orderClauses()
    {
        $filtered_bean = R::dispense($this->bean->model);
        return $filtered_bean->orderClauses();
    }
    public function dispense()
    {
        $this->addValidator('model', 'hasvalue');
    }
}
class Model_Criteria extends Cinnebar_Model
{
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
 		 		 		 	);
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
     public $pat = array('%', '_');
     public $rep = array('\%', '\_');
    public function makeWherePart(Model_Filter $filter)
    {
        if ( ! isset($this->map[$this->bean->op])) throw new Exception('Filter operator has no template');
        $template = $this->map[$this->bean->op];
        $value = $this->mask_filter_value($filter);
        return sprintf($template, $this->bean->attribute, $value);
    }
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
    public function operators()
    {
        if (isset($this->operators[$this->bean->tag])) return $this->operators[$this->bean->tag];
        return array();
    }
    public function getOperators($type = 'text')
    {
        if (isset($this->operators[$type])) return $this->operators[$type];
        return array();
    }
    public function dispense()
    {
        $this->addValidator('attribute', 'hasvalue');
    }
}
class Model_Action extends Cinnebar_Model
{
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
	public function layouts()
	{
        return array('table');
	}
    public function keywords()
    {
        return array(
            $this->bean->name
        );
    }
    public function dispense()
    {
                $this->addValidator('name', 'hasvalue');
        $this->addValidator('name', 'isunique', array('bean' => $this->bean, 'attribute' => 'name'));
    }
}
class Model_Role extends Cinnebar_Model
{
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
	public function layouts()
	{
        return array('table');
	}
    public function keywords()
    {
        return array(
            $this->bean->name
        );
    }
    public function dispense()
    {
                $this->addValidator('name', 'hasvalue');
        $this->addValidator('name', 'isunique', array('bean' => $this->bean, 'attribute' => 'name'));
    }
}
class Model_Team extends Cinnebar_Model
{
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
	public function layouts()
	{
        return array('table');
	}
    public function keywords()
    {
        return array(
            $this->bean->name
        );
    }
    public function dispense()
    {
                $this->addValidator('name', 'hasvalue');
        $this->addValidator('name', 'isunique', array('bean' => $this->bean, 'attribute' => 'name'));
    }
}
class Model_Session extends Cinnebar_Model
{
}
class Model_Module extends Cinnebar_Model
{
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
    public function enabled()
    {
        return R::find('module', ' enabled = ? ORDER BY name', array(1));
    }
 	public function makeMenu($action, Cinnebar_View $view, Cinnebar_Menu $menu = null)
	{
        $menu = parent::makeMenu($action, $view, $menu);
        return $menu;
	}
	public function layouts()
	{
        return array('table');
	}
    public function keywords()
    {
        return array(
            $this->bean->name
        );
    }
    public function dispense()
    {
                $this->setAutoInfo(true);
        $this->addValidator('name', 'hasvalue');
        $this->addValidator('name', 'isunique', array('bean' => $this->bean, 'attribute' => 'name'));
    }
}
class Cinnebar_Validator
{
    public $options = array();
    public function __construct(array $options = array()) {
        $this->options = $options;
    }
    public function execute($value)
    {
        return $value;
    }
}
class Validator_Hasvalue extends Cinnebar_Validator
{
    public function execute($value)
    {
        if (null === $value) return false;
        if (empty($value)) return false;
        return true;
    }
}
class Validator_Hasupload extends Cinnebar_Validator
{
    public function execute($filename)
    {
        global $config;
        $filename = $config['upload']['dir'].$filename;
        return is_file($filename);
    }
}
class Validator_Isdate extends Cinnebar_Validator
{
    public function execute($value)
    {
        if (preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $value, $matches)) {
            if (checkdate($matches[2], $matches[3], $matches[1])) return true;
        }
        return false;
    }
}
class Validator_Isemail extends Cinnebar_Validator
{
    public function execute($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }
}
class Validator_Isnumeric extends Cinnebar_Validator
{
    public function execute($value)
    {
        return (is_numeric($value));
    }
}
class Validator_Range extends Cinnebar_Validator
{
    public function execute($value)
    {
        if ( ! isset($this->options['min']) || ! isset($this->options['max'])) {
            throw new Exception('exception_validator_range_has_no_min_or_max');
        }
        return ($value >= $this->options['min'] && $value <= $this->options['max']);
    }
}
class Cinnebar_Formatter
{
    public function execute(RedBean_OODBBean $bean)
    {
        return $bean;
    }
}
class Cinnebar_Converter
{
    public $bean;
    public $options = array();
    public function __construct(RedBean_OODBBean $bean, array $options = array()) {
        $this->bean = $bean;
        $this->options = $options;
    }
    public function bean()
    {
        return $this->bean;
    }
    public function execute($value)
    {
        return $value;
    }
}
class Converter_Decimal extends Cinnebar_Converter
{
    public function execute($value)
    {
        return (float)str_replace(',', '.', $value);
    }
}
class Converter_Mysqldate extends Cinnebar_Converter
{
    public function execute($value)
    {
        if ( ! $value || empty($value) || $value == '0000-00-00') return '0000-00-00';
        return date('Y-m-d', strtotime($value));
    }
}
class Cinnebar_Module implements iModule
{
    public $backend = false;
    protected $bean;
    protected $view;
    public function __construct(Cinnebar_View $view, RedBean_OODBBean $bean)
    {
        $this->view = $view;
        $this->bean = $bean;
    }
    public function view()
    {
        return $this->view;
    }
    public function bean()
    {
        return $this->bean;
    }
    public function backend($switch = null)
    {
        if ( $switch !== null) $this->backend = $switch;
        return $this->backend;
    }
    public function execute()
    {
        if ($this->backend()) return $this->renderBackend();
        return $this->renderFrontend();
    }
    public function renderFrontend()
    {
        return 'Your code should render a slice bean in frontend mode.';
    }
    public function renderBackend()
    {
        return 'Your code should render a slice bean in backend mode.';
    }
}
abstract class Cinnebar_Migrator
{
    public $migrator;
    public $logname = 'migrator';
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
    public function open()
    {
        $this->migrator->start = time();
        return true;
    }
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
    public function useLegacyDB()
    {
        return R::selectDatabase('legacy');
    }
    public function useHeirDB()
    {
        return R::selectDatabase('heir');
    }
    public function useDefaultDB()
    {
        return R::selectDatabase('default');
    }
    abstract protected function prepare();
    abstract protected function cleanup();
    abstract protected function basic_claims();
    abstract protected function dynamic_claims();
}
class Cinnebar_Sessionhandler
{   
	public function open($path, $id)
    {
        return true;
    }
    public function close()
	{
	    return true;
	}
	public function read($id)
	{
        return '';
	}
    public function write($id, $data)
	{
        return true;
	}
    public function destroy($id)
	{
        return true;
	}
    public function gc($max_lifetime)
	{
        return true;
	}
}
class Sessionhandler_Apc extends Cinnebar_Sessionhandler
{
    public $prefix = 'CINNEBAR_SESS_';
	public function open($path, $id)
    {
        return true;
    }
    public function close()
	{
	    return true;
	}
	public function read($id)
	{
        if ( false == $session = apc_fetch($this->prefix.$id)) return '';
        return $session;
	}
    public function write($id, $data)
	{
        return apc_store($this->prefix.$id, $data);
	}
    public function destroy($id)
	{
        return apc_delete($this->prefix.$id);
	}
    public function gc($max_lifetime)
	{
        return true;
	}
}
class Sessionhandler_Database extends Cinnebar_Sessionhandler
{   
	public function open($path, $id)
    {
        return true;
    }
    public function close()
	{
	    return true;
	}
	public function read($id)
	{
	    if ( ! $session = R::findOne('session', ' token = ? LIMIT 1', array($id))) return '';
        return $session->data;
	}
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
    public function gc($max_lifetime)
	{
        return true;
	}
}
class Sessionhandler_Memory extends Cinnebar_Sessionhandler
{
    public $sessions = array();
	public function open($path, $id)
    {
        return true;
    }
    public function close()
	{
	    return true;
	}
	public function read($id)
	{
        if ( ! isset($this->sessions[$id])) return '';
        return $this->sessions[$id];
	}
    public function write($id, $data)
	{
	    $this->sessions[$id] = $data;
        return true;
	}
    public function destroy($id)
	{
        if (isset($this->sessions[$id])) unset ($this->sessions[$id]);
        return true;
	}
    public function gc($max_lifetime)
	{
        return true;
	}
}
class Cinnebar extends Cinnebar_Facade
{
}
