<?php
/**
 * Cinnebar.
 *
 * My lightweight no-framework framework written in PHP.
 *
 * @package Shoebox
 * @author $Author$
 * @version $Id$
 */

/**
 * The gvr migrator converts the pre 2012 gvr system to the new 2013 s based system.
 *
 * @todo DRY
 * @todo Implement migration protocol and validation mode setting
 *
 * @package Shoebox
 * @subpackage Migrator
 * @version $Id$
 */
class Migrator_Gvr extends Cinnebar_Migrator
{
    /**
     * Holds the prefix of legacy tables or an empty string if no prefix is needed.
     *
     * @var string
     */
    protected $prefix = 'untitled_';

    /**
     * Container for counters.
     *
     * @var array
     */
    protected $counters = array(
        'card' => 0,
        'bill' => 0,
        'annual' => 0,
        'user' => 0
    );
    
    /**
     * Holds the bean that is the "fee" invoice method.
     *
     * @var RedBean_OODBBean
     */
    public $heir_invoice_method_fee;
    
    /**
     * Holds the bean that is the normal invoice method.
     *
     * @var RedBean_OODBBean
     */
    public $heir_invoice_method_invoice;
    
    /**
     * Holds the number of unkown legacy cards.
     *
     * @var int
     */
    public $ufo_card_counter = 0;
    
    /**
     * Holds the number of unkown contacts.
     *
     * @var int
     */
    public $ufo_contact_counter = 0;
    
    /**
     * Container for unknow legacy records.
     *
     * @var array
     */
    protected $unknown = array(
        'user' => array(
            'email' => 'admin@vonrohr.de',
            'shortname' => 'admin',
            'pw' => 'admin',
            'is_admin' => false,
            'startpage' => 'home',
            'is_deleted' => true,
            'team' => 0,
            'role' => 0
        ),
        'team' => array(
            'value' => 'migration',
            'printable_name' => 'migration'
        ),
        'role' => array(
            'title' => 'migrator'
        ),
        'cardtype' => array(
            'printable_name' => 'undefined'
        ),
        'annualtype' => array(
            'printable_name' => 'undefined'
        ),
        'annualfeetype' => array(
            'printable_name' => 'undefined'
        ),
        'annualstatus' => array(
            'printable_name' => 'undefined'
        ),
        'country' => array(
            'iso' => 'xx',
            'printable_name' => 'undefined'
        ),
        'language' => array(
            'code' => 'xx',
            'printable_name' => 'undefined'
        ),
        'contact' => array(
            'nickname' => 'Migration %d',
            'company' => '',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'note' => '',
            'attention' => '',
            'account' => '',
            'tax_id' => '',
            'title' => '',
            'postal1' => '',
            'postal2' => '',
            'language' => 1
        ),
        'shelf' => array(
            'value' => 'undefined',
            'printable_name' => 'undefined'
        ),
        'card' => array(
            'number' => 'Migration %d'
        )
    );

    /**
     * Prepares the heir for taking his legacy.
     *
     * @uses $heir_invoice_method_invoice to stash the invoicemethod for invoice(s)
     * @uses $heir_invoice_method_fee to stash the invoicemethod for fee(s)
     */
    protected function prepare()
    {
        // clean up the heir database
        $this->useHeirDB();
        R::exec('SET FOREIGN_KEY_CHECKS=0');
        
        $tables = array(
            'address',
            'country',
            'country_tag',
            'email',
            'fee',
            'fee_info',
            'feestep',
            'feetype',
            'info_invoice',
            'info_invoicetype',
            'info_person',
            'info_rule',
            'invoice',
            'invoicetype',
            'language',
            'card',
            'card_tag',
            'card_info',
            'cardfeestep',
            'cardstatus',
            'cardtype',
            'person',
            'person_tag',
            'phone',
            'priority',
            'pricetype',
            'rule',
            'rulestep',
            'team',
            'team_user',
            'url',
            'user',
            'useri18n',
            'info_user',
            'country_tag',
            'language_tag',
            'person_tag',
            'priority_tag',
            'tag'
        );
        foreach ($tables as $table) {
            R::exec('DROP TABLE `'.$table.'`');
        }
        R::exec('SET FOREIGN_KEY_CHECKS=1');
        
        echo 'Prepared new database'."\n";
    }
    
    /**
     * Clean up after migration.
     */
    protected function cleanup()
    {
        /*
        $this->useHeirDB();
        $sqlcommands = array(
            'ALTER TABLE `user` ADD INDEX `idx_email` (`email`)',
            'ALTER TABLE `user` ADD INDEX `idx_name` (`name`)',
            'ALTER TABLE `user` ADD INDEX `idx_admin` (`admin`)',
            'ALTER TABLE `user` ADD INDEX `idx_ego` (`ego`)',
            'ALTER TABLE `user` ADD INDEX `idx_banned` (`banned`)',
            'ALTER TABLE `user` ADD INDEX `idx_deleted` (`deleted`)',
            'ALTER TABLE `team` ADD INDEX `idx_name` (`name`)',
            'ALTER TABLE `team` ADD INDEX `idx_sequence` (`sequence`)',
            'ALTER TABLE `role` ADD INDEX `idx_name` (`name`)',
            'ALTER TABLE `role` ADD INDEX `idx_sequence` (`sequence`)',
        );
        foreach ($sqlcommands as $sql) {
            R::exec($sql);
        }
        */
    }

