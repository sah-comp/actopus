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
 * A basic controller.
 *
 * To add your own controller simply add a php file to the controller directory of your Cinnebar
 * installation. Name the controller after the scheme Controller_* extends Cinnebar_Controller and
 * implement methods as you wish. You will not call a controller directly, instead it is called
 * from the {@link Cinnebar_Facade} while a request/response cycle runs.
 *
 * Example controller may look like this:
 * <code>
 * <?php
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
 * ?>
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
        //error_log('Set session ts here?');
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
