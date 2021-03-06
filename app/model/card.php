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
 * Manages cards.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Card extends Cinnebar_Model
{
    /**
     * Optional set of attributes defined by the attr set of this cards cardtype bean.
     */
    public $attrset = array();

    /**
     * Update the cardfeestep beans according to the country, cardtype and pricing and
     * returns an array of cardfeestep beans.
     *
     * @param int country_id
     * @param int cardtype_id
     * @param int pricetype_id
     * @return array
     */
    public function updateCardfeesteps($country_id = null, $cardtype_id = null, $pricetype_id = null)
    {
        error_log('I want to update this cards cardfeestep beans...');
        if ($this->bean->country->getId() != $country_id) {
            error_log('country mismatch');
        }
        if ($this->bean->pricetype->getId() != $pricetype_id) {
            error_log('pricetype mismatch');
        }
        if ($this->bean->cardtype->getId() != $cardtype_id) {
            error_log('cardtype mismatch');
        }
        if (! $rule = R::findOne('rule', ' country_id = ? AND cardtype_id = ? LIMIT 1', array($country_id, $cardtype_id))) {
            error_log('No rule found');
        }
        if ($rule->style != 0) {
            error_log('The rule is perpetual, cant handle that by now');
        }
        if (! $fee = R::findOne('fee', ' rule_id = ? AND pricetype_id = ? LIMIT 1', array($rule->getId(), $pricetype_id))) {
            error_log('No fee steps found');
        }
        error_log('Rule ' . $rule->getId() . ' and fee ' . $fee->getId());
        $feesteps = $fee->with(' ORDER BY id ')->ownFeestep;
        $cardsteps = $this->bean->with(' ORDER BY fy ')->ownCardfeestep;
        if ($feesteps) {
            foreach ($cardsteps as $id => $cardstep) {
                $feestep = array_shift($feesteps);
                if ($cardstep->done) {
                    continue;
                } //skip any done step
                $cardstep->net = $feestep->net;
                error_log('Step ' . $cardstep->fy . ' with feestep ' . $feestep->net);
            }
            error_log('Und I did. Ready.');
        } else {
            error_log('No feesteps definded. Most likely its an perpetual rule');
        }
        return $cardsteps;
    }

    /**
     * Adds this record to a multipay bean.
     */
    public function multipay()
    {
        $feesteps = $this->bean->getLatestCardfeesteps();
        $paymentCode = '';
        $amount = 0;
        $card = $this->bean;
        if (empty($feesteps)) {
            $paymentCode = '';
            $amount = 0;
        } else {
            foreach ($feesteps as $id => $feestep) {
                if (! $feestep->done) {
                    $paymentCode = '0' . (32 + $feestep->sequence);
                    $amount = $feestep->paymentnet;
                    break;
                }
            }
        }
        if (! isset($_SESSION['multipay']['id'])) {
            $multipay = R::dispense('multipay');
            R::store($multipay);
            $_SESSION['multipay']['id'] = $multipay->getId();
        }
        $multipay = R::load('multipay', $_SESSION['multipay']['id']);
        $multipay->user = R::dispense('user')->current();
        $mfee = R::dispense('multipayfee');
        $mfee->card = $this->bean;
        $mfee->cardname = $this->bean->name;
        $mfee->applicationnumber = 'EP' . str_replace(array(' ', 'EP'), array('', ''), $this->bean->applicationnumber);

        $ts = strtotime($this->bean->applicationdate);
        list($fy_app_date, $fm_app_date, $fd_app_date) = array(date('Y', $ts), date('m', $ts), date('d', $ts));
        $ts = strtotime(sprintf('%s-%s-%s', $feestep->fy, $fm_app_date, $fd_app_date));
        $mfee->datedue = date('Y-m-d', $ts);

        // to prevent that the card bean gets dirty we load person like this
        if (! $this->bean->applicant_id) {
            $person = R::load('person', $this->bean->client_id);
        } else {
            $person = R::load('person', $this->bean->applicant_id);
        }
        $mfee->applicantnickname = $person->name;
        $mfee->paymentcode = $paymentCode;
        $mfee->amount = $amount;
        $multipay->ownMultipayfee[] = $mfee;
        R::store($multipay);
        /*
        error_log( $this->bean->name . ' should be added to multipay with ' .
                                                            $paymentCode . ' EUR ' . $amount);*/
    }

    /**
     * Returns a stash bean of this card.
     *
     * The stash bean  holds attributes that were stashed here.
     *
     * @return RedBean_OODBBean
     */
    public function stash()
    {
        if (! $this->bean->stash) {
            $this->bean->stash = R::dispense('stash');
        }
        return $this->bean->stash;
    }

    /**
     * Look up searchtext in all fields of a bean.
     *
     * @param string $searchphrase
     * @return array
     */
    public function searchAllFields($searchphrase = '')
    {
        $searchphrase = '%'.$searchphrase.'%';
        $searchphraseflat = '%'.$this->alphanumericonly($searchphrase).'%';
        return R::find('card', ' name LIKE :f OR searchname LIKE :fan OR title LIKE :f OR (attrset LIKE :f OR attrset LIKE :fan) OR codeword LIKE :f OR note LIKE :f OR clientcode LIKE :f OR clientnickname LIKE :f OR clientaddress LIKE :f OR pattern LIKE :f OR issuenumber LIKE :f OR disclosurenumber LIKE :f OR applicantnickname LIKE :f OR applicantaddress LIKE :f OR applicantcode LIKE :f OR foreignnickname LIKE :f OR foreignaddress LIKE :f OR foreigncode LIKE :f OR invreceivernickname LIKE :f OR invreceiveraddress LIKE :f OR invreceivercode LIKE :f OR feesubject LIKE :f OR revenueaccount LIKE :f OR customeraccount LIKE :f OR applicationnumberflat LIKE :fan OR issuenumberflat LIKE :fan OR disclosurenumberflat LIKE :fan OR titleflat LIKE :fan OR codewordflat LIKE :fan OR noteflat LIKE :fan OR clientcodeflat LIKE :fan OR applicantcodeflat LIKE :fan OR foreigncodeflat LIKE :fan OR invreceivercodeflat LIKE :fan', array(':f' => $searchphrase, ':fan' => $searchphraseflat));
    }

    /**
     * Returns a short text describing the bean for humans.
     *
     * @param Cinnebar_View $view
     * @return string
     */
    public function hitname(Cinnebar_View $view)
    {
        $template = '<a href="%s">%s</a>'."\n";
        $name = sprintf('%s %s %s', $this->bean->name, $this->bean->countryName(), $this->bean->cardtypeName());
        return sprintf($template, $view->url(sprintf('/%s/edit/%d', $this->bean->getMeta('type'), $this->bean->getId())), $name);
    }

    /**
     * Searches for given searchterm within bean and returns the result-set as an multi-dim array
     * after the given layout.
     *
     * @param string $term contains the searchterm as given by jQuery.autocomplete
     * @param string (optional) $layout defaults to "default"
     * @return array
     */
    public function clairvoyant($term, $layout = 'default')
    {
        switch ($layout) {
            default:
                $sql = <<<SQL

                SELECT
                    card.id AS id,
                    CONCAT_WS(' ', card.name, country.name, cardtype.name) AS label,
                    card.name AS cardname,
                    IFNULL(card.invreceiver_id, card.client_id) AS client_id,
                    IFNULL(card.invreceivernickname, card.clientnickname) AS nickname,
                    IFNULL(card.invreceiveraddress, card.clientaddress) AS address,
                    IFNULL(card.invreceivercode, card.clientcode) AS code,
                    IFNULL(card.applicant_id, card.client_id) AS applicant_id,
                    IFNULL(card.applicantnickname, card.clientnickname) AS applicantnickname,
                    CONCAT('EP', REPLACE(card.applicationnumber, ' ', '')) AS epo,
                    card.user_id AS attorney_id

                FROM
                    card

                LEFT JOIN country ON country.id = card.country_id
                LEFT JOIN cardtype ON cardtype.id = card.cardtype_id

                WHERE
                    card.name like :searchtext

                ORDER BY
                    card.name

SQL;
        }
        return $res = R::getAll($sql, array(':searchtext' => $term.'%'));
    }

    /**
     * Returns SQL for filtering these beans.
     *
     * @param string $where_clause
     * @param string $order_clause
     * @param int $offset
     * @param int $limit
     * @return string $SQL
     */
    public function sqlForFilters($where_clause = '1', $order_clause = 'id', $offset = 0, $limit = 1)
    {
        $sql = <<<SQL
		SELECT
			card.id as id

		FROM
			card

		LEFT JOIN country ON country.id = card.country_id
        LEFT JOIN person AS client ON client.id = card.client_id
		LEFT JOIN cardtype ON cardtype.id = card.cardtype_id
		LEFT JOIN cardstatus ON cardstatus.id = card.cardstatus_id
		LEFT JOIN user AS attorney ON attorney.id = card.user_id
        LEFT JOIN feetype ON feetype.id = card.feetype_id
        LEFT JOIN cardfeestep ON cardfeestep.id = (SELECT cardfeestep.id FROM cardfeestep WHERE cardfeestep.card_id = card.id AND cardfeestep.done = 0 ORDER BY cardfeestep.fy ASC LIMIT 1)

		WHERE {$where_clause}

		ORDER BY {$order_clause}

		LIMIT {$offset}, {$limit}
SQL;
        return $sql;
    }
	
    /**
     * Returns SQL for exportToCSV a single bean.
   	 *
     * @return string $SQL
     */
    public function sqlForExportToCSV()
    {
        $sql = <<<SQL
		SELECT
			card.id AS id,
			card.*,
			country.*,
			client.*,
			cardtype.*,
			cardstatus.*,
			attorney.*,
			feetype.*,
			cardfeestep.*

		FROM
			card

		LEFT JOIN country ON country.id = card.country_id
        LEFT JOIN person AS client ON client.id = card.client_id
		LEFT JOIN cardtype ON cardtype.id = card.cardtype_id
		LEFT JOIN cardstatus ON cardstatus.id = card.cardstatus_id
		LEFT JOIN user AS attorney ON attorney.id = card.user_id
        LEFT JOIN feetype ON feetype.id = card.feetype_id
        LEFT JOIN cardfeestep ON cardfeestep.id = (SELECT cardfeestep.id FROM cardfeestep WHERE cardfeestep.card_id = card.id AND cardfeestep.done = 0 ORDER BY cardfeestep.fy ASC LIMIT 1)

		WHERE card.id = ?
		
		LIMIT 1
		
SQL;
        return $sql;
    }

    /**
     * Returns SQL for total.
     *
     * @param string $where_clause
     * @return string $SQL
     */
    public function sqlForTotal($where_clause = '1')
    {
        $sql = <<<SQL
		SELECT
			COUNT(DISTINCT(card.id)) as total

		FROM
			card

		LEFT JOIN country ON country.id = card.country_id
		LEFT JOIN person AS client ON client.id = card.client_id
		LEFT JOIN cardtype ON cardtype.id = card.cardtype_id
		LEFT JOIN cardstatus ON cardstatus.id = card.cardstatus_id
		LEFT JOIN user AS attorney ON attorney.id = card.user_id
        LEFT JOIN feetype ON feetype.id = card.feetype_id
        LEFT JOIN cardfeestep ON cardfeestep.id = (SELECT cardfeestep.id FROM cardfeestep WHERE cardfeestep.card_id = card.id AND cardfeestep.done = 0 ORDER BY cardfeestep.fy ASC LIMIT 1)

		WHERE {$where_clause}
SQL;
        return $sql;
    }

    /**
     * Return ownPriority(ies).
     *
     * @param bool $add if true an empty records gets added.
     * @return array
     */
    public function getownPriority($add = false)
    {
        $own = R::find('priority', ' card_id = ?', array($this->bean->getId()));
        if ($add) {
            $own[] = R::dispense('priority');
        }
        return $own;
    }

    /**
     * Return ownCardfeestep(s).
     *
     * @param bool $add if true an empty records gets added.
     * @return array
     */
    public function getownCardfeestep($add = false)
    {
        $own = R::find('cardfeestep', ' card_id = ? ORDER BY fy', array($this->bean->getId()));
        if ($add) {
            $own[] = R::dispense('cardfeestep');
        }
        return $own;
    }

    /**
     * Returns a country bean.
     *
     * @return RedBean_OODBBean
     */
    public function country()
    {
        if (! $this->bean->country) {
            $this->bean->country = R::dispense('country');
        }
        return $this->bean->country;
    }

    /**
     * Returns a country name.
     *
     * @return string
     */
    public function countryName()
    {
        return '<span class="flag '.$this->bean->country()->iso.'"></span>'.$this->bean->country()->name;
    }

    /**
     * Returns a country iso.
     *
     * @return string
     */
    public function countryIso()
    {
        return '<span title="'.$this->bean->country()->name.'" class="flag '.$this->bean->country()->iso.'"></span>'.strtoupper($this->bean->country()->iso);
    }

    /**
     * Returns a cardtype name.
     *
     * @return string
     */
    public function cardtypeName()
    {
        return $this->bean->cardtype()->name;
    }

    /**
     * Returns a cardstatus name.
     *
     * @return string
     */
    public function cardstatusName()
    {
        return $this->bean->cardstatus()->name;
    }

    /**
     * Returns status humanreadable.
     *
     * @return string
     */
    public function cardStatusInternal()
    {
        return __('annual_label_' . $this->bean->status);
    }

    /**
     * Returns the latest feestep year.
     *
     * The EPO payment code would be 32 + sequence of the cardfeestep. E.g. if you are about
     * to pay for the 4th year the code generated had to be '034 Jahresgebühr für das 4. Jahr'.
     *
     * @return string
     */
    public function feestepMultipay()
    {
        $feesteps = $this->bean->getLatestCardfeesteps();
        if (! $feesteps) {
            return __('cardfeestep_undefined');
        }
        foreach ($feesteps as $id => $feestep) {
            if (! $feestep->done) {
                return $feestep->fy;
            }
        }
        return __('cardfeestep_over_the_top');
    }

    /**
     * Returns a text string representing feeorderneeded and hold status in multipay list.
     *
     * @return string
     */
    public function feeorderMultipay()
    {
        $result = '';
        if ($this->bean->feeorderneeded) {
            $result .= __('card_label_feeorderneeded');
        }
        if ($this->bean->onhold) {
            $result .= ' ' . __('card_label_onhold');
        }
        return $result;
    }

    /**
     * Returns the latest cardfeestep bean.
     *
     * @return mixed Empty array or array of cardfeestep beans or false
     */
    public function getLatestCardfeesteps()
    {
        return R::find('cardfeestep', ' card_id = ? ORDER BY fy', array( $this->bean->getId() ));
    }

    /**
     * Returns a client name.
     *
     * @return RedBean_OODBBean
     */
    public function clientName()
    {
        return $this->bean->client()->name;
    }

    /**
     * Returns a client nickname.
     *
     * @return RedBean_OODBBean
     */
    public function clientNickname()
    {
        return $this->bean->client()->nickname;
    }

    /**
     * Returns a invoice receivers nickname.
     *
     * @return string
     */
    public function invreceiverNickname()
    {
        return $this->bean->invreceiver()->nickname;
    }

    /**
     * Returns a foreign nickname.
     *
     * @return string
     */
    public function foreignNickname()
    {
        return $this->bean->foreign()->nickname;
    }

    /**
     * Returns a team name.
     *
     * @return string
     */
    public function teamName()
    {
        return $this->bean->teammashup;
    }

    /**
     * Returns a attorney name.
     *
     * @return string
     */
    public function attorneyName()
    {
        return $this->bean->user()->shortname;
    }

    /**
     * Returns a cardstatus bean.
     *
     * @return RedBean_OODBBean
     */
    public function cardstatus()
    {
        if (! $this->bean->cardstatus) {
            $this->bean->cardstatus = R::dispense('cardstatus');
        }
        return $this->bean->cardstatus;
    }

    /**
     * Returns a person as client bean.
     *
     * @return RedBean_OODBBean
     */
    public function client()
    {
        if (! $this->bean->fetchAs('person')->client) {
            $this->bean->client = R::dispense('person');
        }
        return $this->bean->fetchAs('person')->client;
    }

    /**
     * Returns card as the original card to this one.
     *
     * @return RedBean_OODBBean
     */
    public function original()
    {
        if (! $this->bean->fetchAs('card')->original) {
            $this->bean->original = R::dispense('card');
        }
        return $this->bean->original;
    }

    /**
     * Returns a person as applicant bean.
     *
     * @return RedBean_OODBBean
     */
    public function applicant()
    {
        if (! $this->bean->fetchAs('person')->applicant) {
            $this->bean->applicant = R::dispense('person');
        }
        return $this->bean->applicant;
    }

    /**
     * Returns a applicant nickname.
     *
     * @return string
     */
    public function applicantNickname()
    {
        return $this->bean->applicant()->nickname;
    }

    /**
     * Returns a person as foreign bean.
     *
     * @return RedBean_OODBBean
     */
    public function foreign()
    {
        if (! $this->bean->fetchAs('person')->foreign) {
            $this->bean->foreign = R::dispense('person');
        }
        return $this->bean->foreign;
    }

    /**
     * Returns a person as invreceiver bean.
     *
     * @return RedBean_OODBBean
     */
    public function invreceiver()
    {
        if (! $this->bean->fetchAs('person')->invreceiver) {
            $this->bean->invreceiver = R::dispense('person');
        }
        return $this->bean->fetchAs('person')->invreceiver;
    }

    /**
     * Returns a fee bean.
     *
     * @return RedBean_OODBBean
     */
    public function fee()
    {
        if (! $this->bean->fee) {
            $this->bean->fee = R::dispense('fee');
        }
        return $this->bean->fee;
    }

    /**
     * Returns a rule bean.
     *
     * @return RedBean_OODBBean
     */
    public function rule()
    {
        if (! $this->bean->rule) {
            $this->bean->rule = R::dispense('rule');
        }
        return $this->bean->rule;
    }

    /**
     * Returns a cardtype bean.
     *
     * @return RedBean_OODBBean
     */
    public function cardtype()
    {
        if (! $this->bean->cardtype) {
            $this->bean->cardtype = R::dispense('cardtype');
        }
        return $this->bean->cardtype;
    }

    /**
     * Returns a pricetype bean.
     *
     * @return RedBean_OODBBean
     */
    public function pricetype()
    {
        if (! $this->bean->pricetype) {
            $this->bean->pricetype = R::dispense('pricetype');
        }
        return $this->bean->pricetype;
    }

    /**
     * Returns an array of possible feeduedate years.
     *
     * @return array
     */
    public function possibleFeeYears()
    {
        $sql = <<<SQL
		SELECT
			DISTINCT(YEAR(card.feeduedate)) as y

		FROM
			card

		WHERE
		    card.feeduedate != '0000-00-00'

		ORDER BY card.feeduedate
SQL;
        return R::$adapter->getAssoc($sql, array());
    }

    /**
     * Returns SQL to retrieve cards in controller annual on the index page.
     *
     * @param string
     * @return string
     */
    public function sqlForAnnuity($where_clause = '1')
    {
        $sql = <<<SQL
		SELECT
			card.id

		FROM
			card

		WHERE
            {$where_clause}

		ORDER BY card.feeduedate
SQL;
        return $sql;
    }

    /**
     * Returns SQL to retrieve cards where annuity is due
     *
     * @deprecated
     * @return string
     */
    public function sqlForAnnuityComplete()
    {
        $sql = <<<SQL
		SELECT
			card.id

		FROM
			card

		WHERE
		    YEAR(card.feeduedate) = ? AND
		    MONTH(card.feeduedate) = ? AND
		    card.feeinactive = 0 AND
		    card.user_id = ? AND
		    card.teammashup like ?

		ORDER BY card.feeduedate
SQL;
        return $sql;
    }

    /**
     * Returns SQL to retrieve cards where annuity is due
     *
     * @deprecated
     * @return string
     */
    public function sqlForAnnuityAttorney()
    {
        $sql = <<<SQL
		SELECT
			card.id

		FROM
			card

		WHERE
		    YEAR(card.feeduedate) = ? AND
		    MONTH(card.feeduedate) = ? AND
		    card.feeinactive = 0 AND
		    card.user_id = ?

		ORDER BY card.feeduedate
SQL;
        return $sql;
    }

    /**
     * Returns SQL to retrieve cards where annuity is due
     *
     * @deprecated
     * @return string
     */
    public function sqlForAnnuityTeam()
    {
        $sql = <<<SQL
		SELECT
			card.id

		FROM
			card

		WHERE
		    YEAR(card.feeduedate) = ? AND
		    MONTH(card.feeduedate) = ? AND
		    card.feeinactive = 0 AND
		    card.teammashup like ?

		ORDER BY card.feeduedate
SQL;
        return $sql;
    }

    /**
     * Returns SQL to retrieve cards where annuity is due
     *
     * @deprecated
     * @return string
     */
    public function sqlForAnnuityNone()
    {
        $sql = <<<SQL
		SELECT
			card.id

		FROM
			card

		WHERE
		    YEAR(card.feeduedate) = ? AND
		    MONTH(card.feeduedate) = ? AND
		    card.feeinactive = 0

		ORDER BY card.feeduedate
SQL;
        return $sql;
    }

    /**
     * Returns a feetype bean.
     *
     * @return RedBean_OODBBean
     */
    public function feetype()
    {
        if (! $this->bean->feetype) {
            $this->bean->feetype = R::dispense('feetype');
        }
        return $this->bean->feetype;
    }

    /**
     * Returns the feetype name.
     *
     * @return string
     */
    public function feetypeName()
    {
        return $this->bean->feetype()->name;
    }

    /**
     * Returns a user bean.
     *
     * @return RedBean_OODBBean
     */
    public function user()
    {
        if (! $this->bean->user) {
            $this->bean->user = R::dispense('user');
        }
        return $this->bean->user;
    }

    /**
     * Returns a customized menu.
     *
     * Adds a menu item which allows to send/queue a newsletter. It will only be added if there
     * is a current newsletter bean.
     *
     * @param string $action
     * @param Cinnebar_View $view
     * @param Cinnebar_Menu (optional) $menu
     * @return Cinnebar_Menu
     */
    public function makeMenu($action, Cinnebar_View $view, Cinnebar_Menu $menu = null)
    {
        $menu = parent::makeMenu($action, $view, $menu);

        $menu->add(__('layout_list2'), $view->url(sprintf('/%s/index/%d/%d/%s/%d/%d', $this->bean->getMeta('type'), 1, Controller_Scaffold::LIMIT, 'table2', $view->order, $view->dir)), 'scaffold_card_list2');

        $menu->add(__('layout_report'), $view->url(sprintf('/%s/report/%d/%d/%s/%d/%d', $this->bean->getMeta('type'), 1, Controller_Scaffold::LIMIT, 'table', $view->order, $view->dir)), 'scaffold_report');

        $menu->add(__('layout_multipay'), $view->url(sprintf('/%s/index/%d/%d/%s/%d/%d', $this->bean->getMeta('type'), 1, Controller_Scaffold::LIMIT, 'multipay', $view->order, $view->dir)), 'scaffold_report');

        //$menu->add(__('layout_extended'), $view->url(sprintf('/%s/index/%d/%d/%s/%d/%d', $this->bean->getMeta('type'), 1, Controller_Scaffold::LIMIT, 'extended', $view->order, $view->dir)), 'scaffold_extended');
        if ($this->bean->getId()) {
            $menu->add(__('scaffold_clone'), $view->url(sprintf('/%s/duplicate/%d', $this->bean->getMeta('type'), $this->bean->getId())), 'scaffold-clone');
            $menu->add(__('card_pdf'), $view->url(sprintf('/card/pdf/%d/%d/%d/%s/%d/%d', $this->bean->getId(), $view->page, $view->limit, $view->layout, $view->order, $view->dir)), 'scaffold-pdf');
            $menu->add(__('card_family_pdf'), $view->url(sprintf('/card/pdf/%d/%d/%d/%s/%d/%d/family', $this->bean->getId(), $view->page, $view->limit, $view->layout, $view->order, $view->dir)), 'scaffold-pdf');
        } else {
            // add CSV export action: vrone - export list type one
            $menu->add(__('scaffold_csv_card_one'), $view->url(sprintf('/%s/press/%d/%d/%s/%d/%d', $this->bean->getMeta('type'), $view->page, $view->limit, 'vrone', $view->order, $view->dir)), 'scaffold_csv');
            // add CSV export action: vrone - export list type one
            $menu->add(__('scaffold_csv_card_two'), $view->url(sprintf('/%s/press/%d/%d/%s/%d/%d', $this->bean->getMeta('type'), $view->page, $view->limit, 'vrtwo', $view->order, $view->dir)), 'scaffold_csv');
            // add a link which generates a PDF and downloads it for printing
            $menu->add(__('scaffold_html_to_pdf'), $view->url(sprintf('/%s/htmlpdf/%d/%d/%s/%d/%d', $this->bean->getMeta('type'), $view->page, $view->limit, $view->layout, $view->order, $view->dir)), 'scaffold_htmlpdf');
        }
        return $menu;
    }

    /**
     * Returns an array of this card for use with pdf output.
     *
     * @param Cinnebar_View $view
     * @return array
     */
    public function genDataPdf(Cinnebar_View $view)
    {
        $priorities = '';
        // build priors
        foreach ($this->bean->ownPriority as $id=>$priority) {
            $priorities .= sprintf('%s %s %s', $priority->country->name, $priority->number, $view->date($priority->date))."\n";
        }
        $_client = $this->bean->client();
        $arr = array(
            'client' => $_client->name . "\n" . $_client->addressLabelByType()->getFormattedAddress(),
            'title' => $this->bean->title,
            'codeword' => $this->bean->codeword,
            'note' => $this->bean->note,
            'classes' => $this->bean->pattern,
            'priority' => $priorities,
            'foreign' => $this->bean->foreignaddress,
            'number' => $this->bean->name,
            'type' => $this->bean->cardtype()->name,
            'country' => $this->bean->country()->name,
            'original_number' => $this->bean->original()->name,
            'application_date' => $view->date($this->bean->applicationdate),
            'application_number' => $this->bean->applicationnumber,
            'disclosure_date' => $view->date($this->bean->disclosuredate),
            'disclosure_number' => $this->bean->disclosurenumber,
            'issue_date' => $view->date($this->bean->issuedate),
            'issue_number' => $this->bean->issuenumber
        );
        return $arr;
    }

    /**
     * Returns an array with possible attributes for order clauses and such.
     *
     * @param string (optional) $layout defaults to table and can be of any value
     * @return array
     */
    public function attributes($layout = 'table')
    {
        switch ($layout) {
            case 'multipay':
                $ret = array(
                    array(
                        'attribute' => 'name',
                        'orderclause' => 'card.sortnumber',
                        'class' => 'text',
                        'width' => '10%',
                        'filter' => array(
                            'tag' => 'text',
                            'orderclause' => 'card.name'
                        )
                    ),
                    array(
                        'attribute' => 'country_id',
                        'orderclause' => 'country.iso',
                        'class' => 'text',
                        'width' => '5%',
                        'callback' => array(
                            'name' => 'countryIso'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'cardtype_id',
                        'orderclause' => 'cardtype.name',
                        'class' => 'text',
                        'width' => '5%',
                        'callback' => array(
                            'name' => 'cardtypeName'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'cardstatus_id',
                        'orderclause' => 'cardstatus.name',
                        'class' => 'text',
                        'width' => '10%',
                        'callback' => array(
                            'name' => 'cardstatusName'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'user_id',
                        'orderclause' => 'attorney.shortname',
                        'class' => 'text',
                        'width' => '10%',
                        'callback' => array(
                            'name' => 'attorneyName'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'teammashup',
                        'orderclause' => 'card.teammashup',
                        'class' => 'text',
                        'width' => '10%',
                        'callback' => array(
                            'name' => 'teamName'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'feetype_id',
                        'orderclause' => 'feetype.name',
                        'class' => 'text',
                        'width' => '5%',
                        'callback' => array(
                            'name' => 'feetypeName'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'applicationdate',
                        'orderclause' => 'card.applicationdate',
                        'class' => 'date',
                        'viewhelper' => 'date',
                        'width' => '15%',
                        'filter' => array(
                            'tag' => 'date'
                        )
                    ),
                    array(
                        'attribute' => 'applicationdate AS appmonth',
                        'orderclause' => 'MONTH( card.applicationdate )',
                        'class' => 'number',
                        'width' => '5%',
                        'callback' => array(
                            'name' => 'monthdueByName'
                        ),
                        'filter' => array(
                            'tag' => 'chooser',
                            'options' => array(
                                1 => __('month_label_01'),
                                2 => __('month_label_02'),
                                3 => __('month_label_03'),
                                4 => __('month_label_04'),
                                5 => __('month_label_05'),
                                6 => __('month_label_06'),
                                7 => __('month_label_07'),
                                8 => __('month_label_08'),
                                9 => __('month_label_09'),
                                10 => __('month_label_10'),
                                11 => __('month_label_11'),
                                12 => __('month_label_12')
                            )
                        )
                    ),
                    array(
                        'attribute' => 'cardfeestep.fy',
                        'orderclause' => 'cardfeestep.fy',
                        'class' => 'number',
                        'width' => '15%',
                        'callback' => array(
                            'name' => 'feestepMultipay'
                        ),
                        'filter' => array(
                            'tag' => 'number'
                        )
                    ),
                    array(
                        'attribute' => 'client_id',
                        'orderclause' => 'client.nickname',
                        'class' => 'text',
                        'width' => '10%',
                        'callback' => array(
                            'name' => 'clientNickname'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'codeword',
                        'orderclause' => 'card.codeword',
                        'class' => 'text',
                        'width' => '20%',
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'feeinactive',
                        'orderclause' => 'card.feeinactive',
                        'class' => 'bool',
                        'width' => '5%',
                        'viewhelper' => 'boolperv',
                        'filter' => array(
                            'tag' => 'boolperv'
                        )
                    ),
                    array(
                        'attribute' => 'status',
                        'orderclause' => 'card.status',
                        'class' => 'text',
                        'width' => '5%',
                        'callback' => array(
                            'name' => 'cardStatusInternal'
                        ),
                        'filter' => array(
                            'tag' => 'chooser',
                            'options' => array(
                                'awareness' => __('annual_label_awareness'),
                                'billed' => __('annual_label_billed'),
                                'done' => __('annual_label_done'),
                                'due' => __('annual_label_due'),
                                'inactive' => __('annual_label_inactive'),
                                'maintain' => __('annual_label_maintain'),
                                'onhold' => __('annual_label_onhold'),
                                'paid' => __('annual_label_paid')
                            )
                        )
                    )
                );
                break;
            case 'table2':
                $ret = array(
                    array(
                        'attribute' => 'name',
                        'orderclause' => 'card.sortnumber',
                        'class' => 'text',
                        'width' => '10%',
                        'filter' => array(
                            'tag' => 'text',
                            'orderclause' => 'card.name'
                        )
                    ),
                    array(
                        'attribute' => 'country_id',
                        'orderclause' => 'country.iso',
                        'class' => 'text',
                        'width' => '5%',
                        'callback' => array(
                            'name' => 'countryIso'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'cardtype_id',
                        'orderclause' => 'cardtype.name',
                        'class' => 'text',
                        'width' => '5%',
                        'callback' => array(
                            'name' => 'cardtypeName'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'cardstatus_id',
                        'orderclause' => 'cardstatus.name',
                        'class' => 'text',
                        'width' => '10%',
                        'callback' => array(
                            'name' => 'clientstatus'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'user_id',
                        'orderclause' => 'attorney.shortname',
                        'class' => 'text',
                        'width' => '10%',
                        'callback' => array(
                            'name' => 'attorneyName'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'applicationnumber',
                        'orderclause' => 'card.applicationnumber',
                        'class' => 'text',
                        'width' => '15%',
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'client_id',
                        'orderclause' => 'client.nickname',
                        'class' => 'text',
                        'width' => '15%',
                        'callback' => array(
                            'name' => 'clientNickname'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'title',
                        'orderclause' => 'card.title',
                        'class' => 'text',
                        'width' => '20%',
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'codeword',
                        'orderclause' => 'card.codeword',
                        'class' => 'text',
                        'width' => '20%',
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'feeinactive',
                        'orderclause' => 'card.feeinactive',
                        'class' => 'bool',
                        'width' => '5%',
                        'viewhelper' => 'boolperv',
                        'filter' => array(
                            'tag' => 'boolperv'
                        )
                    )
                );
                break;
            case 'annual':
                $ret = array(
                    array(
                        'attribute' => 'feeduedate',
                        'orderclause' => 'card.feeduedate',
                        'class' => 'date',
                        'viewhelper' => 'date',
                        'filter' => array(
                            'tag' => 'date'
                        )
                    ),
                    array(
                        'attribute' => 'status',
                        'orderclause' => 'card.status',
                        'class' => 'text',
                        'callback' => array(
                            'name' => 'cardStatusInternal'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'name',
                        'orderclause' => 'card.name',
                        'class' => 'text',
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'country_id',
                        'orderclause' => 'country.iso',
                        'class' => 'text',
                        'callback' => array(
                            'name' => 'countryIso'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'cardtype_id',
                        'orderclause' => 'cardtype.name',
                        'class' => 'text',
                        'callback' => array(
                            'name' => 'cardtypeName'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'cardstatus_id',
                        'orderclause' => 'cardstatus.name',
                        'class' => 'text',
                        'callback' => array(
                            'name' => 'cardstatusName'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'user_id',
                        'orderclause' => 'attorney.shortname',
                        'class' => 'text',
                        'callback' => array(
                            'name' => 'attorneyName'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'client_id',
                        'orderclause' => 'client.nickname',
                        'class' => 'text',
                        'callback' => array(
                            'name' => 'clientNickname'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'title',
                        'orderclause' => 'card.title',
                        'class' => 'text',
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'codeword',
                        'orderclause' => 'card.codeword',
                        'class' => 'text',
                        'filter' => array(
                            'tag' => 'text'
                        )
                    )
                );
                break;
            case 'extended':
                $ret = array(
                    array(
                        'attribute' => 'name',
                        'orderclause' => 'card.sortnumber',
                        'class' => 'text',
                        'filter' => array(
                            'tag' => 'text',
                            'orderclause' => 'card.name'
                        )
                    ),
                    array(
                        'attribute' => 'country_id',
                        'orderclause' => 'country.iso',
                        'class' => 'text',
                        'callback' => array(
                            'name' => 'countryIso'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'cardtype_id',
                        'orderclause' => 'cardtype.name',
                        'class' => 'text',
                        'callback' => array(
                            'name' => 'cardtypeName'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'cardstatus_id',
                        'orderclause' => 'cardstatus.name',
                        'class' => 'text',
                        'callback' => array(
                            'name' => 'cardstatusName'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'user_id',
                        'orderclause' => 'attorney.shortname',
                        'class' => 'text',
                        'callback' => array(
                            'name' => 'attorneyName'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'client_id',
                        'orderclause' => 'client.nickname',
                        'class' => 'text',
                        'callback' => array(
                            'name' => 'clientNickname'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'title',
                        'orderclause' => 'card.title',
                        'class' => 'text',
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'codeword',
                        'orderclause' => 'card.codeword',
                        'class' => 'text',
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'applicationnumber',
                        'orderclause' => 'card.applicationnumber',
                        'class' => 'text',
                        'filter' => array(
                            'tag' => 'text'
                        )
                    )
                );
                break;
            case 'report':
                $ret = array(
                    array(
                        'attribute' => 'name',
                        'orderclause' => 'card.sortnumber',
                        'class' => 'text',
                        'filter' => array(
                            'tag' => 'text',
                            'orderclause' => 'card.name'
                        )
                    ),
                    array(
                        'attribute' => 'country_id',
                        'orderclause' => 'country.iso',
                        'class' => 'text',
                        'callback' => array(
                            'name' => 'countryIso'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'cardtype_id',
                        'orderclause' => 'cardtype.name',
                        'class' => 'text',
                        'callback' => array(
                            'name' => 'cardtypeName'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'cardstatus_id',
                        'orderclause' => 'cardstatus.name',
                        'class' => 'text',
                        'callback' => array(
                            'name' => 'cardstatusName'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'user_id',
                        'orderclause' => 'attorney.shortname',
                        'class' => 'text',
                        'callback' => array(
                            'name' => 'attorneyName'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'teammashup',
                        'orderclause' => 'card.teammashup',
                        'class' => 'text',
                        'callback' => array(
                            'name' => 'teamName'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'client_id',
                        'orderclause' => 'client.nickname',
                        'class' => 'text',
                        'callback' => array(
                            'name' => 'clientNickname'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'clientcode',
                        'orderclause' => 'card.clientcode',
                        'class' => 'text',
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'applicant_id',
                        'orderclause' => 'card.applicantnickname',
                        'class' => 'text',
                        'callback' => array(
                            'name' => 'applicantNickname'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'applicantcode',
                        'orderclause' => 'card.applicantcode',
                        'class' => 'text',
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'invreceiver_id',
                        'orderclause' => 'card.invreceivernickname',
                        'class' => 'text',
                        'callback' => array(
                            'name' => 'invreceiverNickname'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'invreceivercode',
                        'orderclause' => 'card.invreceivercode',
                        'class' => 'text',
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'foreign_id',
                        'orderclause' => 'card.foreignnickname',
                        'class' => 'text',
                        'callback' => array(
                            'name' => 'foreignNickname'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'foreigncode',
                        'orderclause' => 'card.foreigncode',
                        'class' => 'text',
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'title',
                        'orderclause' => 'card.title',
                        'class' => 'text',
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'codeword',
                        'orderclause' => 'card.codeword',
                        'class' => 'text',
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'note',
                        'orderclause' => 'card.note',
                        'class' => 'text',
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'pattern',
                        'orderclause' => 'card.pattern',
                        'class' => 'text',
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'applicationdate',
                        'orderclause' => 'card.applicationdate',
                        'class' => 'date',
                        'viewhelper' => 'date',
                        'filter' => array(
                            'tag' => 'date'
                        )
                    ),
                    array(
                        'attribute' => 'applicationnumber',
                        'orderclause' => 'card.applicationnumber',
                        'class' => 'text',
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'issuedate',
                        'orderclause' => 'card.issuedate',
                        'class' => 'date',
                        'viewhelper' => 'date',
                        'filter' => array(
                            'tag' => 'date'
                        )
                    ),
                    array(
                        'attribute' => 'issuenumber',
                        'orderclause' => 'card.issuenumber',
                        'class' => 'text',
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'disclosuredate',
                        'orderclause' => 'card.disclosuredate',
                        'class' => 'date',
                        'viewhelper' => 'date',
                        'filter' => array(
                            'tag' => 'date'
                        )
                    ),
                    array(
                        'attribute' => 'disclosurenumber',
                        'orderclause' => 'card.disclosurenumber',
                        'class' => 'text',
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'feeduedate',
                        'orderclause' => 'card.feeduedate',
                        'class' => 'date',
                        'viewhelper' => 'date',
                        'filter' => array(
                            'tag' => 'date'
                        )
                    )
                );
                break;
            default:
                $ret = array(
                    array(
                        'attribute' => 'name',
                        'orderclause' => 'card.sortnumber',
                        'class' => 'text',
                        'width' => '10%',
                        'filter' => array(
                            'tag' => 'text',
                            'orderclause' => 'card.name'
                        )
                    ),
                    array(
                        'attribute' => 'country_id',
                        'orderclause' => 'country.iso',
                        'class' => 'text',
                        'width' => '5%',
                        'callback' => array(
                            'name' => 'countryIso'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'cardtype_id',
                        'orderclause' => 'cardtype.name',
                        'class' => 'text',
                        'width' => '5%',
                        'callback' => array(
                            'name' => 'cardtypeName'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'cardstatus_id',
                        'orderclause' => 'cardstatus.name',
                        'class' => 'text',
                        'width' => '10%',
                        'callback' => array(
                            'name' => 'cardstatusName'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'user_id',
                        'orderclause' => 'attorney.shortname',
                        'class' => 'text',
                        'width' => '5%',
                        'callback' => array(
                            'name' => 'attorneyName'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'teammashup',
                        'orderclause' => 'card.teammashup',
                        'class' => 'text',
                        'width' => '10%',
                        'callback' => array(
                            'name' => 'teamName'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'client_id',
                        'orderclause' => 'client.nickname',
                        'class' => 'text',
                        'width' => '20%',
                        'callback' => array(
                            'name' => 'clientNickname'
                        ),
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'title',
                        'orderclause' => 'card.title',
                        'class' => 'text',
                        'width' => '20%',
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'codeword',
                        'orderclause' => 'card.codeword',
                        'class' => 'text',
                        'width' => '15%',
                        'filter' => array(
                            'tag' => 'text'
                        )
                    ),
                    array(
                        'attribute' => 'feeinactive',
                        'orderclause' => 'card.feeinactive',
                        'class' => 'bool',
                        'width' => '5%',
                        'viewhelper' => 'boolperv',
                        'filter' => array(
                            'tag' => 'boolperv'
                        )
                    )
                );
        }
        return $ret;
    }

    /**
     * Return the month of fee due date.
     *
     * @return string
     */
    public function monthdue()
    {
        return date('m', strtotime($this->bean->applicationdate));
    }

    /**
     * Return the month name translated of fee due date.
     *
     * @return string
     */
    public function monthdueByName()
    {
        return __('month_label_' . date('m', strtotime($this->bean->applicationdate)));
    }

    /**
     * Returns a humanreadable status for a client.
     *
     * @return string
     */
    public function clientstatus()
    {
        if ($this->bean->cardstatus_id == 2) {
            return __('humanreadable_status_dead');
        }
        if ($this->bean->issuenumber || $this->bean->issuedate != '0000-00-00') {
            return __('humanreadable_status_issued');
        }
        if ($this->bean->applicationnumber || $this->bean->applicationdate != '0000-00-00') {
            return __('humanreadable_status_applied');
        }
    }

    /**
     * Returns either the codeword or if not set the title.
     *
     * @return string
     */
    public function codewordOrTitle()
    {
        if ($this->bean->codeword) {
            return $this->bean->codeword;
        }
        return $this->bean->title;
    }

    /**
     * Returns an empty string or the next fee due date if valid.
     *
     * @return string
     */
    public function getFeeduedate()
    {
        if ($this->bean->feeduedate == '' || $this->bean->feeduedate == '1970-01-01' ||
                $this->bean->feeduedate == '0000-00-00') {
            return '';
        }
        return $this->bean->feeduedate;
    }

    /**
     * Returns an array of the bean.
     *
     * @param bool $header defaults to false, if true then column headers are returned
     * @param string $layout
     * @return array
     */
    public function exportToCSV($header = false, $layout = 'default')
    {
        // layout "fitler", that is not a fixed layout, but the fields checked in a certain filter
        if ($layout == 'filter') {
			
			if (!isset($_SESSION['filter']['card']['id'])) {
	            if ($header == true) {
	                return array(
	                    __('card_label_card.name')

	                );
	            }
	            return array(
	                $this->bean->name
	            );
			} else {
				// we have a filter, lets puff it away
				$filter = R::load('filter', $_SESSION['filter']['card']['id']);
				$fields = $filter->withCondition('switch = 1 ORDER BY sequence')->ownCriteria;
	            if ($header == true) {
					$header = array();
					foreach ($fields as $field) {
						$header[] = __('card_label_' . $field->attribute);
					}
	                return $header;
	            }
				$data = array();
				$pdo = R::getDatabaseAdapter()->getDatabase()->getPDO();
				$pdo->setAttribute(PDO::ATTR_FETCH_TABLE_NAMES, true);
				$record = R::getRow($this->sqlForExportToCSV(), array($this->bean->getId()));
				$pdo->setAttribute(PDO::ATTR_FETCH_TABLE_NAMES, false);
				foreach ($fields as $field) {
					$data[] = $record[$field->attribute];
				}
	            return $data;
			}
        }

        // layout vrone
        if ($layout == 'vrone') {
            if ($header == true) {
                return array(
                    __('card_label_card.name'),
                    __('card_label_country'),
                    __('card_label_cardtype'),
                    __('card_label_applicationnumber'),
                    __('card_label_applicationdate_short'),
                    __('card_label_clientstatus'),
                    __('card_label_codewordortitle'),
                    __('card_label_feeduedate')

                );
            }
            return array(
                $this->bean->name,
                strtoupper($this->bean->country->iso),
                $this->bean->cardtype->name,
                $this->bean->applicationnumber,
                $this->bean->applicationdate,
                $this->bean->clientstatus(),
                str_replace('"', '', $this->bean->codewordOrTitle()),
                $this->bean->getFeeduedate()

            );
        }

        // layout vrtwo
        if ($layout == 'vrtwo') {
            if ($header == true) {
                return array(
                    __('card_label_card.name'),
                    __('card_label_country'),
                    __('card_label_cardtype'),
                    __('card_label_applicationnumber'),
                    __('card_label_applicationdate_short'),
                    __('card_label_clientstatus'),
                    __('card_label_issuenumber'),
                    __('card_label_issuedate_short'),
                    __('card_label_clientcode'),
                    __('card_label_title'),
                    __('card_label_codeword'),
                    __('card_label_note')
                );
            }
            return array(
                $this->bean->name,
                strtoupper($this->bean->country->iso),
                $this->bean->cardtype->name,
                $this->bean->applicationnumber,
                $this->bean->applicationdate,
                $this->bean->clientstatus(),
                $this->bean->issuenumber,
                $this->bean->issuedate,
                $this->bean->clientcode,
                str_replace('"', '', $this->bean->title),
                str_replace('"', '', $this->bean->codeword),
                str_replace('"', '', $this->bean->note)
            );
        }

        // layout default
        if ($layout == 'default') {
            if ($header === true) {
                return array(
                    __('card_label_card.name'),
                    __('card_label_country'),
                    __('card_label_cardtype'),
                    __('card_label_cardstatus'),
                    __('card_label_attorney'),
                    __('person_label_nickname'),
                    __('card_label_client'),
                    __('card_label_clientcode'),
                    __('card_label_title'),
                    __('card_label_codeword'),
                    __('card_label_note'),
                    __('card_label_applicationdate'),
                    __('card_label_applicationnumber'),
                    __('card_label_issuedate'),
                    __('card_label_issuenumber'),
                    __('card_label_disclosuredate'),
                    __('card_label_disclosurenumber')
                );
            }
            return array(
                $this->bean->name,
                $this->bean->country->name,
                $this->bean->cardtype->name,
                $this->bean->cardstatus->name,
                $this->bean->user->name,
                $this->bean->client()->nickname,
                $this->bean->client()->name,
                $this->bean->clientcode,
                $this->bean->title,
                $this->bean->codeword,
                $this->bean->note,
                $this->bean->applicationdate,
                $this->bean->applicationnumber,
                $this->bean->issuedate,
                $this->bean->issuenumber,
                $this->bean->disclosuredate,
                $this->bean->disclosurenumber
            );
        }
    }

    /**
     * Returns an array with possible layout for list view (index).
     *
     * @return array
     */
    public function layouts()
    {
        return array('table');
    }

    /**
     * Returns keywords from this bean for tagging.
     *
     * @var array
     */
    public function keywords()
    {
        //return array($this->bean->name);
        $keywords = array(
            $this->bean->name,
            $this->alphanumericonly($this->bean->name),
            $this->bean->clientnickname,
            $this->bean->clientcode,
            $this->alphanumericonly($this->bean->clientcode),
            $this->bean->invreceivernickname,
            $this->bean->invreceivercode,
            $this->alphanumericonly($this->bean->invreceivercode),
            $this->bean->applicantnickname,
            $this->bean->applicantcode,
            $this->alphanumericonly($this->bean->applicantcode),
            $this->bean->foreignnickname,
            $this->bean->foreigncode,
            $this->alphanumericonly($this->bean->foreigncode),
            $this->bean->applicationnumber,
            $this->bean->disclosurenumber,
            $this->bean->issuenumber,
            $this->alphanumericonly($this->bean->applicationnumber),
            $this->alphanumericonly($this->bean->disclosurenumber),
            $this->alphanumericonly($this->bean->issuenumber)
        );
        $keywords = array_merge($keywords, $this->splitToWords($this->bean->title));
        $keywords = array_merge($keywords, $this->splitToWords($this->bean->codeword));
        $keywords = array_merge($keywords, $this->splitToWords($this->bean->note));
        $keywords = array_merge($keywords, $this->splitToWords($this->bean->feesubject));
        //foreach ($this->bean->ownPriority as $id=>$priority) {
        //	$keywords = array_merge($keywords, $priority->keywords());
        //}
        return $keywords;
    }

    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        //$this->bean->setMeta('buildcommand.unique', array(array('name')));
        $this->setAutoInfo(true);
        $this->setAutoTag(true);
        $this->addValidator('name', 'hasvalue');

        //$this->addValidator('user_id', 'hasvalue');
        //$this->addValidator('country_id', 'hasvalue');
        //$this->addValidator('cardtype_id', 'hasvalue');
        //$this->addValidator('cardstatus_id', 'hasvalue');

        //$this->bean->feeinactive = false;

        $this->bean->patterncount = 0;

        $this->addValidator('name', 'isunique', array('bean' => $this->bean, 'attribute' => 'name'));
        $this->addValidator('name', 'pregmatch', array('regex' => "/\\d{2}[.]\\d{4}.?/"));
        $this->addConverter('applicationdate', 'mySQLDate');
        $this->addConverter('issuedate', 'mySQLDate');
        $this->addConverter('disclosuredate', 'mySQLDate');
        $this->addConverter('feeduedate', 'mySQLDate');
        if (! $this->bean->getId()) {
            $this->bean->feeinactive = 1;
        }
    }

    /**
     * Prepare this bean for duplication.
     *
     * Deletes related invoices and feesteps after duplication and
     * sets the annual fee details to inactive.
     */
    public function prepareForDuplication()
    {
        $this->bean->feeinactive = 1;
        $this->bean->ownInvoice = array();
        $this->bean->ownCardfeestep = array();
    }

    /**
     * Strip non alphanumeric characters from certain fields and make the card more easy to find.
     *
     * @uses alphanumericonly() to strip non alphanumeric characters from certain fields
     * @return void
     */
    public function makeBetterSearchable()
    {
        $this->bean->searchname = $this->alphanumericonly($this->bean->name);
        $this->bean->applicationnumberflat = $this->alphanumericonly($this->bean->applicationnumber);
        $this->bean->issuenumberflat = $this->alphanumericonly($this->bean->issuenumber);
        $this->bean->disclosurenumberflat = $this->alphanumericonly($this->bean->disclosurenumber);
        $this->bean->titleflat = $this->alphanumericonly($this->bean->title);
        $this->bean->codewordflat = $this->alphanumericonly($this->bean->codeword);
        $this->bean->noteflat = $this->alphanumericonly($this->bean->note);
        $this->bean->clientcodeflat = $this->alphanumericonly($this->bean->clientcode);
        $this->bean->applicantcodeflat = $this->alphanumericonly($this->bean->applicantcode);
        $this->bean->foreigncodeflat = $this->alphanumericonly($this->bean->foreigncode);
        $this->bean->invreceivercodeflat = $this->alphanumericonly($this->bean->invreceivercode);
    }

    /**
     * Open.
     */
    public function open()
    {
        $this->attrset = json_decode($this->bean->attrset, true);
    }

    /**
     * Update.
     *
     * @uses makeSortnumber() to build a sortable field based on the year part of card.name
     * @uses makeBetterSearchable() to derive a sortable name from the name
     */
    public function update()
    {
        if (isset($_POST['attrset'])) {
            $attrset = array_map('htmlentities', $_POST['attrset']);
            $this->bean->attrset = html_entity_decode(json_encode($attrset));
            //$this->bean->attrset = json_encode($_POST['attrset']);
        }
        $this->makeSortnumber();
        $this->makeBetterSearchable();
        if ($this->bean->feeinactive || ! $this->bean->getId()) {
            //unset($this->bean->pricetype);
            if ($this->bean->client()) {
                $this->bean->pricetype = $this->bean->client()->pricetype();
            } else {
                unset($this->bean->pricetype);
            }
            unset($this->bean->feetype);
        }
        if (! $this->bean->country_id) {
            $this->bean->country_id = null;
        }
        if (!$this->bean->originalname) {
            //$this->bean->original = null;
            $this->bean->original_id = null;
        }
        if (! $this->bean->client_id) {
            //$this->bean->client = null;
            $this->bean->client_id = null;
            $this->bean->clientnickname = null;
            $this->bean->clientaddress = null;
            $this->bean->clientcode = null;
        }
        if (! $this->bean->applicant_id) {
            //$this->bean->applicant = null;
            $this->bean->applicant_id = null;
            $this->bean->applicantnickname = null;
            $this->bean->applicantaddress = null;
            $this->bean->applicantcode = null;
        }
        if (! $this->bean->foreign_id) {
            //$this->bean->foreign = null;
            $this->bean->foreign_id = null;
            $this->bean->foreignnickname = null;
            $this->bean->foreignaddress = null;
            $this->bean->foreigncode = null;
        }
        if (! $this->bean->invreceiver_id) {
            //$this->bean->invreceiver = null;
            $this->bean->invreceiver_id = null;
            $this->bean->invreceivernickname = null;
            $this->bean->invreceiveraddress = null;
            $this->bean->invreceivercode = null;
        }
        if (! $this->bean->customeraccount || $this->bean->hasChanged('invreceiver_id') || $this->bean->hasChanged('client_id')) {
            if ($this->bean->invreceiver_id) {
                $this->bean->customeraccount = $this->bean->invreceiver()->account;
            } elseif ($this->bean->client_id) {
                $this->bean->customeraccount = $this->bean->client()->account;
            } else {
                $this->bean->customeraccount = '';
            }
        }
        $this->bean->sharedTeam = array();
        $this->bean->teammashup = '';
        foreach ($this->bean->user->sharedTeam as $id => $team) {
            $this->bean->teammashup .= $team->name . ' ';
            $this->bean->sharedTeam[] = $team;
        }
        $this->checkAndSetCurrentState();
        if ((strtolower($this->bean->cardtype->name) == 'marke' || strtolower($this->bean->cardtype->name) == 'gsm') && strtolower($this->bean->country->iso) == 'wo') {
            $this->addError('card_error_country_type_mismatch', 'country_id');
            if (self::VALIDATION_MODE_IMPLICIT === self::$validation_mode) {
                $this->invalid = true;
            } else {
                throw new Exception('card_error_update');
            }
        }
        parent::update();
    }

    /**
     * The card status is checked and set accordingly.
     *
     * The cardfeestep beans of this card will be checked and the card bean will
     * set it's status to either done, paid, ordered, awareness or maintain as
     * well as the feeduedate is updated. If the feestep is not marked as done and
     * the due date was made either paid, awareness or ordered and the date lies
     * behind today the card is marked as overdue.
     */
    public function checkAndSetCurrentState()
    {
        error_log('Check and set current state');
        $this->bean->overdue = false;
        $this->bean->status = 'inactive';
        if ($this->bean->feeinactive) {
            return false;
        }
        $this->bean->status = 'onhold';
        if ($this->bean->onhold) {
            return false;
        }
        if (! $rule = R::findOne('rule', ' country_id = ? AND cardtype_id = ? LIMIT 1', array($this->bean->country_id, $this->bean->cardtype_id))) {
            error_log('No rule set, just leave the card as it is');
            return false;
        }
        $this->bean->status = 'due';
        $was_already_set = false;
        foreach ($this->bean->ownCardfeestep as $id => $feestep) {
            if ($feestep->done) {
                error_log('Oh, '.$feestep->fy.' is already done...');
                $this->bean->status = 'done';
            } else {
                if (! $was_already_set) {
                    $was_already_set = true;
                    $this->bean->status = 'due';
                    //set next feeduedate to this feesteps fy.
                    $ts = strtotime($this->bean->applicationdate);
                    list($year, $month, $day) = array(date('Y', $ts), date('m', $ts), date('d', $ts));
                    $this->bean->feeduedate = date('Y-m-d', strtotime($feestep->fy.'-'.$month.'-'.$day));
                    error_log('and will next be due on '.$this->bean->feeduedate);

                    error_log($feestep->fy.' is pending and ...');
                    if ($feestep->paymentdate) {
                        $this->bean->status = 'paid'; //the DPMA was paided, all smile please
                        error_log('was paid');
                    } elseif ($feestep->invoicedate) {
                        $this->bean->status = 'billed'; //the customer was billed
                        error_log('was billed');
                    } elseif ($feestep->orderdate) {
                        $this->bean->status = 'ordered';
                        error_log('is called to order');
                    } elseif ($feestep->awarenessdate) {
                        $this->bean->status = 'awareness';
                        error_log('was made aware');
                    } else {
                        $this->bean->status = 'due';
                        error_log('is simply due');
                    }
                }
            }
        }
        if (! $was_already_set) {
            $this->bean->status = 'maintain';
            error_log('and somehow someone should check this one');
        }
        return true;
    }

    /**
     * Set a sortnumber from this beans name attribute.
     *
     * @uses $this->bean->name
     * @uses $this->bean->sortnumber
     * @return void
     */
    protected function makeSortnumber()
    {
        $tmp = explode('.', $this->bean->name);
        $sortnumber = $this->bean->name;
        if (isset($tmp[0])) {
            if ($tmp[0] >= 22) {
                $aYear = '19' . $tmp[0];
            } else {
                $aYear = '20' . $tmp[0];
            }
            $sortnumber = $aYear;
            if (isset($tmp[1])) {
                $sortnumber .= $tmp[1];
            }
        }
        $this->bean->sortnumber = $sortnumber;
    }
}