    /**
     * Migrate basic claims of legacy to heir.
     */
    protected function basic_claims()
    {
        // initialize some tables and records
        list($im1, $im2) = R::dispense('invoicetype', 2);
        $im1->name = 'Rechnung';
        $im1->serial = 10000;
        $im2->name = 'Gebührenrechnung';
        $im2->serial = 16000;

        try {
            R::store($im1);
            R::store($im2);
            $this->heir_invoice_method_invoice = $im1;
            $this->heir_invoice_method_fee = $im2;
        } catch (Exception $e) {
            echo 'Created invoice methods '.$e."\n";
        }
    }

    /**
     * Migrate basic claims of legacy to heir.
     *
     * This will migrate:
     * - card to paper
     * - bill to invoice
     * - annual to annual
     * - contact/person conversion happens on the fly
     */
    protected function dynamic_claims()
    {
        $this->dynamic_claims_users();
        $this->dynamic_claims_cards();
        $this->dynamic_claims_bills();
        $this->dynamic_claims_annuals();
    }

    /**
     * Migrate gvruser of legacy to heir paper.
     *
     * This will migrate:
     * - gvruser to user
     */
    protected function dynamic_claims_users()
    {
        $offset = 0;
        $limit = 500;
        while ($users = $this->load_legacy_users($offset, $limit)) {
            foreach ($users as $id => $user) {
                $this->convert_user_to_user($user);
            }
            echo 'Converted '.$this->counters['user']." users\n";
            $offset = $offset + $limit;    
        }
    }

    /**
     * Migrate gvrcard of legacy to heir paper.
     *
     * This will migrate:
     * - card to paper
     */
    protected function dynamic_claims_cards()
    {
        $offset = 0;
        $limit = 500;
        while ($cards = $this->load_legacy_cards($offset, $limit)) {
            foreach ($cards as $id => $card) {
                $this->convert_card_to_paper($card);
            }
            echo 'Converted '.$this->counters['card']." cards\n";
            $offset = $offset + $limit;    
        }
    }

    /**
     * Migrate gvrbill of legacy to heir invoice.
     *
     * This will migrate:
     * - bill to invoice
     */
    protected function dynamic_claims_bills()
    {
        $offset = 0;
        $limit = 500;
        while ($bills = $this->load_legacy_bills($offset, $limit)) {
            foreach ($bills as $id => $bill) {
                $this->convert_bill_to_invoice($bill);
            }
            echo 'Converted '.$this->counters['bill']." bills\n";
            $offset = $offset + $limit;    
        }
    }

    /**
     * Migrate gvrannual of legacy to heir annual.
     *
     * This will migrate:
     * - annual to annual
     */
    protected function dynamic_claims_annuals()
    {
        $offset = 0;
        $limit = 500;
        while ($annuals = $this->load_legacy_annuals($offset, $limit)) {
            foreach ($annuals as $id => $annual) {
                $this->convert_annual_to_annual($annual);
            }
            echo 'Converted '.$this->counters['annual']." annual\n";
            $offset = $offset + $limit;    
        }
    }

