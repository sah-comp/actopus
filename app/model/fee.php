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
 * Manages fee.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Fee extends Cinnebar_Model
{
    /**
     * Returns a feestep bean.
     *
     * @param int $rulestep_id id of the rulestep used
     * @return RedBean_OODBBean
     */
    public function getFeestep($rulestep_id)
    {
        $rulestep = R::load('rulestep', $rulestep_id);
        if ( ! $feestep = R::findOne('feestep', ' fee_id = ? AND rulestep_id = ? LIMIT 1', array($this->bean->getId(), $rulestep_id))) {
            $feestep = R::dispense('feestep');
            $feestep->rulestep = $rulestep;
        }
        return $feestep;
    }

    /**
     * Returns feestep beans.
     *
     * @param bool $add if true an empty records gets added.
     * @return array
     */
    public function getownFeestep($add)
    {
        $own = R::find('feestep', ' fee_id = ?', array($this->bean->getId()));
        if ($add) $own[] = R::dispense('feestep');
        return $own;
    }

    /**
     * Returns a rule bean.
     *
     * @return RedBean_OODBBean
     */
    public function rule()
    {
        if ( ! $this->bean->rule) $this->bean->rule = R::dispense('rule');
        return $this->bean->rule;
    }
    
    /**
     * Returns a country bean.
     *
     * @return RedBean_OODBBean
     */
    public function country()
    {
        if ( ! $this->bean->rule()->country) $this->bean->rule()->country = R::dispense('country');
        return $this->bean->rule()->country;
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
        if ( ! $this->bean->rule()->cardtype) $this->bean->rule()->cardtype = R::dispense('cardtype');
        return $this->bean->rule()->cardtype;
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
     * Returns a pricetype bean.
     *
     * @return RedBean_OODBBean
     */
    public function pricetype()
    {
        if ( ! $this->bean->pricetype) $this->bean->pricetype = R::dispense('pricetype');
        return $this->bean->pricetype;
    }

    /**
     * Returns a pricetype name.
     *
     * @return RedBean_OODBBean
     */
    public function pricetypeName()
    {
        return $this->bean->pricetype()->name;
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
			DISTINCT(fee.id) as id  

		FROM
			fee

        LEFT JOIN rule ON rule.id = fee.rule_id
		LEFT JOIN country ON country.id = rule.country_id
		LEFT JOIN cardtype ON cardtype.id = rule.cardtype_id
		LEFT JOIN pricetype ON pricetype.id = fee.pricetype_id

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
			COUNT(DISTINCT(fee.id)) as total

		FROM
			fee

        LEFT JOIN rule ON rule.id = fee.rule_id
		LEFT JOIN country ON country.id = rule.country_id
		LEFT JOIN cardtype ON cardtype.id = rule.cardtype_id
		LEFT JOIN pricetype ON pricetype.id = fee.pricetype_id

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
        			    'attribute' => 'pricetype_id',
        			    'orderclause' => 'pricetype.name',
        			    'class' => 'text',
        			    'callback' => array(
        			        'name' => 'pricetypeName'
        			    ),
        				'filter' => array(
        				    'tag' => 'text'
        				)
        			),
        			array(
        			    'attribute' => 'description',
        			    'orderclause' => 'fee.description',
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
        
        $this->addConverter('netincluded', 'decimal');
        $this->addConverter('additionalincluded', 'decimal');
        $this->addConverter('netexcluded', 'decimal');
        $this->addConverter('additionalexcluded', 'decimal');
    }
    
    /**
     * update.
     */
    public function update()
    {
        $this->bean->name = $this->bean->rule()->getId().'-'.$this->bean->pricetype()->getId();
        parent::update();
    }
}
