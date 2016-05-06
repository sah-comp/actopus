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
 * Update command.
 *
 * Usage examle from the command line for updating to revision 17
 * <code>
 * php -f index.php -- -c update -rev 17
 * </code>
 *
 * @package Cinnebar
 * @subpackage Command
 * @version $Id$
 */
class Command_Update extends Cinnebar_Command
{
    /**
     * Dictionary with update methods.
     *
     * @var array
     */
    public $updates = array(
        1 => array('updUnstashOriginal'),
        2 => array('updExportCSV'),
        3 => array('updCardExtended'),
        4 => array('updPaymentstyle'),
        5 => array('updRevision24'),
        6 => array('updRevision25'),
        7 => array('updCardCleanup'),
        8 => array('updCardRegex'),
        9 => array('updCardTeam'),
        10 => array('updDummy'),
        11 => array('UpdCardTeamAndStatus')
    );

    /**
     * Execute the command.
     *
     * The command tries to configure itself from the command line parameters and
     * the runs or displays an error message.
     */
    public function execute()
    {
        if ($this->flag('h')) return $this->help();
        if ( ! $this->flag('rev')) return $this->error('Missing parameter -rev');
        if ($this->flag('rev')) return $this->result();
        return true;
    }

    /**
     * Displays the result page.
     *
     */
    protected function result()
    {
        $view = $this->makeView('command/update/index');
        $view->revision = $this->flag('rev');
        $this->doUpdates($this->flag('rev'));
        echo $view->render();
    }
    
    /**
     * Call all methods that are bound to the requested revision.
     *
     * @param int $revision
     */
    protected function doUpdates($rev)
    {
        if ( ! isset($this->updates[$rev])) return false;
        $updates = $this->updates[$rev];
        foreach ($updates as $update) {
            $this->$update();
        }
    }

    /**
     * Update 3
     *
     * Adds the token card_label_applicationnumber, layout_extended
     */
    protected function updCardExtended()
    {
        $token = R::dispense('token');
        $token->createOrUpdate('card_label_applicationnumber', array(
            0 => array('iso' => 'de', 'payload' => 'Anmeldezeichen'),
            1 => array('iso' => 'en', 'payload' => 'Applicationcode')
        ));
        $token = R::dispense('token');
        $token->createOrUpdate('layout_extended', array(
            0 => array('iso' => 'de', 'payload' => 'Erweitert'),
            1 => array('iso' => 'en', 'payload' => 'Extended')
        ));
        $token->createOrUpdate('ci_phone_fax', array(
            0 => array('iso' => 'de', 'payload' => 'Fax'),
            1 => array('iso' => 'en', 'payload' => 'Fax')
        ));
        $token->createOrUpdate('login_welcome_user', array(
            0 => array('iso' => 'de', 'payload' => 'Willkommen'),
            1 => array('iso' => 'en', 'payload' => 'Welcome')
        ));
        $token->createOrUpdate('gsearch_placeholder_q', array(
            0 => array('iso' => 'de', 'payload' => 'Stichwort finden'),
            1 => array('iso' => 'en', 'payload' => 'Find Keyword')
        ));
    }
    
    /**
     * Update 4
     *
     * Adds the token paymentstyle_label_name, paymentstyle_label_code
     */
    protected function updPaymentstyle()
    {
        $token = R::dispense('token');
        $token->createOrUpdate('domain_paymentstyle', array(
            0 => array('iso' => 'de', 'payload' => 'Zahlarten'),
            1 => array('iso' => 'en', 'payload' => 'Paymenttypes')
        ));
        $token = R::dispense('token');
        $token->createOrUpdate('paymentstyle_head_title', array(
            0 => array('iso' => 'de', 'payload' => 'Zahlarten'),
            1 => array('iso' => 'en', 'payload' => 'Paymenttypes')
        ));
        $token = R::dispense('token');
        $token->createOrUpdate('paymentstyle_label_name', array(
            0 => array('iso' => 'de', 'payload' => 'Name'),
            1 => array('iso' => 'en', 'payload' => 'Name')
        ));
        $token = R::dispense('token');
        $token->createOrUpdate('paymentstyle_label_code', array(
            0 => array('iso' => 'de', 'payload' => 'Code'),
            1 => array('iso' => 'en', 'payload' => 'Code')
        ));
    }
    