    /**
     * Converts a card to a Cinnebar Paper.
     *
     * @uses $counters['card'] to count the successfully converted card/paper pairs
     * @param array $record
     */
    protected function convert_card_to_paper(array $record)
    {
        $paper = R::dispense('card');
        $paper->validationMode(Cinnebar_Model::VALIDATION_MODE_IMPLICIT);

        $paper->name = $record['number'];
        $paper->title = $record['title'];
        $paper->codeword = $record['codeword'];
        $paper->note = $record['note'];

        
        unset($paper->user);
        if ($user = $this->make_heir_user($this->load_legacy_user($record['manager_id']))) {
            $paper->user = $user;
        }
        $paper->cardtype = $this->make_heir_papertype($this->load_legacy_cardtype($record['type_id']));
        $paper->cardstatus = $this->make_heir_paperstatus($this->load_legacy_shelf($record['shelf_id']));
        $paper->country = $this->make_heir_country($this->load_legacy_country($record['country_id']));


        // client
        //$person = R::dispense('person');
        unset($paper->client);
        if ($client = $this->make_heir_person($this->load_legacy_contact($record['client_id']))) {
            $paper->clientcode = $record['client_reference'];
            $paper->clientnickname = $client->nickname;
            $paper->clientaddress = $client->addressLabel($legacy = true);
            $paper->client = $client;
            $paper->client_id = $client->getId();
        }

        unset($paper->invreceiver);
        $paper->invreceiver_id = null;
        if ($record['billing_id'] && $record['billing_id'] != $record['client_id']) {
            //$paper->invreceiver_id = $record['billing_id'];

            // billto
            //$billto = R::dispense('person');
            if ($billto = $this->make_heir_person($this->load_legacy_contact($record['billing_id']))) {
                $paper->invreceivercode = $record['billing_reference'];
                $paper->invreceivernickname = $billto->nickname;
                $paper->invreceiveraddress = $billto->addressLabel($legacy = true);
                $paper->invreceiver = $billto;
                $paper->invreceiver_id = $billto->getId();    
            }
            

        }

        $paper->patterncount = $record['classes_count'];
        $paper->pattern = $record['classes'];

        $stash = R::dispense('stash');
        $stash->family_tree = $record['family_tree'];
        $stash->classes = $record['classes'];
        $stash->originalnumber = $record['original_number'];
        $stash->wocountries = $record['wo_countries'];
        $stash->wocountriescount = $record['wo_countries_count'];
        $stash->notemanually = $record['note_manually'];
        $stash->status = $record['status'];

        $paper->stash = $stash; // unbearable field salad cucumber

        // proceeding
        //$proceeding = R::dispense('proceeding');    
        $pro_states = array(
            'application',
            'issue',
            'disclosure'
        );
        foreach ($pro_states as $state) {
            // application, issue or disclosure?
            $fn_date = $state.'_date';
            $fn_number = $state.'_number';
            // preset
            $proceeding->$fn_date = null;
            $proceeding->$fn_number = null;
            // reset
            //if (($record[$fn_date] != '0000-00-00' && $record[$fn_date]) != null || $record[$fn_number]) {
                //$proceeding->$fn_has = true;
                $paper->{$state.'date'} = $record[$fn_date];
                $paper->{$state.'number'} = $record[$fn_number];
            //}
        }

        $paper->applicant_id = null;
        unset($paper->applicant);
        if ($record['applicant_id'] && $record['applicant_id'] != $record['client_id']) {
            //$proceeding->has_applicant = true;

            // applicant
            if ($applicant = $this->make_heir_person($this->load_legacy_contact($record['applicant_id']))) {
                $paper->applicantcode = $record['applicant_reference'];
                $paper->applicantnickname = $applicant->nickname;
                $paper->applicantaddress = $applicant->addressLabel($legacy = true);
                $paper->applicant = $applicant;
                $paper->applicant_id = $applicant->getId();
            }
        }

        unset($paper->foreign);
        $paper->foreign_id = null;
        if ($record['foreign_id'] && $record['foreign_id'] != $record['client_id']) {

            // foreign
            if ($foreign = $this->make_heir_person($this->load_legacy_contact($record['foreign_id']))) {
                $paper->foreigncode = $record['foreign_reference'];
                $paper->foreignnickname = $foreign->nickname;
                $paper->foreignaddress = $foreign->addressLabel($legacy = true);
                $paper->foreign = $foreign;
                $paper->foreign_id = $foreign->getId();
            }
        }

        //$paper->ownProceeding = array($proceeding);

        // optional priorities
        $priors = array(
            '1', '2', '3'
        );
        foreach ($priors as $prior) {
            $fn_country_name = 'printable_name';
            $fn_country = 'prior_country_'.$prior;
            $fn_date = 'prior_date_'.$prior;
            $fn_number = 'prior_number_'.$prior;
            if ($record[$fn_country] > 0 && ($record[$fn_date] != '0000-00-00' || $record[$fn_number])) {
                $prior = R::dispense('priority');
                $prior->date = $record[$fn_date];
                unset($prior->country);
                $country = $this->make_heir_country($this->load_legacy_country($record[$fn_country]));
                $prior->country = $country;
                $prior->country_id = $country->getId();
                $prior->number = $record[$fn_number];
                $paper->ownPriority[] = $prior;
            }
        }
        
        


        try {
            $this->useHeirDB();
            R::store($paper);
            $this->counters['card']++;
            if ($paper->invalid) {
                $message = 'Card "'.$record['number'].'" was migrated, but is invalid';
                Cinnebar_Logger::instance()->log($message, $this->logname.'_card');
            }
            return true;
        } catch (Exception $e) {
            $message = 'Card "'.$record['number'].'" says: '.$e->getMessage();
            Cinnebar_Logger::instance()->log($message, $this->logname.'_card');
            //echo $message."\n";
            return false;
        }
    }

    /**
     * Converts a bill to a Cinnebar Invoice.
     *
     * @uses $counters['bill'] to count the successfully converted bill/invoice pairs
     * @param array $record
     */
    protected function convert_bill_to_invoice(array $record)
    {
        $invoice = R::dispense('invoice');
        $invoice->validationMode(Cinnebar_Model::VALIDATION_MODE_IMPLICIT);

        $invoice->name = $record['number'];
        $invoice->invoicedate = $record['invoice_date'];
        $invoice->bookingdate = date('Y-m-d', strtotime($record['ctime']));

        unset($invoice->card);
        if ($card = $this->make_heir_paper($this->load_legacy_card($record['gvrcard_id']))) {
            $invoice->card = $card;
            $invoice->card_id = $card->getId();
            $invoice->cardname = $invoice->card->name;    
        }
        

        //$invoice->subject = trim($invoice->paper->number . ' ' . $invoice->paper->title);
        $invoice->deleted = false;
        if (strtolower($record['cancelation']) == 'y') $invoice->deleted = true;

        if ($record['type_id'] == 2) {
            // this is a "gebührenrechnung"
            $invoice->invoicetype = $this->heir_invoice_method_fee;
            $invoice->invoicetype_id = $this->heir_invoice_method_fee->getId();
        } else {
            $invoice->invoicetype = $this->heir_invoice_method_invoice;
            $invoice->invoicetype_id = $this->heir_invoice_method_invoice->getId();
        }

        unset($invoice->client);
        $invoice->client_id = null;
        if ($client = $this->make_heir_person($this->load_legacy_contact($record['client_id']))) {
            $invoice->clientnickname = $client->nickname;
            $invoice->clientcode = '';//$client->nickname;
            $invoice->clientaddress = $client->addressLabel($legacy = true);
            $invoice->client = $client;
            $invoice->client_id = $client->getId();
            
        }
        
        try {
            $this->useHeirDB();
            R::store($invoice);
            $this->counters['bill']++;
            return true;
        } catch (Exception $e) {
            $message = 'Invoice "'.$record['number'].'" says: '.$e->getMessage();
            Cinnebar_Logger::instance()->log($message, $this->logname.'_invoice');
            //echo $message."\n";
            return false;
        }
    }

