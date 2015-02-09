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
 * Manages domains.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Domain extends Cinnebar_Model
{  
    /**
     * Return ownDomain(s).
     *
     * @param bool $add if true an empty records gets added.
     * @return array
     */
    public function getownDomain($add)
    {
        $own = R::find('domain', ' domain_id = ? ORDER BY sequence, name', array($this->bean->getId()));
        if ($add) $own[] = R::dispense('domain');
        return $own;
    }

    /**
     * Builds a hierarchical menu from an adjancy bean.
     *
     * @param string (optional) $url_prefix as a kind of basehref, e.g. 'http://localhost/s/de'
     * @param string (optional) $lng code of the language to retrieve
     * @param string (optional) $orderclause defaults to 'sequence'
     * @param bool (optional) $invisibles default to false so that invisible beans wont show up
     * @return Cinnebar_Menu
     */
    public function hierMenu($url_prefix = '', $lng = null, $order = 'sequence ASC', $invisible = false)
    {
        $sql_invisible = 'AND invisible != 1';
        if ($invisible) {
            $sql_invisible = null;
        }
        $sql = sprintf(
            '%s = ? %s ORDER BY %s',
            $this->bean->getMeta('type').'_id',
            $sql_invisible, $order
        );
        $records = R::find(
            $this->bean->getMeta('type'),
            $sql,
            array($this->bean->getId())
        );
        $menu = new Cinnebar_Menu();
        foreach ($records as $record) {
            $menu->add(
                __('domain_'.$record->name),
                $url_prefix.$record->url,
                $record->getMeta('type').'-'.$record->getId(),
                $record->hierMenu($url_prefix, $lng, $order, $invisible)
            );
        }
        return $menu;
    }
    
    /**
     * Returns the (translated) name of the domain.
     *
     * @param string (optional) $lng iso code of the translation
     * @return string
     */
    public function name($lng = null)
    {
        if (empty($lng)) return $this->bean->name;
        return $this->bean->name.'('.$lng.')';
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
			DISTINCT(domain.id) as id  

		FROM
			domain

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
        				'class' => 'text',
        				'filter' => array(
        				    'tag' => 'text'
        				)
        			),
        			array(
        				'attribute' => 'url',
        				'orderclause' => 'url',
        				'class' => 'text',
        				'filter' => array(
        				    'tag' => 'text'
        				)
        			),
        			array(
        				'attribute' => 'invisible',
        				'orderclause' => 'invisible',
        				'class' => 'bool',
        				'viewhelper' => 'bool',
        				'filter' => array('tag' => 'bool')
        			),
        			array(
        				'attribute' => 'blessed',
        				'orderclause' => 'blessed',
        				'class' => 'bool',
        				'viewhelper' => 'bool',
        				'filter' => array('tag' => 'bool')
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
        return array($this->bean->name);
    }

    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        if ( ! $this->bean->domain_id) $this->bean->domain_id = null;
        $this->setAutoInfo(true);
        $this->addValidator('name', 'hasvalue');
    }
    
    /**
     * Update.
     */
    public function update()
    {
        if ( ! $this->bean->domain_id) $this->bean->domain_id = null;
        parent::update();
    }
}
