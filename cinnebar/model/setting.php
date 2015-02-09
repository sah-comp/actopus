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
 * Manages setting.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Setting extends Cinnebar_Model
{
    /**
     * Returns a domain bean aliased as blessedfolder.
     *
     * @return RedBean_OODBean
     */
    public function blessedfolder()
    {
        if ( ! $this->bean->fetchAs('domain')->blessedfolder) $this->bean->blessedfolder = R::dispense('domain');
        return $this->bean->blessedfolder;
    }
    
    /**
     * Returns a pricetype bean aliased as feebase.
     *
     * @return RedBean_OODBean
     */
    public function feebase()
    {
        if ( ! $this->bean->fetchAs('pricetype')->feebase) $this->bean->feebase = R::dispense('pricetype');
        return $this->bean->feebase;
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
        				'attribute' => 'id',
        				'orderclause' => 'id',
        				'class' => 'number'
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
        return array();
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
     * update.
     */
    public function update()
    {
        if ($this->bean->loadexchangerates) {
            $now = time();
            if ( ! R::dispense('currency')->loadExchangeRates($now)) {
                throw new Exception('failed to load exchange rates');
            }
            $this->bean->tsexchangerate = $now;
        }
        parent::update();
    }

    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        $this->addValidator('blessedfolder', 'hasvalue');
        $this->addValidator('feebase', 'hasvalue');
        $this->setAutoInfo(true);
    }
}