    /**
     * Update 5
     *
     * Adds some tokens for revision 24.
     */
    protected function updRevision24()
    {
        $token = R::dispense('token');
        $token->createOrUpdate('person_tab_card', array(
            0 => array('iso' => 'de', 'payload' => 'Akten'),
            1 => array('iso' => 'en', 'payload' => 'Cards')
        ));
        $token->createOrUpdate('person_legend_card', array(
            0 => array('iso' => 'de', 'payload' => ''),
            1 => array('iso' => 'en', 'payload' => '')
        ));
        $token->createOrUpdate('person_tab_invoice', array(
            0 => array('iso' => 'de', 'payload' => 'Rechnungen'),
            1 => array('iso' => 'en', 'payload' => 'Invoices')
        ));
        $token->createOrUpdate('person_legend_invoice', array(
            0 => array('iso' => 'de', 'payload' => ''),
            1 => array('iso' => 'en', 'payload' => '')
        ));
        $token->createOrUpdate('card_tab_invoice', array(
            0 => array('iso' => 'de', 'payload' => 'Rechnungen'),
            1 => array('iso' => 'en', 'payload' => 'Invoices')
        ));
        $token->createOrUpdate('card_legend_invoice', array(
            0 => array('iso' => 'de', 'payload' => ''),
            1 => array('iso' => 'en', 'payload' => '')
        ));
        $token->createOrUpdate('invoice_label_user_id', array(
            0 => array('iso' => 'de', 'payload' => 'Angelegt von'),
            1 => array('iso' => 'en', 'payload' => 'Created by')
        ));
    }
    
    /**
     * Update 6
     *
     * Adds some tokens for revision 25.
     */
    protected function updRevision25()
    {
        $token = R::dispense('token');
        $token->createOrUpdate('card_label_rerule', array(
            0 => array('iso' => 'de', 'payload' => ''),
            1 => array('iso' => 'en', 'payload' => '')
        ));
        $token->createOrUpdate('card_rerule', array(
            0 => array('iso' => 'de', 'payload' => 'Neu laden'),
            1 => array('iso' => 'en', 'payload' => 'Reload')
        ));
        $token->createOrUpdate('card_rerule_hint', array(
            0 => array('iso' => 'de', 'payload' => 'Die Gebührenliste wird neu erstellt. Bereits gespeicherte Gebühren werden zuvor entfernt'),
            1 => array('iso' => 'en', 'payload' => 'Update fees. This will override existing fees')
        ));
        $token->createOrUpdate('card_label_customeraccount', array(
            0 => array('iso' => 'de', 'payload' => 'Konto'),
            1 => array('iso' => 'en', 'payload' => 'Account')
        ));
    }
    
    /**
     * Update card cleanup, inv creation
     *
     */
    protected function updCardCleanup()
    {
        $sql = 'ALTER TABLE card DROP COLUMN `original`';
        R::exec($sql);
        $sql = 'ALTER TABLE card DROP COLUMN `invreceiver`';
        R::exec($sql);
        $sql = 'ALTER TABLE card DROP COLUMN `client`';
        R::exec($sql);
        $sql = 'ALTER TABLE card DROP COLUMN `applicant`';
        R::exec($sql);
        $sql = 'ALTER TABLE card DROP COLUMN `foreign`';
        R::exec($sql);
    }
    
    /**
     * Update 2
     *
     * Adds the token scaffold_csv
     */
    protected function updExportCSV()
    {
        // add a token for scaffold_csv
        if ( ! $scaffold_csv = R::findOne('token', ' name = ? LIMIT 1', array('scaffold_csv'))) {
            $scaffold_csv = R::dispense('token');
            $scaffold_csv->name = 'scaffold_csv';
        }
        $scaffold_csv_trans = R::dispense('translation', 2);
        $scaffold_csv_trans[0]->iso = 'de';
        $scaffold_csv_trans[0]->payload = 'CSV';
        $scaffold_csv_trans[1]->iso = 'en';
        $scaffold_csv_trans[1]->payload = 'CSV';
        $scaffold_csv->ownTranslation = $scaffold_csv_trans;
        try {
            R::store($scaffold_csv);
        } catch (Exception $e) {
            echo $e;
        }
    }
    
