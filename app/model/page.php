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
 * Manages pages.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Page extends Cinnebar_Model
{
    /**
     * Returns all root pages which by definition are sites.
     *
     * @return array
     */
    public function sites()
    {
        return R::find('page', ' page_id IS NULL ORDER BY sequence');
    }
    
    /**
     * Returns an array with articles of this page.
     *
     * If no article exists we check for bubble up the tree to find the first
     * parent that has an article and we create at least a new article from that
     * one for every enabled language.
     */
    public function articles()
    {
        $articles = $this->own('article', false);
        if ( ! empty($articles)) return $articles;
        Cinnebar_Logger::instance()->log('Page '.$this->bean->getId().' has no articles, try to make one from its parent', 'warn');
        $parent = $this->bean->parent();
        if ( ! $parent->getId()) {
            Cinnebar_Logger::instance()->log('Page '.$this->bean->getId().' has no parent', 'warn');
            return false;
        }
        $articles = $parent->own('article', false);
        if (empty($articles)) {
            Cinnebar_Logger::instance()->log('Page '.$this->bean->getId().' parent does not have articles, we give up', 'warn');
            return false;
        }
        $first_article = reset($articles);
        $article = R::dispense('article');
        $enabled_languages = R::dispense('language')->enabled();
        foreach ($enabled_languages as $id => $language) {
            $first_article_i18n = $first_article->i18n($language->iso);
            $article_i18n = R::dispense('articlei18n');
            $article_i18n->iso = $language->iso;
            $article_i18n->template = $first_article_i18n->template();
            $article_i18n->name = $this->bean->i18n($language->iso)->name;
            $article->ownArticlei18n[] = $article_i18n;
        }
        try {
            $this->bean->ownArticle[] = $article;
            R::store($this->bean);
            return $this->bean->own('article', false);
        } catch (Exception $e) {
            Cinnebar_Logger::instance()->log('Failed to store page after trying to add article and localized articles from parent', 'exceptions');
            return false;
        }
    }

    /**
     * Return ownArticle(s).
     *
     * @param bool $add if true an empty records gets added.
     * @return array
     */
    public function getownArticle($add = false)
    {
        $own = R::find('article', ' page_id = ? ORDER BY sequence', array($this->bean->getId()));
        if ($add) $own[] = R::dispense('article');
        return $own;
    }

    /**
     * Return ownPage(s).
     *
     * @param bool $add if true an empty records gets added.
     * @return array
     */
    public function getownPage($add)
    {
        $own = R::find('page', ' page_id = ? ORDER BY sequence', array($this->bean->getId()));
        if ($add) $own[] = R::dispense('page');
        return $own;
    }

    /**
     * Returns wether the bean is mulilingual or not.
     *
     * @return bool
     */
    public function isI18n()
    {
        return true;
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
			DISTINCT(page.id) as id  

		FROM
			page

		LEFT JOIN pagei18n ON pagei18n.page_id = page.id

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
			COUNT(DISTINCT(page.id)) as total

		FROM
			page

		LEFT JOIN pagei18n ON pagei18n.page_id = page.id

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
        				'orderclause' => 'pagei18n.name',
        				'class' => 'text',
        				'callback' => array(
        				    'name' => 'translated'
        				),
        				'filter' => array(
        				    'tag' => 'text'
        				)
        			),
        			array(
        				'attribute' => 'invisible',
        				'orderclause' => 'page.invisible',
        				'class' => 'bool',
        				'viewhelper' => 'bool',
            			'filter' => array(
            			    'tag' => 'text'
            			)
        			)
        		);
        }
        return $ret;
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
        $sql_language = null;
        $sql_invisible = 'AND page.invisible != 1';
        if ($invisible) {
            $sql_invisible = null;
        }
        if ($lng !== null) {
            $sql_language = sprintf("AND i18n.iso = '%s'", $lng);
        }
        $sql = 'SELECT page.id AS id, i18n.name AS name FROM page LEFT JOIN pagei18n AS i18n ON i18n.page_id = page.id WHERE page.page_id = ? %s %s ORDER BY page.sequence';
        $sql = sprintf($sql, $sql_invisible, $sql_language);
        //R::debug(true);
		$assoc = R::$adapter->getAssoc($sql, array($this->bean->getId()));
		//R::debug(false);
		$records = R::batch('page', array_keys($assoc));
		
        $menu = new Cinnebar_Menu();
        foreach ($records as $record) {
            $class = $record->getMeta('type').'-'.$record->getId();
    		if ($record->invisible) $class .= ' inactive';
            $menu->add(
                $record->i18n()->name,
                $url_prefix.$record->getId(),
                $class,
                $record->hierMenu($url_prefix, $lng, $order, $invisible),
                $record->getMeta('type').'-'.$record->getId()
            );
        }
        return $menu;
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
     * update.
     */
    public function update()
    {
        parent::update();
    }

    /**
     * Setup validators and set auto info to true.
     *
     * A page bean will have sequence = 0, enabled = 1 when dispensed.
     */
    public function dispense()
    {
        $this->setAutoInfo(true);
        if ( ! $this->bean->getId()) {
            $this->bean->sequence = 0;
            $this->bean->invisible = 0;
        }
    }
}
