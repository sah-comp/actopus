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
 * The filter model manages filters you may set on beans.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Filter extends Cinnebar_Model
{
    /**
     * Container for values that are collected on building a where clause.
     *
     * @var array
     */
    public $filter_values = array();
    
    /**
     * Returns wether the attributes array has a key named filter or not.
     *
     * @param array $attributes
     * @return bool
     */
    public function hasFilter(array $attributes)
    {
        foreach ($attributes as $attribute) {
            if (isset($attribute['filter']) && is_array($attribute['filter'])) return true;
        }
        return false;
    }
    
    /**
     * Returns a SQL WHERE clause for usage with another bean.
     *
     * @uses Model_Criteria::makeWherePart() to generate the SQL for a criteria
     * @return string $WhereClauseForSQL
     */
    public function buildWhereClause()
    {
        $criterias = $this->bean->ownCriteria;
        
        if (empty($criterias)) return '1';// find all because there are no criterias
        
    	$where = array();
    	$this->filter_values = array();
    	//$mask = " %s %s %s"; // login, field, op (with masked value)
    	
    	$n = 0;
    	foreach ($criterias as $id=>$criteria) {
    	    if ( ! $criteria->op) continue; // skip all entries that say any!
            if ( $criteria->value === null || $criteria->value === '') continue; // skip all empty
    		$n++;
    		$logic = $this->bean->logic . ' ';
    		if ($n == 1) $logic = '';
    		$where[] = $logic.$criteria->makeWherePart($this);
    	}
    	
    	if (empty($where)) return '1';// find all because there was no active criteria
    	
    	$where = implode(' ', $where);
    	return $where;
    }
    
    /**
     * Returns an array with values that were collected as the where clause was build.
     *
     * @return array
     */
    public function filterValues()
    {
        return $this->filter_values;
    }
    
    /**
     * Masks the criterias value and stacks it into the filter values.
     *
     * @uses $filter_values
     * @param RedBean_OODBBean $criteria
     * @return void
     */
    protected function dep_mask_filter_value(RedBean_OODBBean $criteria)
    {
        $add_to_filter_values = true;
    	switch ($criteria->op) {
    		case 'like':
    			$value = '%'.str_replace($this->pat, $this->rep, $criteria->value).'%';
    			break;
    		case 'notlike':
    			$value = '%'.str_replace($this->pat, $this->rep, $criteria->value).'%';
    			break;
    		case 'bw':
    			$value = str_replace($this->pat, $this->rep, $criteria->value).'%';
    			break;
    		case 'ew':
    			$value = '%'.str_replace($this->pat, $this->rep, $criteria->value);
    			break;
    		case 'in':
    		    $_sharedSubName = 'shared'.ucfirst(strtolower($criteria->substitute));
    		    $ids = array_keys($criteria->{$_sharedSubName});
    		    $value = 'IN ('.implode(', ', $ids).')';
    		    $add_to_filter_values = false;
    		    break;
    		default:
    			$value = $criteria->value;
    	}
    	if ($add_to_filter_values) {
            if ($criteria->tag == 'date') {
                $value = date('Y-m-d', strtotime($criteria->value));
            }
    	    $this->filter_values[] = $value;
    	}
    	return true;
    }
    
    /**
     * Returns array of filter criterias.
     *
     * @return array
     */
    public function criterias()
    {
        /*
        if ( ! $this->bean->ownCriteria) {
            $model = R::dispense($this->bean->model);
            $preset = $model->filters();
            foreach ($preset as $n=>$attr) {
                 $li = R::dispense('criteria');
                 $li->import($attr);
                 $this->bean->ownCriteria[] = $li;
             }
        }
        */
        return $this->bean->ownCriteria;
    }
    
    /**
     * Returns a criteria bean for a certain filter attribute.
     *
     * @param array $attribute
     * @return RedBean_OODBBean
     */
    public function getCriteria(array $attribute)
    {
        $attrName = isset($attribute['filter']['orderclause']) ? $attribute['filter']['orderclause']: $attribute['orderclause'];
        if ( ! $criteria = R::findOne('criteria', ' filter_id = ? AND attribute = ? LIMIT 1', array($this->bean->getId(), $attrName))) {
            $criteria = R::dispense('criteria');
            $criteria->tag = $attribute['filter']['tag'];
            $criteria->attribute = $attrName;
            $operators = $criteria->operators();
            $criteria->op = $operators[0];
        }
        return $criteria;
    }
    
    /**
     * Returns an array with order clause option of the filtered bean type.
     *
     * @return array $orderClauses
     */
    public function deprecated_orderClauses()
    {
        $filtered_bean = R::dispense($this->bean->model);
        return $filtered_bean->orderClauses();
    }
    
    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        $this->addValidator('model', 'hasvalue');
    }
}