    /**
     * Converts a untitled-(user) to a Cinnebar User.
     *
     * @uses $counters['users'] to count the successfully converted user/user pairs
     * @param array $record
     */
    protected function convert_user_to_user(array $record)
    {
        $user = R::dispense('user');
        //$user->validationMode(Cinnebar_Model::VALIDATION_MODE_IMPLICIT);

        $user->email = $record['email'];

        if (empty($user->email) || trim($user->email) == '') {
            $user->email = $this->make_email_name($record['shortname']);
        }

        $user->name = $record['shortname'];
        $user->pw = $record['shortname'];
        $user->admin = $record['is_admin'];
        $user->home = '/'.$record['startpage'];
        $user->deleted = $record['is_deleted'];

        $user->sharedTeam[] = $this->make_heir_team($this->load_legacy_team($record['team']));
        $user->sharedRole[] = $this->make_heir_role($this->load_legacy_role($record['role']));

        try {
            $this->useHeirDB();
            R::store($user);
            $this->counters['user']++;
            return true;
        } catch (Exception $e) {
            $message = 'User "'.$record['shortname'].'" says: '.$e->getMessage();
            Cinnebar_Logger::instance()->log($message, $this->logname.'_user');
            //echo $message."\n";
            return false;
        }
    }

    /**
     * Generates an email name part from a string.
     *
     * @param string $name
     * @return string
     */
    public function make_email_name($name, $extender = '@vonrohr.de')
    {
        if ( ! $name) return uniqid();
        $rep = array('ä', 'ü', 'ö', 'ß', 'Ä', 'Ü', 'Ö');
        $tar = array('ae', 'ue', 'oe', 'ss', 'ae', 'ue', 'oe');
        $name = str_replace($rep, $tar, $name);
        return strtolower($name).$extender;
    }

    /**
     * Converts a annual (legacy) to a Cinnebar Annual (heir).
     *
     * @uses $counters['annual'] to count the successfully converted annual/annual pairs
     * @param array $record
     */
    protected function convert_annual_to_annual(array $record)
    {
        $paper = $this->make_heir_paper($this->load_legacy_card($record['gvrcard_id']));
        if ( ! $paper) return;
        
        $paper->validationMode(Cinnebar_Model::VALIDATION_MODE_IMPLICIT);
        
        $pricetype = $this->make_heir_feetype($this->load_legacy_feetype($record['fee_type_id']));
        
        
        $feetype = $this->make_heir_annualmethod($this->load_legacy_annualtype($record['type_id']));
        $annualstatus = $this->load_legacy_annualstatus($record['status']);
        
        unset($paper->feetype);
        if ($feetype) {
            $paper->feetype = $feetype;
            $paper->feetype_id = $feetype->getId();
        }
        
        unset($paper->pricetype);
        if ($pricetype) {
            $paper->pricetype = $pricetype;
            $paper->pricetype_id = $pricetype->getId();
        }
        
        $paper->onhold = false;
        if (isset($annualstatus['value']) && $annualstatus['value'] == 'temporarily_inactive') {
            $paper->onhold = true;
        }
        $paper->feeinactive = true;
        if (isset($annualstatus['value']) && $annualstatus['value'] == 'active') {
            $paper->feeinactive = false;
        }

        $paper->feeduedate = $record['due_date'];
        $paper->feeorderneeded = $record['pay_on_order'];
        $paper->feesubject = $record['note'];
        $paper->revenueaccount = $record['billing_revenueaccount'];

        // here come the hot stepper
        $fees = json_decode($record['fees'], true);
        if (is_array($fees)) {
            $paper->ownCardfeestep = $this->make_heir_annualsteps($fees);
        }

        try {
            $this->useHeirDB();
            R::store($paper);
            $this->counters['annual']++;
            return true;
        } catch (Exception $e) {
            $message = 'Card/Annual "'.$record['card_number'].'" says: '.$e->getMessage();
            Cinnebar_Logger::instance()->log($message, $this->logname.'_annual');
            //echo $message."\n";
            return false;
        }
    }

    /**
     * load a legacy user record by its id.
     *
     * @param int $id
     * @return array $user
     */
    protected function load_legacy_user($id)
    {
        $row = $this->load_legacy('user', $id);
        if ( ! $row) {
            return $this->unknown['user'];
        }
        return $row;
    }

    /**
     * load a legacy team record by its id.
     *
     * @param int $id
     * @return array $team
     */
    protected function load_legacy_team($id)
    {
        $row = $this->load_legacy('selopts', $id);
        if ( ! $row) return $this->unknown['team'];
        return $row;
    }

