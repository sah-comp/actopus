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
 * Manages queues.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Queue extends Cinnebar_Model
{
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
			DISTINCT(queue.id) as id  

		FROM
			queue

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
        return array('table');
	}
	
	/**
	 * Marks the the queue as opened.
	 */
	public function wasOpened()
	{
        if ( ! $this->bean->open) {
            $this->bean->open = time();
            $this->bean->counter = 0;
        }
        $this->bean->counter++;
	}
	
	/**
	 * update.
	 */
	public function update()
	{
	    $this->bean->emailhash = md5($this->bean->email);
        if ( ! $this->bean->getId()) {
            $this->bean->hash = md5($this->bean->email.time());
        }
	}

    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        $this->bean->sent = null;
        $this->bean->open = null;
        $this->bean->error = false;
    }
}
