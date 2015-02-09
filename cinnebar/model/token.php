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
 * Manages translateable tokens.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Token extends Cinnebar_Model implements iToken
{
    /**
     * Returns the payload of the translation in current language or an empty string.
     *
     * @param string $attribute
     * @return string
     */
    public function translated($attribute)
    {
        return $this->in()->$attribute;
    }
    
    /**
     * Creates a new token or updates an existing one.
     *
     * @param string $name of the token
     * @param array $translations
     * @return bool
     */
    public function createOrUpdate($name, $translations = array())
    {
        if ( ! $token = R::findOne('token', ' name = ? LIMIT 1', array($name))) {
            $token = R::dispense('token');
            $token->name = $name;
        }
        
        $trans = R::dispense('translation', count($translations));
        foreach ($translations as $i => $translation) {
            $trans[$i]->iso = $translation['iso'];
            $trans[$i]->payload = $translation['payload'];
        }
        $token->ownTranslation = $trans;
        try {
            R::store($token);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Returns a translation of the token for the given language.
     *
     * @todo get rid of global language code
     * @todo get rid of this method in favor of Cinnebar_Model::i18n()
     *
     * @param mixed $iso code of the wanted translation language or null to use current language
     * @return RedBean_OODBBean $translation
     */
    public function in($iso = null)
    {
        global $language;
        if ($iso === null) $iso = $language;
        if ( ! $translation = R::findOne('translation', ' token_id = ? AND iso = ? LIMIT 1', array($this->bean->getId(), $iso))) {
            $translation = R::dispense('translation');
            $translation->iso = $iso;
            $translation->payload = $this->bean->name;
        }
        return $translation;
    }

    /**
     * Returns wether the bean is mulilingual or not.
     *
     * Attention: For a token bean this will return false. This is because a token does not
     * use the *i18n bean but instead it uses the translation bean to store mulitlingual content.
     *
     * @return bool
     */
    public function isI18n()
    {
        return false;
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
        global $language; /* Oh, how i loathe globals */
		$sql = <<<SQL
		SELECT
			COUNT(DISTINCT(token.id)) as total
		FROM
			token

		LEFT JOIN
		    translation ON translation.token_id = token.id AND translation.iso = '{$language}'

		WHERE {$where_clause}
SQL;
        return $sql;
    }
    
    /**
     * Returns SQL for filtering these beans.
     *
     * @todo get rid of global language code
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
        global $language; /* Oh, how i loathe globals */
		$sql = <<<SQL
		SELECT
			DISTINCT(token.id) as id
		FROM
			token
			
		LEFT JOIN
		    translation ON translation.token_id = token.id AND translation.iso = '{$language}'

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
        				'orderclause' => 'token.name',
        				'class' => 'text',
        				'filter' => array(
        				    'tag' => 'text'
        				)
        			),
        			array(
        				'attribute' => 'payload',
        				'orderclause' => 'translation.payload',
        				'class' => 'text',
        				'callback' => array(
        				    'name' => 'translated'
        				),
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
            $this->bean->name
        );
    }

    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        //$this->bean->setMeta('buildcommand.unique', array(array('name')));
        //$this->setAutoTag(true);
        $this->setAutoInfo(true);
        $this->addValidator('name', 'hasvalue');
        $this->addValidator('name', 'isunique', array('bean' => $this->bean, 'attribute' => 'name'));
    }
}
