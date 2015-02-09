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
