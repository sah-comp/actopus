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
 * Manages countries.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Country extends Cinnebar_Model
{
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
                    country.id AS id,
                    country.name AS label,
                    country.iso AS iso

                FROM
                    country

                WHERE
                    name like ?

                ORDER BY
                    name

SQL;
        }
        return $res = R::getAll($sql, array($term.'%'));
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
			DISTINCT(country.id) as id  

		FROM
			country

		WHERE {$where_clause}

		ORDER BY {$order_clause}

		LIMIT {$offset}, {$limit}
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
            $this->bean->iso,
            $this->bean->name
        );
    }

    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        //$this->bean->setMeta('buildcommand.unique', array(array('iso')));
        //$this->setAutoTag(true);
        $this->addValidator('iso', 'hasvalue');
        $this->addValidator('iso', 'isunique', array('bean' => $this->bean, 'attribute' => 'iso'));
    }
}
