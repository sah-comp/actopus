<?php<?php
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
 * The criteria model belongs to a filter bean and lets you filter other beans.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Criteria extends Cinnebar_Model
{
    /**
     * Container for the map of search operators.
     *
     * @var array
     */
    public $map = array(
        'like' => '%1$s like ?',
        'notlike' => '%1$s not like ?',
        'eq' => '%1$s = ?',
        'neq' => '%1$s != ?',
        'bw' => '%1$s like ?',
        'ew' => '%1$s like ?',
        'lt' => '%1$s < ?',
        'gt' => '%1$s > ?',
        'in' => '%1$s in (%2$s)'
        //'between' => __('filter_op_between'),
        //'istrue' => __('filter_op_istrue'),
        //'isfalse' => __('filter_op_isfalse')
    );

    /**
     * Holds possible search operators depending on the tag type.
     *
     * @var array
     */
    public $operators = array(
         'text' => array('like', 'ew', 'eq', 'neq', 'bw', 'notlike'),
         'number' => array('eq', 'gt', 'lt', 'neq'),
         'date' => array('eq', 'gt', 'lt', 'neq'),
         'time' => array('eq', 'gt', 'lt', 'neq'),
         'email' => array('bw', 'ew', 'eq', 'neq', 'like', 'notlike'),
         'textarea' => array('bw', 'ew', 'eq', 'neq', 'like', 'notlike'),
         'in' => array('in'),
         'select' => array('eq'),
         'bool' => array('eq'),
         'boolperv' => array('eq'),
         'chooser' => array('eq')
     );

    /**
     * Container for characters that have to be escaped for usage with SQL.
     *
     * @var array
     */
    public $pat = array('%', '_');

    /**
     * Container for escaped charaters.
     *
     * @var array
     */
    public $rep = array('\%', '\_');

    /**
     * Returns a string to use as part of a SQL query.
     *
     * @throws an exception when criteria operator has no template definded in map
     * @uses $map
     * @uses mask_filter_value()
     * @param Model_Filter $filter
     * @return string
     */
    public function makeWherePart(Model_Filter $filter)
    {
        if (! isset($this->map[$this->bean->op])) {
            throw new Exception('Filter operator has no template');
        }
        $template = $this->map[$this->bean->op];
        $value = $this->mask_filter_value($filter);
        return sprintf($template, $this->bean->attribute, $value);
    }

    /**
     * Masks the criterias value and stacks it into the filter values.
     *
     * @uses Model_Filter::$filter_values where the values of our criterias are stacked up
     * @param Model_Filter $filter
     * @return void
     */
    protected function mask_filter_value(Model_Filter $filter)
    {
        $add_to_filter_values = true;
        switch ($this->bean->op) {
            case 'like':
                $value = '%'.str_replace($this->pat, $this->rep, $this->bean->value).'%';
                break;
            case 'notlike':
                $value = '%'.str_replace($this->pat, $this->rep, $this->bean->value).'%';
                break;
            case 'bw':
                $value = str_replace($this->pat, $this->rep, $this->bean->value).'%';
                break;
            case 'ew':
                $value = '%'.str_replace($this->pat, $this->rep, $this->bean->value);
                break;
            case 'in':
                $_sharedSubName = 'shared'.ucfirst(strtolower($this->bean->substitute));
                $ids = array_keys($this->bean->{$_sharedSubName});
                $value = implode(', ', $ids);
                $add_to_filter_values = false;
                break;
            default:
                $value = $this->bean->value;
        }
        if ($add_to_filter_values) {
            if ($this->bean->tag == 'date') {
                $value = date('Y-m-d', strtotime($value));
            }
            $filter->filter_values[] = $value;
        }
        return $value;
    }

    /**
     * Returns array with possible operators for the given tag type.
     *
     * @return array $operators
     */
    public function operators()
    {
        if (isset($this->operators[$this->bean->tag])) {
            return $this->operators[$this->bean->tag];
        }
        return array();
    }

    /**
     * Returns array with possible operators for the given type.
     *
     * @param string $type
     * @return array $operators
     */
    public function getOperators($type = 'text')
    {
        if (isset($this->operators[$type])) {
            return $this->operators[$type];
        }
        return array();
    }

    /**
     * Setup validators.
     */
    public function dispense()
    {
        $this->addValidator('attribute', 'hasvalue');
    }
}
