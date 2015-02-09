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
 * Manages import.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Import extends Cinnebar_Model
{
    /**
     * Map to translate media extension to mime type.
     *
     * @var array
     */
    protected $extensions = array(
        'csv' => 'text/comma-separated-values'
    );
    
    /**
     * Container for text encodings.
     *
     * @var array
     */
    protected $encodings = array(
        'ISO-8859-1',
		'UTF-8',
		'UTF-16'
    );
    
    /**
     * Read all entries from the import file and try to import them into the system.
     *
     * @return bool
     * @throws Exception if there is no data in the import file or if storing fails
     */
    public function execute()
    {
        $csv = $this->csv();
        if ( ! isset($csv['state']) || ! $csv['state']) {
            throw new Exception(__('import_no_data'));
        }
        $imported = array();
        $mappers = $this->bean->ownMap;
        foreach ($csv['records'] as $n=>$record) {
            $bean = R::dispense($this->bean->model);
            $bean->csvImport($this->bean, $record, $mappers);
            $imported[] = $bean;
            unset($bean);
        }
        return R::storeAll($imported);
    }
    
    /**
     * Import data from a csv formatted file.
     *
     * This model requires the parseCSV library {@link http://code.google.com/p/parsecsv-for-php/}.
     *
     * @uses parseCSV()
     *
     * @param int $index is the current cursor position in the import file
     * @return array
     */
    public function csv($index = 0)
	{
        require_once BASEDIR.'/vendors/parsecsv-0.3.2/parsecsv.lib.php';
	    $ret = array();
		$ret['state'] = false; // import of csv is in progress, allow dispatching and run
		$ret['file'] = basename($this->bean->file);
		$ret['delimiter'] = $this->bean->delimiter;
		$ret['enclosure'] = $this->bean->enclosure;
		$ret['encoding'] = $this->bean->encoding;
		
		$csv = new parseCSV();
		$csv->encoding($this->bean->encoding, 'UTF-8'); // encode from to UTF-8
		$csv->delimiter = $this->bean->delimiter;
		$csv->enclosure = $this->bean->enclosure;
		
		//$csv->parse($filename);
		$ret['auto_delimiter'] = $csv->auto($this->bean->dir.$this->bean->file);
		$ret['records'] = $csv->data;
		
		$ret['max_records'] = count($ret['records']);
		$index = max(0, $index);
		$index = min($ret['max_records'] - 1, $index);
		$ret['current_record'] = $index;
		if ($ret['max_records'] > 0) $ret['state'] = true;
		return $ret;
	}
	
	/**
	 * Returns a map instance of this import bean and the import source fieldname.
	 *
	 * @param string $sourcefieldname
	 * @return RedBean_OODBBean
	 */
	public function map($sourcefieldname)
	{
        if ( ! $map = R::findOne('map', ' import_id = ? AND source = ? LIMIT 1', array($this->bean->getId(), $sourcefieldname))) {
            $map = R::dispense('map');
            //$map->import = $this->bean;
        }
        $map->source = $sourcefieldname;
        return $map;
	}
	
	/**
	 * Returns a freshly dispensed bean of this import bean model type.
	 *
	 * @return RedBean_OODBBean $model
	 */
	public function model()
	{
        return R::dispense($this->bean->model);
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
			DISTINCT(import.id) as id  

		FROM
			import

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
        				'class' => 'number',
        				'filter' => array(
        				    'tag' => 'number'
        				)
        			),
        			array(
        				'attribute' => 'encoding',
        				'orderclause' => 'encoding',
        				'class' => 'text',
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
	 * Returns the array with text encodings.
	 *
	 * @return array
	 */
	public function encodings()
	{
        return $this->encodings;
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
        $this->setAutoInfo(true);
        $this->addConverter('file', 'fileupload', array('container' => 'file', 'extensions' => array('csv')));
        $this->addValidator('encoding', 'hasvalue');
        $this->addValidator('delimiter', 'hasvalue');
        $this->addValidator('file', 'hasupload');
        if ( ! $this->bean->getId()) {
            $this->bean->encoding = 'UTF-8';
            $this->bean->enclosure = '"';
            $this->bean->delimiter = ';';
            $this->bean->skiprows = 0;
        }
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
