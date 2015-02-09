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
 * Manages campaigns.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Campaign extends Cinnebar_Model
{
    
    /**
     * Return ownAttribute(s).
     *
     * @param bool $add if true an empty records gets added.
     * @return array
     */
    public function getownAttribute($add)
    {
        $own = R::find('attribute', ' campaign_id = ? ORDER BY sequence, name', array($this->bean->getId()));
        if ($add) $own[] = R::dispense('attribute');
        return $own;
    }

    /**
     * Returns an array of optin beans.
     *
     * @param bool (optional) $enabled defaults to true
     * @return array $articles
     */
    public function getOptins($enabled = true)
    {
		$sql = <<<SQL
		SELECT
        	DISTINCT(optin.id) as id  

        FROM
        	campaign_optin AS receiver

        LEFT JOIN
            optin ON optin.id = receiver.optin_id

        WHERE
            receiver.campaign_id = ? AND
            optin.enabled = ?

        ORDER BY optin.email
SQL;
        //R::debug(true);
        $assoc = R::$adapter->getAssoc($sql, array($this->bean->getId(), $enabled));
        //R::debug(false);
        return R::batch('optin', array_keys($assoc));
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
			DISTINCT(campaign.id) as id  

		FROM
			campaign

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
        				'attribute' => 'name',
        				'orderclause' => 'name',
        				'class' => 'text'
        			),
        			array(
        				'attribute' => 'enabled',
        				'orderclause' => 'enabled',
        				'viewhelper' => 'bool',
        				'class' => 'text'
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
        $this->bean->setMeta('buildcommand.unique', array(array('name')));
        
        $this->setAutoTag(true);
        $this->setAutoInfo(true);
        $this->addValidator('name', 'hasvalue');
        $this->addValidator('name', 'isunique', array('bean' => $this->bean, 'attribute' => 'name'));
    }
}
