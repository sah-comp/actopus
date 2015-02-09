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
 * Manages todos.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Todo extends Cinnebar_Model
{
    /**
     * Return ownTodo(s).
     *
     * @param bool $add if true an empty records gets added.
     * @return array
     */
    public function getownTodo($add)
    {
        $own = R::find('todo', ' todo_id = ? ORDER BY sequence, name', array($this->bean->getId()));
        if ($add) $own[] = R::dispense('todo');
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
			DISTINCT(todo.id) as id  

		FROM
			todo

		WHERE {$where_clause} AND todo_id is NULL

		ORDER BY {$order_clause}

		LIMIT {$offset}, {$limit}
SQL;
        return $sql;
    }
    
    /**
     * Returns SQL to get the total number of beans.
     *
     * @uses R
     * @param string $where_clause
     * @return string $SQL
     */
    public function sqlForTotal($where_clause = '1')
    {
		$sql = <<<SQL
		SELECT
			COUNT(DISTINCT(todo.id)) as total

		FROM
			todo

		WHERE {$where_clause} AND todo_id is NULL
		
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
        				'orderclause' => 'todo.name',
        				'class' => 'text'
        			),
        			array(
        				'attribute' => 'finished',
        				'orderclause' => 'todo.finished',
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
            $this->bean->name
        );
    }

    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        $this->setAutoTag(true);
        $this->setAutoInfo(true);
        $this->addValidator('name', 'hasvalue');
        $this->sequence = 0;
    }
}