    /**
     * load a legacy annual type (Führungsart) record by its id.
     *
     * @param int $id
     * @return array $team
     */
    protected function load_legacy_annualtype($id)
    {
        $row = $this->load_legacy('selopts', $id);
        if ( ! $row) return $this->unknown['annualtype'];
        return $row;
    }

    /**
     * load a legacy annual status (GT Status) record by its id.
     *
     * @param int $id
     * @return array $annualstatus
     */
    protected function load_legacy_annualstatus($id)
    {
        $row = $this->load_legacy('selopts', $id);
        if ( ! $row) return $this->unknown['annualstatus'];
        return $row;
    }

    /**
     * load a legacy annual fee type (Honorarlist) record by its id.
     *
     * @param int $id
     * @return array $team
     */
    protected function load_legacy_feetype($id)
    {
        $row = $this->load_legacy('selopts', $id);
        if ( ! $row) return $this->unknown['annualfeetype'];
        return $row;
    }

    /**
     * load a legacy role record by its id.
     *
     * @param int $id
     * @return array $role
     */
    protected function load_legacy_role($id)
    {
        $row = $this->load_legacy('role', $id);
        if ( ! $row) return $this->unknown['role'];
        return $row;
    }

    /**
     * load a legacy cardtype record by its id.
     *
     * @param int $id
     * @return array $cardtype
     */
    protected function load_legacy_cardtype($id)
    {
        $row = $this->load_legacy('cardtype', $id);
        if ( ! $row) return $this->unknown['cardtype'];
        return $row;
    }

    /**
     * load a legacy shelf (status) record by its id.
     *
     * @param int $id
     * @return array $shelf
     */
    protected function load_legacy_shelf($id)
    {
        $row = $this->load_legacy('selopts', $id);
        if ( ! $row) return $this->unknown['shelf'];
        return $row;
    }

    /**
     * load a legacy country record by its id.
     *
     * @param int $id
     * @return array $country
     */
    protected function load_legacy_country($id)
    {
        $row = $this->load_legacy('country', $id);
        if ( ! $row) return $this->unknown['country'];
        return $row;
    }

    /**
     * load a legacy language record by its id.
     *
     * @param int $id
     * @return array $language
     */
    protected function load_legacy_language($id)
    {
        $row = $this->load_legacy('language', $id);
        if ( ! $row) return $this->unknown['language'];
        return $row;
    }

    /**
     * load a legacy contact record by its id.
     *
     * @param int $id
     * @return array $contact
     */
    protected function load_legacy_contact($id)
    {
        $row = $this->load_legacy('contact', $id);
        if ( ! $row) {
            // try to build a more unique nickname for an unknown soldier, e.g. jd 8977
            $this->ufo_contact_counter++;
            $this->unknown['contact']['nickname'] = sprintf($this->unknown['contact']['nickname'], $this->ufo_contact_counter);
            return $this->unknown['contact'];
        }
        return $row;
    }

    /**
     * load a legacy card record by its id.
     *
     * @param int $id
     * @return array $card
     */
    protected function load_legacy_card($id)
    {
        $row = $this->load_legacy('gvrcard', $id);
        if ( ! $row) {
            $row = $this->unknown['card'];
            $this->ufo_card_counter++;
            $row['number'] = sprintf($row['number'], $this->ufo_card_counter);
        }
        return $row;
    }

    /**
     * make a heir paper from a legacy card array.
     *
     * @param array $record holds the key/value array with legacy data
     * @return RedBean_OODBBean $paper
     */
    protected function make_heir_paper(array $record)
    {
        $this->useHeirDB();
        if ( $paper = R::findOne('card', ' name = ?', array(mb_strtolower($record['number'])))) {
            return $paper;
        }
        $paper = R::dispense('card');
        $paper->name = mb_strtolower($record['number']);
        //$this->useHeirDB();
        try {
            R::store($paper);
        } catch (Exception $e) {
            $message = 'Card "'.$record['number'].'" says: '.$e->getMessage();
            Cinnebar_Logger::instance()->log($message, $this->logname.'_card');
            //echo $message."\n";
            return null;
        }
        return $paper;
    }

    /**
     * make a heir user from a legacy user array.
     *
     * @param array $record holds the key/value array with legacy data
     * @return RedBean_OODBBean $user
     */
    protected function make_heir_user(array $record)
    {
        $this->useHeirDB();
        if ( $user = R::findOne('user', ' name = ?', array(mb_strtolower($record['shortname'])))) {
            return $user;
        }
        $user = R::dispense('user');
        $user->email = $record['email'];

        if (empty($user->email) || trim($user->email) == '') {
            $user->email = $this->make_email_name($record['shortname']);
        }

        $user->name = mb_strtolower($record['shortname']);
        //$user->nickname = $record['shortname'];
        $user->pw = $record['shortname'];
        $user->admin = $record['is_admin'];
        $user->goto = $record['startpage'];
        $user->deleted = $record['is_deleted'];
        $user->banned = $record['is_deleted'];

        $user->sharedTeam[] = $this->make_heir_team($this->load_legacy_team($record['team']));
        $user->sharedRole[] = $this->make_heir_role($this->load_legacy_role($record['role']));

        try {
            R::store($user);
        } catch (Exception $e) {
            $message = 'User "'.$record['shortname'].'" says: '.$e->getMessage();
            Cinnebar_Logger::instance()->log($message, $this->logname.'_user');
            //echo $message."\n";
            return null;
        }
        return $user;
    }

