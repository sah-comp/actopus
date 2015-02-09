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
 * Shows a welcome message on the command line.
 *
 * Usage examle from the command line
 * <code>
 * php -f index.php -- -c welcome -n Steve
 * </code>
 *
 * @package Cinnebar
 * @subpackage Command
 * @version $Id$
 */
class Command_Welcome extends Cinnebar_Command
{
    /**
     * Execute the command.
     *
     * The command tries to configure itself from the command line parameters and
     * the runs or displays an error message.
     */
    public function execute()
    {
        if ($this->flag('h')) return $this->help();
        if ( ! $this->flag('n')) return $this->error('Missing parameter -n');
        if ($this->flag('n')) return $this->result();
        return true;
    }

    /**
     * Displays the result page.
     *
     */
    protected function result()
    {
        $view = $this->makeView('command/welcome');
        $this->age = $this->input(sprintf('How old are you, %s? ', $this->flag('n')));
        $view->name = $this->flag('n');
        $view->age = $this->age;
        echo $view->render();
    }
    
    /**
     * Displays the help page.
     *
     */
    protected function help()
    {
        echo "\n", __('help_not_available'), "\n";
        return;
    }
}
