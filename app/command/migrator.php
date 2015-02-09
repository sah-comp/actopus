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
 * The migrator command ports a legacy database to a cinnebar application.
 *
 * Usage examle from the command line
 * <code>
 * php -f index.php -- -c migrator [--id 23]
 * </code>
 *
 * @package Cinnebar
 * @subpackage Command
 * @version $Id$
 */
class Command_Migrator extends Cinnebar_Command
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
        //if ( ! $this->flag('n')) return $this->error('Missing parameter -n');
        //if ($this->flag('n')) return $this->result();
        //return true;
        return $this->index($this->flag('id'));
    }

    /**
     * Loads a existing migrator or creates a new one, ask for confirmation and if given
     * runs the choosen migration tool.
     *
     * @uses makeView()
     * @uses workhorse_migrate() in case a migrator could be generated
     * @param int (optional) $id of the migrator to use again
     * @return mixed
     */
    protected function index($id = 0)
    {
        $migrator = R::load('migrator', $id);
        
        $view = $this->makeView('command/migrator/index');
        echo $view->render();
        
        // if there was no mig...
        if ( ! $migrator->getId()) {
            // ... ask user on cli and add one
            if ( ! $migrator = $this->add()) {
                echo __('migrator_error_invalid')."\n\n";
                return false;
            }
        } else {
            echo $migrator."\n\n";
        }

        // confirmation
        $confirm = $this->input(__('migrator_confirm').'? ');
        if ( $confirm === 'y') {
            return $this->workhorse_migrate($migrator);
        }
    }
    
    /**
     * Asks for input on the cli and tries to create a new migrator from that input.
     *
     * @return mixed $eitherRedBean_OODBBeanOrFalse
     */
    protected function add()
    {
        $questions = array(
            // the legacy server
            'legacy_host',
            'legacy_db',
            'legacy_user',
            'legacy_pw',
            // our new server
            'heir_host',
            'heir_db',
            'heir_user',
            'heir_pw',
            // which migrator to use?
            'token'
        );
        $answers = array();
        foreach ($questions as $question) {
            $answers[$question] = $this->input(__('migrator_'.$question).'? ');
        }
        try {
            $migrator = R::dispense('migrator');
            $migrator->import($answers);
            R::store($migrator);
            return $migrator;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * This will instanciate our migrator and run it.
     *
     * @param RedBean_OODBBean $migrator an instance of a migrator bean
     */
    protected function workhorse_migrate(RedBean_OODBBean $migrator)
    {
        $mig_name = 'Migrator_'.ucfirst(strtolower($migrator->token));
        $mig = new $mig_name($migrator);
        return $mig->migrate();
    }
    
    /**
     * Displays the help page.
     *
     * @uses makeView()
     */
    protected function help()
    {
        $view = $this->makeView('command/migrator/help');
        echo $view->render();
    }
}