    /**
     * make a heir team from a legacy selopt (user_team) array.
     *
     * @param array $record holds the key/value array with legacy data
     * @return RedBean_OODBBean $team
     */
    protected function make_heir_team(array $record)
    {
        $this->useHeirDB();
        if ($team = R::findOne('team', ' name = ?', array(mb_strtolower($record['printable_name'])))) {
            return $team;
        }
        $team = R::dispense('team');
        $team->name = $record['printable_name'];
        $team->sequence = rand(1, 1000);
        try {
            R::store($team);
        } catch (Exception $e) {
            $message = 'Team "'.$record['value'].'" says: '.$e->getMessage();
            Cinnebar_Logger::instance()->log($message, $this->logname.'_team');
            //echo $message."\n";
            return null;
        }
        return $team;
    }

    /**
     * make a heir role from a legacy role array.
     *
     * @param array $record holds the key/value array with legacy data
     * @return RedBean_OODBBean $role
     */
    protected function make_heir_role(array $record)
    {
        $this->useHeirDB();
        if ($role = R::findOne('role', ' name = ?', array(mb_strtolower($record['title'])))) {
            return $role;
        }
        $role = R::dispense('role');
        $role->name = $record['title'];
        $role->sequence = rand(1, 1000);
        try {
            R::store($role);
        } catch (Exception $e) {
            $message = 'Role "'.$record['shortname'].'" says: '.$e->getMessage();
            Cinnebar_Logger::instance()->log($message, $this->logname.'_role');
            //echo $message."\n";
            return null;
        }
        return $role;
    }

    /**
     * make a heir papertype from a legacy cardtype array.
     *
     * @param array $record holds the key/value array with legacy data
     * @return RedBean_OODBBean $papertype
     */
    protected function make_heir_papertype(array $record)
    {
        $this->useHeirDB();
        if ($papertype = R::findOne('cardtype', ' name = ?', array(mb_strtolower($record['printable_name'])))) {
            return $papertype;
        }
        $papertype = R::dispense('cardtype');
        //$papertype->token = mb_strtolower($record['printable_name']);
        $papertype->name = $record['printable_name'];
        try {
            R::store($papertype);
        } catch (Exception $e) {
            $message = 'Cardtype "'.$record['printable_name'].'" says: '.$e->getMessage();
            Cinnebar_Logger::instance()->log($message, $this->logname.'_cardtype');
            //echo $message."\n";
            return null;
        }
        return $papertype;
    }

    /**
     * make a heir annualmethod from a legacy annualtype array.
     *
     * @param array $record holds the key/value array with legacy data
     * @return RedBean_OODBBean $annualmethod
     */
    protected function make_heir_annualmethod(array $record)
    {
        $this->useHeirDB();
        if ($annualmethod = R::findOne('feetype', ' name = ?', array(mb_strtolower($record['printable_name'])))) {
            return $annualmethod;
        }
        $annualmethod = R::dispense('feetype');
        //$annualmethod->token = mb_strtolower($record['printable_name']);
        $annualmethod->name = $record['printable_name'];
        try {
            R::store($annualmethod);
        } catch (Exception $e) {
            $message = 'Feetype "'.$record['printable_name'].'" says: '.$e->getMessage();
            Cinnebar_Logger::instance()->log($message, $this->logname.'_feetype');
            //echo $message."\n";
            return null;
        }
        return $annualmethod;
    }

    /**
     * make a heir annualstatus from a legacy annualstatus array.
     *
     * @param array $record holds the key/value array with legacy data
     * @return RedBean_OODBBean $annualstatus
     */
    protected function make_heir_annualstatus(array $record)
    {
        $this->useHeirDB();
        if ($annualstatus = R::findOne('annualstatus', ' token = ?', array(mb_strtolower($record['printable_name'])))) {
            return $annualstatus;
        }
        $annualstatus = R::dispense('annualstatus');
        $annualstatus->token = mb_strtolower($record['printable_name']);
        $annualstatus->printable_name = $record['printable_name'];
        try {
            R::store($annualstatus);
        } catch (Exception $e) {
            $message = 'Annualstatus "'.$record['printable_name'].'" says: '.$e->getMessage();
            Cinnebar_Logger::instance()->log($message, $this->logname.'_feetype');
            //echo $message."\n";
            return null;
        }
        return $annualstatus;
    }

