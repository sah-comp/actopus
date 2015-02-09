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
