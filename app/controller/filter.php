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
 * Manages CURD on filter beans.
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_Filter extends Controller_Scaffold
{
    /**
     * Holds the bean type to apply scaffolding to.
     *
     * @var string
     */
    public $type = 'filter';

	/**
	 * Dispenses a blank Bean as either own or shared and outputs the template.
	 *
	 * @uses controller() to fetch the calling controller
	 * @param string $prefix
	 * @param string $type
	 * @param mixed (optional) $id
	 * @return void
	 */
	public function addcriteria($prefix, $type, $id = 0)
	{
        session_start();
        $this->cache()->deactivate();

		//$n = md5(microtime(true));
		$n = rand();
        $filter = R::load('filter', $id);
        $this->view = $this->makeView(sprintf('model/%s/form/%s/%s', $this->type, $prefix, $type));
        //find first attribute not used and set up criteria
        $this->view->record = R::dispense($filter->model);
        $record = R::dispense($type);
        $attribute = $this->firstAttributeOfModel($this->view->record);
        $record->attribute = $attribute['orderclause'];
        if (isset($attribute['filter']['orderclause'])) {
            $record->attribute = $attribute['filter']['orderclause'];
        }
        $record->tag = $attribute['filter']['tag'];
        $this->view->$type = $record;
        $this->view->n = $n;
        //$this->trigger('edit', 'before');
        echo $this->view->render();
		return true;
	}
	

    /**
     * Updates a criteria.
     *
     * This is called from a ajax post request.
     *
     * @param int (optional) $n index of the criteria
     * @param string (optional) $attribute of the criteria
     * @param string (optional) $model of the filter
     * @return void
     */
    public function updcriteria($n = null, $attr_name = null, $model = null)
    {
        session_start();
        $this->cache()->deactivate();
        $this->view = $this->makeView(null);
        $criteria = R::load('criteria', $n);
        $this->view->record = R::dispense($model);
        //find attribute and set it up
        $attribute = $this->findAttributeByName($this->view->record, $attr_name);
        $criteria->attribute = $attribute['orderclause'];
        if (isset($attribute['filter']['orderclause'])) {
            $criteria->attribute = $attribute['filter']['orderclause'];
        }
        $criteria->tag = $attribute['filter']['tag'];
        //$filter = $criteria->filter;
        $this->view->criteria = $criteria;
        $this->view->n = $n;
        echo $this->view->partial('model/filter/form/own/innercriteriawrapper');
        return;
    }
    
    /**
     * Finds and returns the attribute of the given model by name.
     *
     * @param RedBean_OODBBean $bean
     * @param string $name of the attribute to look up
     * @return array
     */
    public function findAttributeByName(RedBean_OODBBean $bean, $attr_name = '')
    {
        $attributes = $bean->attributes('report');
        foreach ($attributes as $attribute) {
            if ((isset($attribute['filter']['orderclause']) && $attribute['filter']['orderclause'] == $attr_name) || $attribute['orderclause'] == $attr_name) {
                return $attribute;
            }
        }
        return array();
    }
    
    /**
     * Returns the first attribute of the given model by name.
     *
     * @param RedBean_OODBBean $bean
     * @return array
     */
    public function firstAttributeOfModel(RedBean_OODBBean $bean)
    {
        $attributes = $bean->attributes('report');
        return reset($attributes);
    }
}
