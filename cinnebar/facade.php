<?php
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
    const RELEASE = '1.10';

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
        if ($this->cli()) {
            return $this->run_cli();
        }
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
        $this->deps['response']->addReplacement(
            'memory_usage',
                                        round(memory_get_peak_usage(true)/1048576, 2)
        );
        $this->deps['response']->addReplacement(
            'execution_time',
                                        $this->deps['stopwatch']->mark('stop')->laptime('start', 'stop')
        );
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
