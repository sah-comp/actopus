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
 * Manages rule.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Rule extends Cinnebar_Model
{
    /**
     * Returns an array of cardfeestep beans.
     *
     * There are two subtypes of rule where the 0 = "limit" subtype will generate a set of
     * annualstep beans according to the rulesteps of this rule. setup will create as many
     * annualsteps until there are no more rulesteps or until the next due step.
     *
     * The second subtype 1 = "perpetual" will generate as many annualsteps until the next due
     * annualstep is reached.
     *
     * @uses workhorse_setup_timeframe() if the subtype of this rule is timeframe
     * @uses workhorse_setup_repeatable() if the subtype of this rule is repeatable
     *
     * @param RedBean_OODBBean $card an instance of a card bean
     * @param RedBean_OODBBean (optional) $fee
     * @param RedBean_OODBBean (optional) $feebase
     * @return array
     */
    public function setupCard(RedBean_OODBbean $card, RedBean_OODBbean $fee = null, RedBean_OODBbean $feebase = null)
    {
        if ( $this->bean->style == 0) return $this->setup_limit_workhorse($card, $fee, $feebase);
        if ( $this->bean->style == 1) return $this->setup_perpetual_workhorse($card, $fee, $feebase);
        return array();
    }
    
    /**
     * Returns an array of cardfeestep beans.
     *
     * The array is generated according to the rule and the fee beans which are related by the
     * card and rule. The array may be empty.
     *
     * @todo next due date calculation??
     *
     * @param RedBean_OODBBean $card
     * @param RedBean_OODBBean (optional) $fee
     * @param RedBean_OODBBean (optional) $feebase
     * @return array
     */
    protected function setup_limit_workhorse(RedBean_OODBBean $card, RedBean_OODBbean $fee = null, RedBean_OODBbean $feebase = null)
    {
        if ( ! $card->applicationdate) return array();
        if ( ! $rulesteps = $this->own('rulestep', false)) return array();
        // there are steps, there is a date: lets setup a timeframe for this jolly
        $current_date = time();
        list($fy_app_date, $fm_app_date, $fd_app_date) = $this->split_date($card->applicationdate);
        $cardfeesteps = array();
        $next_due_date = null;
        $i = 0;
        foreach ($rulesteps as $id => $rulestep) {
            if ( ! $feestep = R::findOne('feestep', ' fee_id = ? AND rulestep_id = ? LIMIT 1', array($fee->getId(), $rulestep->getId()))) {
                // wooah, there is no feestep
                $feestep = R::dispense('feestep');
            }
            if ( ! $feestepbase = R::findOne('feestep', ' fee_id = ? AND rulestep_id = ? LIMIT 1', array($feebase->getId(), $rulestep->getId()))) {
                // wooah, there is no feestep
                $feestepbase = R::dispense('feestep');
            }
            $cardfeestep = R::dispense('cardfeestep');
            $i++;
            $cardfeestep->sequence = $i;
            // start building it
            //$cardfeestep->rulestep = $rulestep;
            //$cardfeestep->feestep = $feestep;
            // make the year
            $cardfeestep->fy = $fy_app_date + $rulestep->offset - 1;
            // and date
            $due_date = $this->make_due_date($cardfeestep->fy, $fm_app_date, $fd_app_date);
            $cardfeestep->duedate = date('Y-m-d', $due_date);

            $cardfeestep->net = $feestep->net;
            $cardfeestep->additional = $feestep->additional;
            $cardfeestep->paymentnet = $feestepbase->net;
            if ($fee->multiplier == 'patterncount') {
                $cardfeestep->net = $card->{$fee->multiplier} * $feestep->net;
                $cardfeestep->additional = $card->{$fee->multiplier} * $feestep->additional;
                $cardfeestep->paymentnet = $card->{$fee->multiplier} * $feestepbase->net;
            }
            
            // end building it
            
            // check if that due date is beyond current date
            if ( $due_date >= $current_date ) {
                $cardfeestep->done = false;
                if ( ! $next_due_date) $next_due_date = $due_date;
            } else {
                $cardfeestep->done = true; // does this reflect?
            }
            // push that step to our stack
            $cardfeesteps[] = $cardfeestep;
        }
        $card->feeduedate = date('Y-m-d', $next_due_date);
        try {
            $card->setAutoInfo(false);
            $card->setAutoTAg(false);
            $card->ownCardfeestep = $cardfeesteps;
            R::store($card);
        } catch (Exception $e) {
            error_log($e);
        }
        return $cardfeesteps;
    }
    
    /**
     * Returns an array of cardfeestep beans.
     *
     * The array is generated according to the rule and the fee beans which are related by the
     * card and rule. The array may be empty.
     *
     * @todo next due date calculation??
     *
     * @param RedBean_OODBBean $card
     * @param RedBean_OODBBean (optional) $fee
     * @param RedBean_OODBBean (optional) $feebase
     * @return array
     */
    protected function setup_perpetual_workhorse(RedBean_OODBBean $card, RedBean_OODBbean $fee = null, RedBean_OODBbean $feebase = null)
    {
        if ( ! $card->applicationdate) return array();
        $current_date = time();
        list($fy_app_date, $fm_app_date, $fd_app_date) = $this->split_date($card->applicationdate);
        $cardfeesteps = array();
        $i = 0;
        $due_date = null;
        $finished = false;
        do {
            $cardfeestep = R::dispense('cardfeestep');
            $i++;
            $cardfeestep->sequence = $i;
            // start building it
            //$cardfeestep->rulestep = $rulestep;
            //$cardfeestep->feestep = $feestep;
            // make the year
            // and date
            $due_date = $this->make_due_date($fy_app_date + ($i * $this->bean->period), $fm_app_date, $fd_app_date);
            $cardfeestep->fy = date('Y', $due_date);
            $cardfeestep->duedate = date('Y-m-d', $due_date);
            
            
            
            $cardfeestep->net = $fee->netincluded;
            $cardfeestep->additional = $fee->additionalincluded;
            
            $cardfeestep->paymentnet = $feebase->netincluded;
            
            if ($fee->multiplier == 'patterncount' && ($card->{$fee->multiplier} > $fee->included)) {
                $cardfeestep->net += ($card->{$fee->multiplier} - $fee->included) * $fee->netexcluded;
                $cardfeestep->additional += ($card->{$fee->multiplier} - $fee->included) * $fee->additionalexcluded;
                $cardfeestep->paymentnet += ($card->{$feebase->multiplier} - $feebase->included) * $feebase->netexcluded;
            }
            
            // end building it
            
            // check if that due date is beyond current date
            if ( $due_date >= $current_date ) {
                $cardfeestep->done = false;
                $finished = true;
            } else {
                $cardfeestep->done = true; // does this reflect?
            }
            // push that step to our stack
            $cardfeesteps[] = $cardfeestep;
        } while ( ! $finished);
        $card->feeduedate = date('Y-m-d', $due_date);
        try {
            $card->setAutoInfo(false);
            $card->setAutoTAg(false);
            $card->ownCardfeestep = $cardfeesteps;
            R::store($card);
        } catch (Exception $e) {
            error_log($e);
        }
        return $cardfeesteps;
    }

    
    /**
     * Returns an array with year, month and day of an date.
     *
     * @param string $mysqlDateString e.g. 2012-03-23
     * @return array
     */
    protected function split_date($date)
    {
        $ts = strtotime($date);
        return array(date('Y', $ts), date('m', $ts), date('d', $ts));
    }
    
    /**
     * Returns a UNIX_TIMESTAMP for the given date according to the rule.
     *
     * If the rule has the attribute last_day set and true, the given day will be mutant
     * to the last day of the month.
     *
     * @todo reimplement flag for "due at last day of month" in a rule
     *
     * @param int $year
     * @param int $month
     * @param int $day
     * @return int $unixtimestamp
     */
    protected function make_due_date($year, $month, $day)
    {
        $ts = strtotime(sprintf('%s-%s-%s', $year, $month, $day));
        /*
        if ($this->bean->lastdayofmonth) {
            $last_day = date('t', $ts);
            $ts = strtotime(sprintf('%s-%s-%s', $year, $month, $last_day));
        }
        */
        return $ts;
    }

    /**
     * Returns a string with a printable name of the rule.
     *
     * The name consists of the country name plus the cardtype name.
     *
     * @return string
     */
    public function displayName()
    {
        return $this->countryName().' '.$this->cardtypeName();
    }

    /**
     * Returns rulestep beans.
     *
     * @param bool $add if true an empty records gets added.
     * @return array
     */
    public function getownRulestep($add)
    {
        $own = R::find('rulestep', ' rule_id = ? ORDER BY sequence, offset', array($this->bean->getId()));
        if ($add) $own[] = R::dispense('rulestep');
        return $own;
    }
    
    /**
     * Returns a country bean.
     *
     * @return RedBean_OODBBean
     */
    public function country()
    {
        if ( ! $this->bean->country) $this->bean->country = R::dispense('country');
        return $this->bean->country;
    }
    
    /**
     * Returns a country name.
     *
     * @return RedBean_OODBBean
     */
    public function countryName()
    {
        return '<span class="flag '.$this->bean->country()->iso.'"></span>'.$this->bean->country()->name;
    }
    
    /**
     * Returns a country iso.
     *
     * @return RedBean_OODBBean
     */
    public function countryIso()
    {
        return '<span title="'.$this->bean->country()->name.'" class="flag '.$this->bean->country()->iso.'"></span>'.strtoupper($this->bean->country()->iso);
    }
    
    /**
     * Returns a cardtype bean.
     *
     * @return RedBean_OODBBean
     */
    public function cardtype()
    {
        if ( ! $this->bean->cardtype) $this->bean->cardtype = R::dispense('cardtype');
        return $this->bean->cardtype;
    }
    
    /**
     * Returns a cardtype name.
     *
     * @return RedBean_OODBBean
     */
    public function cardtypeName()
    {
        return $this->bean->cardtype()->name;
    }
    
    /**
     * Returns SQL for filtering these beans.
     *
     * @uses R
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
			DISTINCT(rule.id) as id  

		FROM
			rule

		LEFT JOIN country ON country.id = rule.country_id
		LEFT JOIN cardtype ON cardtype.id = rule.cardtype_id

		WHERE {$where_clause}

		ORDER BY {$order_clause}

		LIMIT {$offset}, {$limit}
SQL;
        return $sql;
    }

    /**
     * Returns SQL for total.
     *
     * @uses R
     * @param string $where_clause
     * @return string $SQL
     */
    public function sqlForTotal($where_clause = '1')
    {
		$sql = <<<SQL
		SELECT
			COUNT(DISTINCT(rule.id)) as total

		FROM
			rule

		LEFT JOIN country ON country.id = rule.country_id
		LEFT JOIN cardtype ON cardtype.id = rule.cardtype_id

		WHERE {$where_clause}
SQL;
        return $sql;
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
	        default:
        		$ret = array(
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
        			    'attribute' => 'description',
        			    'orderclause' => 'rule.description',
        			    'class' => 'text',
        				'filter' => array(
        				    'tag' => 'text'
        				)
        			)
        		);
        }
        return $ret;
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
        return array(
        );
    }

    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        $this->setAutoInfo(true);
        //$this->bean->setMeta('buildcommand.unique', array(array('name')));
        $this->addValidator('name', 'hasvalue');
        $this->addValidator('name', 'isunique', array('bean' => $this->bean, 'attribute' => 'name'));
    }
    
    /**
     * update.
     */
    public function update()
    {
        $this->bean->name = $this->bean->country()->getId().'-'.$this->bean->cardtype()->getId();
        parent::update();
    }
}
