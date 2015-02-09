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
 * Manages templates.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Template extends Cinnebar_Model
{
    /**
     * Returns this templates regions.
     *
     * @return array of region beans
     * @throws exception when a default region could not be stored
     */
    public function regions()
    {
        $regions = $this->bean->getownRegion(false);
        if ( ! empty($regions)) return $regions;
        // no region is this template, make a default one
        $region = R::dispense('region');
        $region->name = 'default';
        $region->sequence = 0;
        $this->bean->ownRegion[] = $region;
        try {
            R::store($this->bean);
        } catch (Exception $e) {
            throw new Exception('Model_Template failed to make a default region '.$e);
        }
        return $this->bean->regions();
    }

    /**
     * Return ownRegion(s).
     *
     * @param bool $add if true an empty records gets added.
     * @return array
     */
    public function getownRegion($add)
    {
        $own = R::find('region', ' template_id = ? ORDER BY sequence', array($this->bean->getId()));
        if ($add) $own[] = R::dispense('region');
        return $own;
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
			DISTINCT(template.id) as id  

		FROM
			template
			
		WHERE {$where_clause}

		ORDER BY {$order_clause}

		LIMIT {$offset}, {$limit}
SQL;
        return $sql;
    }
    
    /**
     * Returns SQL for fetch the total of all beans.
     *
     * @todo get rid of global language code
     *
     * @uses R
     * @param string $where_clause
     * @return string $SQL
     */
    public function sqlForTotal($where_clause = '1')
    {
		$sql = <<<SQL
		SELECT
			COUNT(DISTINCT(template.id)) as total
		FROM
			template

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
        				'attribute' => 'name',
        				'orderclause' => 'template.name',
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
	 * Returns a customized menu.
	 *
	 * @param string $action
	 * @param Cinnebar_View $view
 	 * @param Cinnebar_Menu (optional) $menu
 	 * @return Cinnebar_Menu
 	 */
 	public function makeMenu($action, Cinnebar_View $view, Cinnebar_Menu $menu = null)
	{
        $menu = parent::makeMenu($action, $view, $menu);
        return $menu;
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
        //$this->bean->setMeta('buildcommand.unique', array(array('name')));
        $this->setAutoInfo(true);
        $this->addValidator('name', 'hasvalue');
        $this->addValidator('name', 'isunique', array('bean' => $this->bean, 'attribute' => 'name'));
    }
}