    /**
     * Update 1
     *
     * Loops through all card beans and looks up the original number from stash
     * and adds a original (alias card) bean to the card.
     */
    protected function updUnstashOriginal()
    {
        $counter = 0;
        $total = 0;
        $offset = 0;
        $limit = 500;
        echo "Transform migrated original number to a bean:\n";
        //R::debug(true);
        while ($cards = R::findAll('card', ' ORDER BY id LIMIT '.$limit.' OFFSET '.$offset)) {
            foreach ($cards as $id => $card) {
                $total++;
                $stash = $card->stash();
                if ( ! $stash->originalnumber) continue; //skip when nothing or not found
                if ( ! $original = R::findOne('card', ' name = ? LIMIT 1', array($stash->originalnumber))) continue;
                if ( $original->originalnumber == $card->name) continue; //original is card itself
                try {
                    $card->setAutoInfo(false);
                    $card->setAutoTag(false);
                    $original->setAutoInfo(false);
                    $original->setAutoTag(false);
                    $card->original = $original;
                    R::store($card);
                    $counter++;
                    echo "$card->name now has original {$original->name}\n";
                    unset($stash);
                    unset($original);
                }
                catch (Exception $e) {
                    error_log($e);
                }
            }
            echo $counter." transformed having read ".$total."\n";
            $offset = $offset + $limit;    
        }
    }
    
    /**
     * Update card regex.
     *
     * This will update card table and add application-, issue- and disclosurenumberflat fields.
     *
     */
    protected function updCardRegex()
    {
        //R::freeze(false);
        R::begin();
        try {
            $cards = R::findAll('card');
            foreach ($cards as $id => $card) {
                $card->revision = 9;
            }
            R::storeAll($cards);
            R::commit();
        } catch ( Exception $e ) {
            echo $e;
            R::rollback();
        }
        //R::freeze(true);
    }
    
    /**
     * Update card team.
     *
     * This will update card beans, population the team relation according to the selected attorney.
     *
     */
    protected function updCardTeam()
    {
        echo 'Please update to revision 11 --rev 11 to update team relations on cards';
    }
    
    /**
     * Dummy update loop.
     *
     * Demonstrates a loop through all records in packages of n.
     * Copy this, rename, specify a certain criteria for beans to update
     * and hook it up into the command list to use it from the shell to
     * update many records without running into memory limits.
     *
     */
    protected function updDummy()
    {
        $cards = R::find('card', ' status IS NULL LIMIT 10 ');
        if ( empty($cards) ) {
            echo 'Ready';
            exit;
        }
        $i = 0;
        R::begin();
        try {
            echo 'Dummy started...'."\n";
            foreach ($cards as $id => $card) {
                $i++;
                echo $i . ' ' . $card->name ."\n";
                $card->validationMode(Cinnebar_Model::VALIDATION_MODE_IMPLICIT);
                $card->dummy = 'init';
            }
            R::storeAll($cards);
            R::commit();
            echo 'Success'."\n";
        } catch ( Exception $e ) {
            echo $e;
            R::rollback();
            echo 'Fail.'."\n";
            exit;
        }
        $this->updDummy();
    }
    
    /**
     * UpdCardTeamAndStatus
    */
    protected function UpdCardTeamAndStatus()
    {
        $cards = R::find('card', ' status IS NULL LIMIT 100 ');
        if ( empty($cards) ) {
            echo 'Ready';
            exit;
        }
        $i = 0;
        R::begin();
        try {
            echo 'UpdCardTeamAndStatus started...'."\n";
            foreach ($cards as $id => $card) {
                $i++;
                echo $i . ' ' . $card->name ."\n";
                $card->validationMode(Cinnebar_Model::VALIDATION_MODE_IMPLICIT);
                $card->status = 'maintain';
            }
            R::storeAll($cards);
            R::commit();
            echo 'Success'."\n";
        } catch ( Exception $e ) {
            echo $e;
            R::rollback();
            echo 'Fail.'."\n";
            exit;
        }
        unset($cards);
        $this->UpdCardTeamAndStatus();
    }
    
    /**
     * Displays the help page.
     *
     */
    protected function help()
    {
        $view = $this->makeView('command/update/help');
        echo $view->render();
        return;
    }
}
