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
 * Manages currency.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Currency extends Cinnebar_Model
{
    /**
     * Load currency exchange rates.
     *
     * @param int $now is a unix timestamp of the last time the exchangerates have been synched
     * @return bool
     */
    public function loadExchangerates($now)
    {
        return $this->loadExchangerates_workhorse($now);
    }
    
    /**
     * Uses a service of the ECB to load currency exchange rates based on the EUR.
     *
     * For each currency on the exchange rate list a currency bean is looked up and updated by a
     * plain RedBean exec command (to bypass converters and validators). If a currency is not
     * found in the list of currency beans it will be skipped.
     *
     * The ECB has more information {@link http://www.ecb.int/stats/exchange/eurofxref/html/index.en.html}
     *
     * @global $config
     * @param int $now is a unix timestamp of the last time the exchangerates have been synched
     * @return bool
     */
    protected function loadExchangerates_workhorse($now)
    {
        global $config;
        if ( ! isset($config['currency']['exchangerates'])) return false;
        if ( ! $xml = simplexml_load_file($config['currency']['exchangerates'])) return false;
        $ret = true;
        foreach ($xml->Cube->Cube->Cube as $rate) {
            // do whatever stuff to store rate | currency into the database.
            // $rate['rate'] = decimal, $rate['currency'] = iso code
            if ( ! $currency = R::findOne('currency', ' iso = ? LIMIT 1', array($rate['currency']))) continue;
            try {
                $sql = 'UPDATE currency SET exchangerate = ? WHERE id = ?';
                R::exec($sql, array($rate['rate'], $currency->getId()));
            } catch (Exception $e) {
                Cinnebar_Logger::instance()->log('Failed to update currency exchange rate', 'sql');
                $ret = false;
            }
        }
        return $ret;
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
        				'attribute' => 'iso',
        				'orderclause' => 'iso',
        				'class' => 'text'
        			),
        			array(
        				'attribute' => 'name',
        				'orderclause' => 'name',
        				'class' => 'text'
        			),
        			array(
        				'attribute' => 'fractionalunit',
        				'orderclause' => 'fractionalunit',
        				'class' => 'text'
        			),
        			array(
        				'attribute' => 'numbertobasic',
        				'orderclause' => 'numbertobasic',
        				'class' => 'number'
        			),
        			array(
        				'attribute' => 'enabled',
        				'orderclause' => 'enabled',
        				'class' => 'bool',
        				'viewhelper' => 'bool'
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
            $this->bean->iso,
            $this->bean->name
        );
    }
    
    /**
     * update.
     *
     * Makes sure that exchangerate will be stored as double.
     */
    public function update()
    {
        $this->bean->setMeta('cast.exchangerate', 'double')->exchangerate = $this->bean->exchangerate;
        parent::update();
    }

    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        //$this->bean->setMeta('buildcommand.unique', array(array('iso')));
        $this->setAutoInfo(true);
        $this->addValidator('iso', 'hasvalue');
        $this->addValidator('iso', 'isunique', array('bean' => $this->bean, 'attribute' => 'iso'));
        $this->addValidator('name', 'hasvalue');
        $this->addConverter('exchangerate', 'decimal');
    }
}
