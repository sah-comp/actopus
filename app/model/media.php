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
 * Manages media.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Media extends Cinnebar_Model
{
    /**
     * Map to translate media extension to mime type.
     *
     * @var array
     */
    protected $extensions = array(
        'gif' => 'image/gif',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png'
    );

    /**
     * Container for exensions that qualify as image files.
     *
     * @var array
     */
    protected $extensions_image = array(
        'jpg',
        'gif',
        'jpeg',
        'jpg',
        'png'
    );
    
    /**
     * Returns this media beans name, which is either the translated name or the basename.
     *
     * @return string
     */
    public function mediaName()
    {
        if ( ! $translated = $this->translated('name')) return $this->bean->basename;
        return $translated;
    }
    
    /**
     * Returns wether the uploaded file is an image file or not.
     *
     * Wether the file is an image or not is determined by testing the file extension the time being.
     *
     * @todo Implement a better image check using getimagesize()
     *
     * @return bool
     */
    public function isImage()
    {
        return in_array($this->bean->extension, $this->extensions_image);
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
			DISTINCT(media.id) as id  

		FROM
			media
			
		LEFT JOIN mediai18n ON mediai18n.media_id = media.id

		WHERE {$where_clause}

		ORDER BY {$order_clause}

		LIMIT {$offset}, {$limit}
SQL;
        return $sql;
    }
    
    /**
     * Returns SQL for fetch the total of all beans.
     *
     * @uses R
     * @param string $where_clause
     * @return string $SQL
     */
    public function sqlForTotal($where_clause = '1')
    {
		$sql = <<<SQL
		SELECT
			COUNT(DISTINCT(media.id)) as total  

		FROM
			media

		LEFT JOIN mediai18n ON mediai18n.media_id = media.id

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
        				'orderclause' => 'mediai18n.name',
        				'class' => 'text',
                        'callback' => array(
        				    'name' => 'mediaName'
        				),
        				'filter' => array(
        				    'tag' => 'text'
        				)
        			),
        			array(
        				'attribute' => 'extension',
        				'orderclause' => 'extension',
        				'class' => 'text',
        				'filter' => array(
        				    'tag' => 'text'
        				)
        			),
        			array(
        				'attribute' => 'filesize',
        				'orderclause' => 'filesize',
        				'class' => 'number',
        				'viewhelper' => 'humanmemorysize',
        				'filter' => array(
        				    'tag' => 'number'
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
	 * Returns the array wich maps extensions to image mime types.
	 *
	 * @return array
	 */
	public function extensions()
	{
        return $this->extensions;
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
     * Returns if there is a file.
     *
     * @return bool
     */
    public function hasFile()
    {
        return is_file($this->bean->dir.$this->bean->file);
    }

    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        //$this->bean->setMeta('buildcommand.unique', array(array('file')));
        $this->setAutoTag(true);
        $this->setAutoInfo(true);
        $this->addConverter('file', 'fileupload', array('container' => 'file', 'extensions' => null));
        $this->addValidator('file', 'hasupload');
        $this->addValidator('file', 'isunique', array('bean' => $this->bean, 'attribute' => 'file'));
    }
    
    /**
     * update.
     *
     * This will check for file uploads.
     */
    public function update()
    {
        parent::update();
    }
    
    /**
     * after_delete.
     *
     * After the bean was deleted from the database, we will also delete the real file.
     *
     */
    public function after_delete()
    {
        if (is_file($this->bean->dir.$this->bean->file)) {
            unlink($this->bean->dir.$this->bean->file);
        }
    }
}
