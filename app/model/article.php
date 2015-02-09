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
 * Manages articles.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Article extends Cinnebar_Model
{
    /**
     * Returns the aliassed article bean or this articles bean if it is not aliassed.
     *
     * @return RedBean_OODBBean
     */
    public function aka()
    {
        if ( ! $aka = $this->bean->fetchAs('article')->aka) return $this->bean;
        return $aka;
    }

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
        @session_start();
        $iso = $_SESSION['backend']['language'];
        switch ($layout) {
            default:
                $sql = <<<SQL

                SELECT
                    DISTINCT(article.id) AS id,
                    articlei18n.name AS label,
                    articlei18n.thumbnail AS thumbnail,
                    articlei18n.keywords AS keywords,
                    articlei18n.description AS description,
                    article.invisible AS invisible,
                    articlei18n.template_id AS template_id

                FROM
                    article

                LEFT JOIN
                    articlei18n ON articlei18n.article_id = article.id

                WHERE
                    articlei18n.name like :searchtext AND
                    articlei18n.iso = :iso AND
                    article.aka_id IS NULL

                ORDER BY
                    articlei18n.name

SQL;
        }
        return $res = R::getAll($sql, array(':searchtext' => $term.'%', ':iso' => $iso));
    }

    /**
     * Returns slices of this article grouped by region.
     *
     * @param int $id of the region
     * @param string $iso code of the language
     * @param bool (optional) $add a empty region bean
     * @return array
     */
    public function sliceByRegionAndLanguage($id, $iso, $add = true)
    {
        $own = R::find('slice', ' article_id = ? AND region = ? AND iso = ? ORDER BY sequence', array($this->bean->getId(), $id, $iso));
        if ($add) $own[] = R::dispense('slice');
        return $own;
    }

    /**
     * Returns true because a article bean can be localized.
     *
     * @return bool
     */
    public function isI18n()
    {
        return true;
    }

    /**
     * Returns a newsletter bean.
     *
     * @return RedBeanOODB_Bean $newsletter
     */
    public function newsletter()
    {
        if ( ! $this->bean->newsletter) $this->bean->newsletter = R::dispense('newsletter');
        return $this->bean->newsletter;
    }

    /**
     * Returns the name of the newsletter.
     *
     * @return string
     */
    public function newsletterName()
    {
        return $this->bean->newsletter()->name;
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
			DISTINCT(article.id) as id  

		FROM
			article
			
		LEFT JOIN articlei18n ON articlei18n.article_id = article.id

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
			COUNT(DISTINCT(article.id)) as total
		FROM
			article

		LEFT JOIN articlei18n ON articlei18n.article_id = article.id

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
        				'orderclause' => 'articlei18n.name',
        				'callback' => array(
        				    'name' => 'translated'
        				),
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
        $this->setAutoInfo(true);
    }
}