    /**
     * make a heir annualstatus from a legacy annualstatus array.
     *
     * @param array $fees holds arrays of legacy feesteps
     * @return array $arrayOfAnnualsteps
     */
    protected function make_heir_annualsteps(array $fees = array())
    {
        $result = array();
        $types = array(
            'awareness' => 'awareness',
            'order' => 'acceptance',
            'invoice' => 'demand',
            'payment' => 'feepayment'
        );
        $i = 0;
        foreach ($fees as $n=>$fee) {
            $i++;
            $annualstep = R::dispense('cardfeestep');
            $annualstep->fy = $n;
            $annualstep->net = 0.00;
            $annualstep->additional = 0.00;
            $annualstep->sequence = $i;
            $annualstep->done = false;
            if (isset($fee['finished']) && $fee['finished']) {
                $annualstep->done = true;
            }
            foreach ($types as $type=>$new_type) {
                if (isset($fee[$type])) {
                    //$bean_by_type = R::dispense($new_type);
                    $annualstep->{$type.'date'} = $fee[$type]['date'];
                    $annualstep->{$type.'user'} = $this->make_heir_user($this->load_legacy_user($fee[$type]['sign']));
                    /*
                    $date = $fee[$type]['date'];
                    $this->cooked['ownFeestep'][$n]['own'.ucfirst($new_type)][0] = array(
                        'type' => $new_type,
                        'date' => $date,
                        'user' => array(
                            'type' => 'user',
                            'id' => $this->make_user_from_sign($fee[$type]['sign'])
                        )
                    );
                    */
                    if ($type != 'order') {
                        $annualstep->{$type.'net'} = $this->floatvalue($fee[$type]['fee']);
                    }
                    if ($type == 'payment') {
                        $annualstep->paymenthold = false;
                        if (isset($fee[$type]['hold']) && $fee[$type]['hold']) {
                            $annualstep->paymenthold = true;
                        }
                        $annualstep->{$type.'style'} = ($fee[$type]['type'] == 'list') ? 1 : 0;                      
                    }

                    //$annualstep->{$new_type} = $bean_by_type;

                }
            }
            $result[] = $annualstep;
        }
        return $result;
    }

    /**
     * Returns a float value from a given string.
     *
     * @see http://php.net/manual/de/function.floatval.php
     * @author info at marc-gutt dot de
     *
     * @param string $value
     * @return float
     */
    protected function floatvalue($value) { 
         return floatval(preg_replace('#^([-]*[0-9\.,\' ]+?)((\.|,){1}([0-9-]{1,2}))*$#e', "str_replace(array('.', ',', \"'\", ' '), '', '\\1') . '.\\4'", $value)); 
    }

    /**
     * make a heir annualfeetype from a legacy annualfeetype array.
     *
     * @param array $record holds the key/value array with legacy data
     * @return RedBean_OODBBean $feetype
     */
    protected function make_heir_feetype(array $record)
    {
        $this->useHeirDB();
        if ($feetype = R::findOne('pricetype', ' name = ?', array(mb_strtolower($record['printable_name'])))) {
            return $feetype;
        }
        $feetype = R::dispense('pricetype');
        //$feetype->token = mb_strtolower($record['printable_name']);
        $feetype->name = $record['printable_name'];
        try {
            R::store($feetype);
        } catch (Exception $e) {
            $message = 'Pricetype "'.$record['printable_name'].'" says: '.$e->getMessage();
            Cinnebar_Logger::instance()->log($message, $this->logname.'_pricetype');
            //echo $message."\n";
            return null;
        }
        return $feetype;
    }

    /**
     * make a heir paperstatus from a legacy status array.
     *
     * @param array $record holds the key/value array with legacy data
     * @return RedBean_OODBBean $paperstatus
     */
    protected function make_heir_paperstatus(array $record)
    {
        $this->useHeirDB();
        if ($paperstatus = R::findOne('cardstatus', ' name = ?', array(mb_strtolower($record['printable_name'])))) {
            return $paperstatus;
        }
        $paperstatus = R::dispense('cardstatus');
        //$paperstatus->name = mb_strtolower($record['printable_name']);
        $paperstatus->name = $record['printable_name'];
        try {
            R::store($paperstatus);
        } catch (Exception $e) {
            $message = 'Cardstatus "'.$record['shortname'].'" says: '.$e->getMessage();
            Cinnebar_Logger::instance()->log($message, $this->logname.'_cardstatus');
            //echo $message."\n";
            return null;
        }
        return $paperstatus;
    }

    /**
     * make a heir country from a legacy country array.
     *
     * @param array $record holds the key/value array with legacy data
     * @return RedBean_OODBBean $country
     */
    protected function make_heir_country(array $record)
    {
        $this->useHeirDB();
        if ($country = R::findOne('country', ' iso = ?', array(mb_strtolower($record['iso'])))) {
            return $country;
        }
        $country = R::dispense('country');
        $country->iso = mb_strtolower($record['iso']);
        $country->name = $record['printable_name'];
        $country->enabled = true;
        try {
            R::store($country);
        } catch (Exception $e) {
            $message = 'Country "'.$record['iso'].'" says: '.$e->getMessage();
            Cinnebar_Logger::instance()->log($message, $this->logname.'_country');
            //echo $message."\n";
            return null;
        }
        return $country;
    }

    /**
     * make a heir language from a legacy language array.
     *
     * @param array $record holds the key/value array with legacy data
     * @return RedBean_OODBBean $language
     */
    protected function make_heir_language(array $record)
    {
        $this->useHeirDB();
        if ($language = R::findOne('language', ' iso = ?', array(mb_strtolower($record['code'])))) {
            return $language;
        }
        $language = R::dispense('language');
        $language->iso = mb_strtolower($record['code']);
        $language->name = $record['printable_name'];
        $language->enabled = true;
        try {
            R::store($language);
        } catch (Exception $e) {
            $message = 'Language "'.$record['iso'].'" says: '.$e->getMessage();
            Cinnebar_Logger::instance()->log($message, $this->logname.'_language');
            //echo $message."\n";
            return null;
        }
        return $language;
    }

