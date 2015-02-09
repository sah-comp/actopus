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
  * A basic command.
  *
  * To add your own comman simply add a php file to the command directory of your Cinnebar
  * installation. Name the command after the scheme Command_* extends Cinnebar_Command and
  * implement methods as you wish. You will not call a command directly, instead it is called
  * from the {@link Cinnebar_Facade} while a cli cycle runs.
  *
  * Example controller may look like this:
  * <code>
  * <?php
  * class Command_Example extends Cinnebar_Command
  * {
  *     public function execute() {
  *         // ...
  *         // ... your code here
  *         // ...
  *     }
  * }
  * ?>
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
     * <?php
     * // ...
     * // ... code in your command method
     * // ...
     * $view = $this->makeView('command/inspector');
     * // ...
     * // ... more code
     * // ...
     * ?>
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