    /**
     * make a heir person from a legacy contact array.
     *
     * @param array $record holds the key/value array with legacy data
     * @return RedBean_OODBBean $person
     */
    protected function make_heir_person(array $record)
    {
        $this->useHeirDB();
        if ($person = R::findOne('person', ' nickname = ?', array(mb_strtolower($record['nickname'])))) {
            return $person;
        }
        $person = R::dispense('person');
        $person->nickname = $record['nickname'];
        $person->organization = $record['company'];
        $person->company = true;
        $person->firstname = $record['firstname'];
        //$person->middlename = '';
        $person->lastname = $record['lastname'];
        $person->note = $record['note'];
        $person->attention = $record['attention'];
        $person->account = $record['account'];
        $person->taxid = $record['tax_id'];
        $person->title = $record['title'];
        /*$person->maidenname = '';
        $person->birthdate = null;
        $person->jobtitle = '';
        $person->department = '';*/
        $language = $this->make_heir_language($this->load_legacy_language($record['language']));
        $person->iso = $language->iso;

        if (isset($record['email']) && $record['email']) {
            $email = R::dispense('email');
            $email->label = 'work';
            $email->value = $record['email'];
            $person->ownEmail[] = $email;
        }

        if (isset($record['website']) && $record['website']) {
            $url = R::dispense('url');
            $url->label = 'work';
            $url->value = $record['website'];
            $person->ownUrl[] = $url;
        }

        if (isset($record['phone']) && $record['phone']) {
            $phone = R::dispense('phone');
            $phone->label = 'work';
            $phone->value = $record['phone'];
            $person->ownPhone[] = $phone;
        }

        if (isset($record['cell']) && $record['cell']) {
            $cell = R::dispense('phone');
            $cell->label = 'cell';
            $cell->value = $record['cell'];
            $person->ownPhone[] = $cell;
        }

        if (isset($record['fax']) && $record['fax']) {
            $fax = R::dispense('phone');
            $fax->label = 'fax';
            $fax->value = $record['fax'];
            $person->ownPhone[] = $fax;
        }

        if (isset($record['postal1']) && $record['postal1']) {
            $postal1 = R::dispense('address');
            $postal1->label = 'work';
            $postal1->street = $record['postal1'];
            $person->ownAddress[] = $postal1;
            $person->legacyaddresswork = $record['postal1'];
        }

        if (isset($record['postal2']) && $record['postal2']) {
            $postal2 = R::dispense('address');
            $postal2->label = 'home';
            $postal2->street = $record['postal2'];
            $person->ownAddress[] = $postal2;
            $person->legacyaddresshome = $record['postal2'];
        }

        try {
            R::store($person);
        } catch (Exception $e) {
            $message = 'Person "'.$record['nickname'].'" says: '.$e->getMessage();
            Cinnebar_Logger::instance()->log($message, $this->logname.'_person');
            //echo $message."\n";
            return null;
        }
        return $person;
    }

    /**
     * load a legacy record by its type and id.
     *
     * @param string $type
     * @param int $id
     * @return array $user
     */
    protected function load_legacy($type, $id)
    {
        $this->useLegacyDB();
        $sql = <<<SQL
            SELECT
                {$type}.*
            FROM
                {$this->prefix}{$type} AS {$type}
            WHERE
                id = ?
            LIMIT 1
SQL;
        return R::getRow($sql, array($id));
    }

    /**
     * Returns an assoc of legacy gvrcard records from the legacy database.
     *
     * @param int $offset
     * @param int $limit
     * @return array
     */
    protected function load_legacy_cards($offset, $limit)
    {
        $this->useLegacyDB();
        $sql = <<<SQL
            SELECT
                card.*
            FROM
                {$this->prefix}gvrcard AS card
            WHERE
                card.is_deleted IS NULL
            ORDER BY
                card.id
            LIMIT {$offset}, {$limit}
SQL;
        return R::getAll($sql, array()); 
    }

    /**
     * Returns an assoc of legacy gvruser records from the legacy database.
     *
     * @param int $offset
     * @param int $limit
     * @return array
     */
    protected function load_legacy_users($offset, $limit)
    {
        $this->useLegacyDB();
        $sql = <<<SQL
            SELECT
                user.*
            FROM
                {$this->prefix}user AS user
            ORDER BY
                user.id
            LIMIT {$offset}, {$limit}
SQL;
        return R::getAll($sql, array()); 
    }

    /**
     * Returns an assoc of legacy gvrbill records from the legacy database.
     *
     * @param int $offset
     * @param int $limit
     * @return array
     */
    protected function load_legacy_bills($offset, $limit)
    {
        $this->useLegacyDB();
        $sql = <<<SQL
            SELECT
                bill.*
            FROM
                {$this->prefix}gvrbill AS bill
            WHERE
                bill.is_deleted IS NULL
            ORDER BY
                bill.id
            LIMIT {$offset}, {$limit}
SQL;
        return R::getAll($sql, array()); 
    }

    /**
     * Returns an assoc of legacy gvrannual records from the legacy database.
     *
     * @param int $offset
     * @param int $limit
     * @return array
     */
    protected function load_legacy_annuals($offset, $limit)
    {
        $this->useLegacyDB();
        $sql = <<<SQL
            SELECT
                annual.*
            FROM
                {$this->prefix}gvrannual AS annual
            WHERE
                annual.is_deleted IS NULL
            ORDER BY
                annual.id
            LIMIT {$offset}, {$limit}
SQL;
        return R::getAll($sql, array()); 
    }
}
