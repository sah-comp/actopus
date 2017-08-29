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
 * Handles CURD operations on a certain bean type.
 *
 * As the Death Star Cantina man says: "you'll still need a tray." please do not forget to have the model
 * templates for the scaffold bean type in place. I went that way because scaffolding is nice, but later
 * on i have to setup individual partials for customizing needs anyway.
 *
 * @todo Evolve a plugin from this controller so that any controller may benefit
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_Scaffold extends Cinnebar_Controller
{
    /**
     * Default limit value.
     */
    const LIMIT = 23;
    
    /**
     * Default layout for index.
     */
    const LAYOUT = 'table';

    /**
     * Holds the bean type to apply scaffolding to.
     *
     * @var string
     */
    public $type = 'token';
    
    /**
     * Holds the alias for a bean type to apply pageflip to.
     *
     * @var string
     */
    public $typeAlias = null;
    
    /**
     * Holds the current action.
     *
     * @var string
     */
    public $action;
    
    /**
     * Holds the path to scaffold templates.
     *
     * @var string
     */
    public $path = 'shared/scaffold/';
    
    /**
     * Holds an instance of a Cinnebar_View
     *
     * @var Cinnebar_View
     */
    public $view;
    
    /**
     * Holds the current page number.
     *
     * @var int
     */
    public $page;
    
    /**
     * Holds the limit of beans to fetch at once.
     *
     * @var int
     */
    public $limit;
    
    /**
     * Holds the layout to use.
     *
     * @var string
     */
    public $layout;
    
    /**
     * Holds the key of the orderclause to use for sorting.
     *
     * @var int
     */
    public $order;
    
    /**
     * Holds the dir(ection) key for sorting.
     *
     * @var int
     */
    public $dir;
    
    /**
     * Container for sort directions.
     *
     * @var array
     */
    public $sortdirs = array(
        'ASC',
        'DESC'
    );
    
    /**
     * Container for actions.
     *
     * @var array
     */
    public $actions = array(
        'table' => array('expunge'),
        'edit' => array('next', 'prev', 'update', 'list'),
        'add' => array('continue', 'update', 'list')
    );
    
    /**
     * Set environmental parameters like page, limit and so on.
     *
     * A record (maybe empty) of a certain type is loaded and the environment is set according
     * to the given parameters.
     *
     * @uses Cinnebar_Model::makeMenu() to create a menu based on the current action
     * @uses Cinnebar_Model::makeActions() to create possible scaffold actions
     * @uses $actions which holds the preset scaffolding actions
     * @param int $page
     * @param int $limit
     * @param string $layout
     * @param int $order
     * @param int (optional) $id of a certain bean to load
     * @param string $action
     */
    protected function env($page, $limit, $layout, $order, $dir, $id = null, $action = 'index')
    {
        $this->view = $this->makeView($this->path.$action);
        $this->view->title = __('scaffold_head_title_'.$action);
        
        $this->view->user = $this->user();
        $this->view->action = $this->action = $action;
        $this->view->record = $this->record($id);
        
        $this->view->layout = $this->layout = $layout;
        
        $this->view->filter = $this->make_filter();
        
        $this->view->page = $this->page = $page;
        $this->view->limit = $this->limit = $limit;
        
        $this->view->attributes = $this->view->record->attributes($this->view->layout);
        $this->view->colspan = count($this->view->attributes) + 1;
        $this->view->order = $this->order = $order;
        $this->view->sortdirs = $this->sortdirs;
        $this->view->orderclass = strtolower($this->sortdirs[$dir]);
        $this->view->dir = $this->dir = $dir;
        $this->view->id = $this->id = $id;
        $this->view->actions = $this->view->record->makeActions($this->actions);
        //$this->view->action = $this->action = $action;
        $this->view->followup = null;
        if (isset($_SESSION['scaffold'][$this->view->action]['followup'])) {
            $this->view->followup = $_SESSION['scaffold'][$this->view->action]['followup'];
        }
        // Last, but not least we create a menu
        $this->view->nav = R::findOne('domain', ' blessed = ? LIMIT 1', array(1))->hierMenu($this->view->url());
        $this->view->navfunc = $this->view->record->makeMenu($action, $this->view, $this->view->nav);
        
        $this->view->urhere = with(new Cinnebar_Menu())->add(__($this->type.'_head_title'), $this->view->url(sprintf('/%s/index/%d/%d/%s/%d/%d', $this->type, 1, self::LIMIT, $this->view->layout, $this->view->order, $this->view->dir)));
    }
    
    /**
     * Generates an instance of filter.
     *
     * @return Model_Filter
     */
    protected function make_filter()
    {
        if ( ! isset($_SESSION['filter'][$this->type]['id'])) {
            $_SESSION['filter'][$this->type]['id'] = 0;
        }
        $filter = R::load('filter', $_SESSION['filter'][$this->type]['id']);
        if ( ! $filter->getId()) {
            $filter->rowsperpage = self::LIMIT;
            $filter->model = $this->type; // always re-inforce the bean type
            $filter->user = $this->view->user; // owner
            $filter->logic = 'AND';
            $filter->name = __('filter_auto_title', array(__('domain_' . $this->type)));
            try {
                R::store($filter);
                $_SESSION['filter'][$this->type]['id'] = $filter->getId();
            } catch (Exception $e) {
                Cinnebar_Logger::instance()->log('Filter: '.$e, 'exceptions');
            }
        }
        return $filter;
    }
    
    /**
     * Returns an bean of the scaffolded type.
     *
     * The returned bean may be empty in case the optional parameter was not given.
     *
     * @uses $type
     * @param int (optional) $id
     * @return RedBean_OODBBean
     */
    protected function record($id = null)
    {
        return R::load($this->type, $id);
    }
    
    /**
     * Returns an array of beans.
     *
     * @uses $type
     * @uses Cinnebar_Filter to build where clause and get filter values
     * @uses Cinnebar_Model to get SQL for filters
     * @return array
     */
    protected function collection()
    {
    	$whereClause = $this->view->filter->buildWhereClause();
		$orderClause = $this->view->attributes[$this->order]['orderclause'].' '.$this->sortdir($this->dir);
		$sql = $this->view->record->sqlForFilters($whereClause, $orderClause, $this->offset($this->page, $this->limit), $this->limit);
		
		$this->view->total = 0;
		
		try {
			//R::debug(true);
			$assoc = R::$adapter->getAssoc($sql, $this->view->filter->filterValues());
			//R::debug(false);
			$this->view->records = R::batch($this->type, array_keys($assoc));
			//R::debug(true);
            $this->view->total = R::getCell($this->view->record->sqlForTotal($whereClause), $this->view->filter->filterValues());
			//R::debug(false);
			//error_log(count($this->records));
			return true;
		} catch (Exception $e) {
            Cinnebar_Logger::instance()->log('Scaffold Collection has issues: '.$e.' '.$sql, 'sql');
			$this->view->records = array();
			return false;
		}    
    }
    
    /**
     * Returns the offset calculated from the current page number and limit of rows per page.
     *
     * @param int $page
     * @param int $limit
     * @return int
     */
    protected function offset($page, $limit)
    {
        return ($page - 1) * $limit;
    }
    
    /**
     * Returns the sort direction.
     *
     * @param int $dir
     * @return string
     */
    protected function sortdir($dir = 0)
    {
        if ( ! isset($this->sortdirs[$dir])) return 'ASC';
        return $this->sortdirs[$dir];
    }
    
	/**
	 * Checks for the callback trigger method and if it exists calls it.
	 *
	 * You can have none, one or all of these callbacks in your custom controller:
	 * - before_add
	 * - after_add
	 * - before_view
	 * - after_view
	 * - before_edit
	 * - after_edit
	 * - before_delete
	 * - after_delete
	 * - before_index
	 * - after_index
	 *
	 * @param string $action
	 * @param string $condition
	 * @return void
	 */
	public function trigger($action, $condition)
	{
	    $callback = $condition.'_'.$action;
        if ( ! method_exists($this, $callback)) return;
        $this->$callback();
	}
	
	/**
	 * Applies the action to a selection of beans.
	 *
	 * @param mixed $pointers must hold a key/value array where key is the id of a bean
	 * @param int $action
	 * @return bool
	 */
	protected function applyToSelection($pointers = null, $action = 'idle')
	{
        if ( ! $this->permission()->allowed($this->user(), $this->type, $action)) {
			return false;
		}	    

        if ( empty($pointers)) return false;
        if ( ! is_array($pointers)) return false;
        $valid = true;
        foreach ($pointers as $id=>$switch) {
            $record = R::load($this->type, $id);
            try {
                $record->$action();
            } catch (Exception $e) {
                $valid = false;
            }
        }
        if ($valid) return count($pointers);
        return false;
	}

    /**
     * Displays a page with a (paginated) selection of beans.
     *
     * @param int $page
     * @param int $limit
     * @param string $layout
     * @param int $order
     */
    public function report($page = 1, $limit = self::LIMIT, $layout = self::LAYOUT, $order = 0, $dir = 0)
    {
        $this->cache()->deactivate();
        
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/'.$this->router()->internalUrl())));
        
        if ( ! $this->permission()->allowed($this->user(), $this->type, 'index')) {
			return $this->error('403');
		}

        $this->env($page, $limit, $layout, $order, $dir, null, 'report');
        /*
        if (empty($this->view->filter->ownCriteria)) {
            $attribute = reset($this->view->record->attributes('report'));
            $criteria = R::dispense('criteria');
            $criteria->attribute = $attribute['orderclause'];
            if (isset($attribute['filter']['orderclause'])) {
                $criteria->attribute = $attribute['filter']['orderclause'];
            }
            $criteria->tag = $attribute['filter']['tag'];
            $this->view->filter->ownCriteria = array($criteria);
        }
        */
        $this->trigger('report', 'before');
        
        
        if ($this->input()->post()) {
            
            if ($this->input()->post('otherreport')) {
                // change to another report
                $_SESSION['filter'][$this->type]['id'] = $this->input()->post('otherreport');
                $this->redirect(sprintf('/%s/report/%d/%d/%s/%d/%d/', $this->type, 1, $this->limit, $this->layout, $this->order, $this->dir));
            }
            
            if ($this->input()->post('submit') == __('filter_submit_refresh')) {
                $this->view->filter = R::graph($this->input()->post('filter'), true);
                try {
                    R::store($this->view->filter);
                    $_SESSION['filter'][$this->type]['id'] = $this->view->filter->getId();
                    $this->redirect(sprintf('/%s/report/%d/%d/%s/%d/%d/', $this->type, 1, $this->limit, $this->layout, $this->order, $this->dir));
                } catch (Exception $e) {
                    Cinnebar_Logger::instance()->log($e, 'exceptions');
                    $message = __('action_filter_error');
                    with(new Cinnebar_Messenger)->notify($this->user(), $message, 'error');
                    //$this->view->filter->addError('error_filter_could_not_be_applied');
                }
            }
            if ($this->input()->post('submit') == __('filter_submit_clear') && $_SESSION['filter'][$this->type]['id']) {
                //R::trash($this->view->filter);
                $_SESSION['filter'][$this->type]['id'] = 0;
                unset($_SESSION['filter'][$this->type]['id']);
                $this->redirect(sprintf('/%s/report/%d/%d/%s/%d/%d/', $this->type, 1, $this->limit, $this->layout, $this->order, $this->dir));
            }
            if ($this->input()->post('submit') == __('filter_submit_delete') && $_SESSION['filter'][$this->type]['id']) {
                R::trash($this->view->filter);
                $_SESSION['filter'][$this->type]['id'] = 0;
                unset($_SESSION['filter'][$this->type]['id']);
                $this->redirect(sprintf('/%s/report/%d/%d/%s/%d/%d/', $this->type, 1, $this->limit, $this->layout, $this->order, $this->dir));
            }
            
            if ($this->input()->post('submit') == __('scaffold_submit_order')) {
                $this->redirect(sprintf('/%s/report/%d/%d/%s/%d/%d/', $this->type, 1, $this->limit, $this->layout, $this->input()->post('order'), $this->input()->post('dir')));            
            }
            
            $this->view->selection = $this->input()->post('selection');
            $counter = $this->applyToSelection($this->view->selection[$this->type], $this->input()->post('action'));
            if ($counter) {
                $message = __('scaffold_apply_to_selection_success', array($counter, __('action_'.$this->input()->post('action'))), null, null, 'This takes two parameters where the first one is the number of records and the second is the translation of the applied action token');
                with(new Cinnebar_Messenger)->notify($this->user(), $message, 'success');
            }

            $this->trigger('report', 'after');
            $this->redirect(sprintf('/%s/report/%d/%d/%s/%d/%d/', $this->type, 1, $this->limit, $this->layout, $this->order, $this->dir));
        }

        $this->collection();
        
        $this->view->pagination = new Cinnebar_Pagination(
            $this->view->url(sprintf('/%s/report/', $this->type)),
            $this->page,
            $this->limit,
            $this->layout,
            $this->order,
            $this->dir,
            $this->view->total
        );
        
        $this->trigger('report', 'after');
        
        echo $this->view->render();
    }
    
    /**
     * Displays a page with a (paginated) selection of beans.
     *
     * @param int $page
     * @param int $limit
     * @param string $layout
     * @param int $order
     * @param int $dir
     */
    public function index($page = 1, $limit = self::LIMIT, $layout = self::LAYOUT, $order = 0, $dir = 0)
    {
        $this->cache()->deactivate();
        
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/'.$this->router()->internalUrl())));
        
        if ( ! $this->permission()->allowed($this->user(), $this->type, 'index')) {
			return $this->error('403');
		}

        $this->env($page, $limit, $layout, $order, $dir, null, 'index');
        
        $this->trigger('index', 'before');
        
        
        if ($this->input()->post()) {
            
            if ($this->input()->post('submit') == __('filter_submit_refresh')) {
                $this->view->filter = R::graph($this->input()->post('filter'), true);
                try {
                    R::store($this->view->filter);
                    $_SESSION['filter'][$this->type]['id'] = $this->view->filter->getId();
                    $this->redirect(sprintf('/%s/index/%d/%d/%s/%d/%d/', $this->type, 1, $this->limit, $this->layout, $this->order, $this->dir));
                } catch (Exception $e) {
                    Cinnebar_Logger::instance()->log($e, 'exceptions');
                    $message = __('action_filter_error');
                    with(new Cinnebar_Messenger)->notify($this->user(), $message, 'error');
                    //$this->view->filter->addError('error_filter_could_not_be_applied');
                }
            }
            if ($this->input()->post('submit') == __('filter_submit_clear') && $_SESSION['filter'][$this->type]['id']) {
                R::trash($this->view->filter);
                $_SESSION['filter'][$this->type]['id'] = 0;
                $this->redirect(sprintf('/%s/index/%d/%d/%s/%d/%d/', $this->type, 1, $this->limit, $this->layout, $this->order, $this->dir));
            }
            
            if ($this->input()->post('submit') == __('scaffold_submit_order')) {
                $this->redirect(sprintf('/%s/index/%d/%d/%s/%d/%d/', $this->type, 1, $this->limit, $this->layout, $this->input()->post('order'), $this->input()->post('dir')));            
            }
            
            $this->view->selection = $this->input()->post('selection');
            $counter = $this->applyToSelection($this->view->selection[$this->type], $this->input()->post('action'));
            if ($counter) {
                $message = __('scaffold_apply_to_selection_success', array($counter, __('action_'.$this->input()->post('action'))), null, null, 'This takes two parameters where the first one is the number of records and the second is the translation of the applied action token');
                with(new Cinnebar_Messenger)->notify($this->user(), $message, 'success');
            }

            $this->trigger('index', 'after');
            $this->redirect(sprintf('/%s/index/%d/%d/%s/%d/%d/', $this->type, 1, $this->limit, $this->layout, $this->order, $this->dir));
        }

        $this->collection();
        
        $this->view->pagination = new Cinnebar_Pagination(
            $this->view->url(sprintf('/%s/index/', $this->type)),
            $this->page,
            $this->limit,
            $this->layout,
            $this->order,
            $this->dir,
            $this->view->total
        );
        
        $this->trigger('index', 'after');
        
        echo $this->view->render();
    }
    
    /**
     * Creates a PDF from a HTML using FPDF.
     *
     * This is basically the same as index but limit and offset are manipulated to start from
     * zero (0) offset and use all records possible.
     *
     * @param int $page
     * @param int $limit
     * @param string $layout
     * @param int $order
     * @param int $dir
     */
    public function htmlpdf($page = 1, $limit = self::LIMIT, $layout = self::LAYOUT, $order = 0, $dir = 0)
    {
        $this->cache()->deactivate();
        
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/'.$this->router()->internalUrl())));
        
        if ( ! $this->permission()->allowed($this->user(), $this->type, 'index')) {
			return $this->error('403');
		}

        $this->env($page, $limit, $layout, $order, $dir, null, 'htmlpdf');
        
        // memorize limit and offset
        $real_limit = $this->limit;
        $real_offset = $this->offset;
        // use offset and limit...
        $this->limit = R::count($this->type);//10000000;
        $this->offset = 0;
        // ... to get a collection of all records
        $this->collection();
        // and then go back to what is used on the screen
        $this->limit = $real_limit;
        $this->offset = $real_offset;
    	require_once BASEDIR.'/vendors/mpdf/mpdf.php';
        $docname = 'Cards as PDF';
        $filename = 'Liste.pdf';
        $mpdf = new mPDF('c', 'A4');
        $mpdf->SetTitle($docname);
        $mpdf->SetAuthor( 'von Rohr' );
        $mpdf->SetDisplayMode('fullpage');

        $html = $this->view->render();

        $mpdf->WriteHTML( $html );
        $mpdf->Output($filename, 'D');
        exit;
    }
    
    /**
     * Prints selection of beans.
     *
     * This is basically the same as index but limit and offset are manipulated to start from
     * zero (0) offset and use all records possible.
     *
     * @param int $page
     * @param int $limit
     * @param string $layout
     * @param int $order
     */
    public function press($page = 1, $limit = self::LIMIT, $layout = self::LAYOUT, $order = 0, $dir = 0)
    {
        $this->cache()->deactivate();
        
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/'.$this->router()->internalUrl())));
        
        if ( ! $this->permission()->allowed($this->user(), $this->type, 'index')) {
			return $this->error('403');
		}

        $this->env($page, $limit, $layout, $order, $dir, null, 'index');
        
        // memorize limit and offset
        $real_limit = $this->limit;
        $real_offset = $this->offset;
        // use offset and limit...
        $this->limit = R::count($this->type);//10000000;
        $this->offset = 0;
        // ... to get a collection of all records
        $this->collection();
        // and then go back to what is used on the screen
        $this->limit = $real_limit;
        $this->offset = $real_offset;
        
        $data = array();
        //$data[] = $this->view->record->exportToCSV(true);
        foreach ($this->view->records as $id => $record) {
            $data[] = $record->exportToCSV(false, $layout);
        }
        
        require_once BASEDIR.'/vendors/parsecsv-0.3.2/parsecsv.lib.php';
        $csv = new ParseCSV();
        $csv->output(true, __($this->view->record->getMeta('type').'_head_title').'.csv', $data, $this->view->record->exportToCSV(true, $layout));
        exit;
    }
    
    /**
     * dup(licate) a record and redirect to edit it
     */
    public function duplicate($id)
    {
        $this->cache()->deactivate();
        
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/'.$this->router()->internalUrl())));
        
        if ( ! $this->permission()->allowed($this->user(), $this->type, 'add')) {
			return $this->error('403');
		}
		
		$record = R::load($this->type, $id);
		$dup = R::dup($record);
		//error_log($dup->name . ' Copy');
		$dup->name = $dup->name . ' Kopie';
		try {
		    $dup->validationMode(Cinnebar_Model::VALIDATION_MODE_IMPLICIT);
		    $dup->prepareForDuplication();
		    R::store($dup);
		} catch (Exception $e) {
            Cinnebar_Logger::instance()->log($e, 'exceptions');
		}
	    // goto to edit the duplicate
	    $this->redirect(sprintf('/%s/edit/%d/', $dup->getMeta('type'), $dup->getId()));
    }
    
    /**
     * Displays the bean in a form so it can be added.
     *
     * @param int $page
     * @param int $limit
     * @param string $layout
     * @param int $order
     * @param int $dir
     */
    public function add($id = 0, $page = 1, $limit = self::LIMIT, $layout = self::LAYOUT, $order = 0, $dir = 0)
    {
        $this->cache()->deactivate();
        
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/'.$this->router()->internalUrl())));
        
        if ( ! $this->permission()->allowed($this->user(), $this->type, 'add')) {
			return $this->error('403');
		}
        
        $this->env($page, $limit, $layout, $order, $dir, $id, 'add');
        
        $this->trigger('add', 'before');
        
        if ($this->input()->post()) {
            $this->view->record = R::graph($this->input()->post('dialog'), true);
            $_SESSION['scaffold']['lasttab'] = $this->input()->post('lasttab');
            try {
                R::store($this->view->record);
                
                $_SESSION['scaffold']['add']['followup'] = $followup = $this->input()->post('action');
                
                $message = __('action_add_success');
                with(new Cinnebar_Messenger)->notify($this->user(), $message, 'success');
                
                $this->trigger('add', 'after');
                
                if ($followup == 'list') {
                    $this->redirect(sprintf('/%s/index/%d/%d/%s/%d/%d/', $this->type, 1, self::LIMIT, $this->layout, $this->order, $this->dir));
                }

                if ($followup == 'update') {
                    $this->redirect(sprintf('/%s/edit/%d/%d/%d/%s/%d/%d/', $this->type, $this->view->record->getId(), 1, self::LIMIT, $this->layout, $this->order, $this->dir));
                }
                
                $this->redirect(sprintf('/%s/add', $this->type));

            } catch (Exception $e) {
                
                Cinnebar_Logger::instance()->log($e, 'exceptions');
                $message = __('action_add_error');
                with(new Cinnebar_Messenger)->notify($this->user(), $message, 'error');
                
            }
        }
        else {
            if ($this->view->record->getId()) {
                $message = __('action_clone_success', array($this->view->url(sprintf('/%s/edit/%d/%d/%d/%s/%d/%d/', $this->type, $this->view->record->getId(), 1, self::LIMIT, $this->layout, $this->order, $this->dir))));
                with(new Cinnebar_Messenger)->notify($this->user(), $message, 'info');
                //$this->view->record = R::dup($this->view->record);
            }
        }
        
        $this->view->records = array();
        
        $this->trigger('add', 'after');
        
        echo $this->view->render();
    }
    
    /**
     * Displays a form to import csv into beans.
     *
     * @param int (optional) $id of the import bean used to import type of scaffold bean
     * @param int (optional) $page is the index of the current import record to view
     */
    public function import($id = null, $page = 0)
    {
        $this->cache()->deactivate();
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/'.$this->router()->internalUrl())));
        
        if ( ! $this->permission()->allowed($this->user(), $this->type, 'import')) {
			return $this->error('403');
		}

        $this->env($page, 0, 0, 0, 0, $id, 'import'); // horrible!!
        
        //$this->trigger('import', 'before');
        $this->view->record = R::load('import', $id);
        $this->view->record->model = $this->type; // always re-inforce bean type
        $this->view->csv = $this->view->record->csv($this->view->page);

        if ($this->input()->post()) {
            $this->view->record = R::graph($this->input()->post('dialog'), true);
            try {
                R::store($this->view->record);

                if ($this->input()->post('submit') == __('scaffold_submit_import')) {
                    $message = __('action_import_success');
                    with(new Cinnebar_Messenger)->notify($this->user(), $message, 'success');
            
                    //$this->trigger('import', 'after');
                    //$this->redirect(sprintf('/%s/import/%d', $this->type, $this->view->record->getId()));
                }
                elseif ($this->input()->post('submit') == __('import_submit_prev')) {
                    $this->view->page = max(0, $this->view->page - 1);
                }
                elseif ($this->input()->post('submit') == __('import_submit_next')) {
                    $this->view->page = min($this->view->csv['max_records'] - 1, $this->view->page + 1);
                }
                elseif ($this->input()->post('submit') == __('import_submit_execute')) {
                    // tries to import from csv with rollback
                    R::begin();
                    try {
                        $imported_ids = $this->view->record->execute(); // will throw exception if it fails
                        $message = __('action_imported_n_of_m_success', array(count($imported_ids), count($this->view->csv['records'])));
                        with(new Cinnebar_Messenger)->notify($this->user(), $message, 'success');
                        R::commit();
                        $this->redirect(sprintf('/%s/index', $this->view->record->model));
                    } catch (Exception $e) {
                        R::rollback();
                        $message = __('action_import_error_invalid_data');
                        with(new Cinnebar_Messenger)->notify($this->user(), $message, 'error');
                        $this->redirect(sprintf('/%s/import/%d/%d', $this->type, $this->view->record->getId(), $this->view->page));
                    }
                }
                else {
                    Cinnebar_Logger::instance()->log('scaffold import unkown post request', 'warn');
                }
                $this->redirect(sprintf('/%s/import/%d/%d', $this->type, $this->view->record->getId(), $this->view->page));

            } catch (Exception $e) {
            
                $message = __('action_import_error');
                Cinnebar_Logger::instance()->log($e, 'scaffold');
                with(new Cinnebar_Messenger)->notify($this->user(), $message, 'error');

            }
        }
        
        $this->view->records = array();
        //$this->trigger('import', 'after');
        
        echo $this->view->render();
    }
    
    /**
     * Displays the bean in a form so it can be edited.
     *
     * @param int $page
     * @param int $limit
     * @param string $layout
     * @param int $order
     * @param int $dir
     */
    public function edit($id, $page = 1, $limit = self::LIMIT, $layout = self::LAYOUT, $order = 0, $dir = 0)
    {
        $this->cache()->deactivate();
        
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/'.$this->router()->internalUrl())));
        
        if ( ! $this->permission()->allowed($this->user(), $this->type, 'edit')) {
			return $this->error('403');
		}

        $this->env($page, $limit, $layout, $order, $dir, $id, 'edit');
        
        $this->trigger('edit', 'before');
        if ($this->input()->post()) {
            $this->view->record = R::graph($this->input()->post('dialog'), true);
            $_SESSION['scaffold']['lasttab'] = $this->input()->post('lasttab');
            try {
                R::store($this->view->record);
                
                $message = __('action_edit_success');
                with(new Cinnebar_Messenger)->notify($this->user(), $message, 'success');

                $_SESSION['scaffold']['edit']['followup'] = $followup = $this->input()->post('action');
                
                $this->trigger('edit', 'after');
                
                if ($followup == 'next' && $id = $this->id_at_offset($this->page + 1)) {
                    $this->redirect(sprintf('/%s/edit/%d/%d/%d/%s/%d/%d/', $this->type, $id, $this->page + 1, 1, $this->layout, $this->order, $this->dir));
                }
                if ($followup == 'prev' && $id = $this->id_at_offset($this->page - 1)) {
                    $this->redirect(sprintf('/%s/edit/%d/%d/%d/%s/%d/%d/', $this->type, $id, $this->page - 1, 1, $this->layout, $this->order, $this->dir));
                }
                if ($followup == 'list') {
                    $this->redirect(sprintf('/%s/index/%d/%d/%s/%d/%d/', $this->type, 1, self::LIMIT, $this->layout, $this->order, $this->dir));
                }
                
                if ($followup == 'listandreset') {
                    unset($_SESSION['filter'][$this->type]['id']);
                    $this->redirect(sprintf('/%s/index/%d/%d/%s/%d/%d/', $this->type, 1, self::LIMIT, $this->layout, $this->order, $this->dir));
                }
                
                $this->redirect(sprintf('/%s/edit/%d/%d/%d/%s/%d/%d/', $this->type, $this->view->record->getId(), $this->page, $this->limit, $this->layout, $this->order, $this->dir));

            } catch (Exception $e) {
                Cinnebar_Logger::instance()->log($e, 'exceptions');
                $message = __('action_edit_error');
                with(new Cinnebar_Messenger)->notify($this->user(), $message, 'error');

            }
        }
        $this->view->records = array();
        $this->make_pageflip();
        $this->trigger('edit', 'after');
        
        echo $this->view->render();
    }
    
    /**
     * Creates a pageflip menu.
     *
     * @uses Cinnebar_Menu to build a prev and next page navigation for edit or other views
     */
    protected function make_pageflip()
    {
        // add pageflipper, did you like flipper? I eat it a lot.
        $next_id = $this->id_at_offset($this->page + 1);
        $prev_id = $this->id_at_offset($this->page - 1);
        
        $this->view->pageflip = new Cinnebar_Menu();
        
        if ($prev_id) {
            $page = $this->page - 1;
            $id = $prev_id;
        } else {
            $page = $this->page;
            $id = $this->view->record->getId();
        }
        $this->view->pageflip->add(__('pagination_page_prev'), $this->view->url(sprintf('/%s/edit/%d/%d/%d/%s/%d/%d/', $this->getType(), $id, $page, $this->limit, $this->layout, $this->order, $this->dir)));

        if ($next_id) {
            $page = $this->page + 1;
            $id = $next_id;
        } else {
            $page = $this->page;
            $id = $this->view->record->getId();
        }
        $this->view->pageflip->add(__('pagination_page_next'), $this->view->url(sprintf('/%s/edit/%d/%d/%d/%s/%d/%d/', $this->getType(), $id, $page, $this->limit, $this->layout, $this->order, $this->dir)));
    }
    
    /**
     * Returns the current type or alias as a string.
     *
     * @uses $typeAlias, $type
     * @return string
     */
    public function getType()
    {
        if ( ! $this->typeAlias) return $this->type;
        return $this->typeAlias;
    }
    
    /**
     * Returns the id of a bean at a certain (filtered) list position or the id of
     * the current bean if the query failed.
     *
     * @uses buildWhereClause() to check for an active filter
     * @uses Model::sqlForFilters() to gather the SQL for selection a selection of this beans
     * @param int $offset
     * @return mixed $idOfTheBeanAtPositionInFilteredListOrFalse
     */
    protected function id_at_offset($offset)
    {
        $offset--; //because we count page 1..2..3.. where the offset has to be 0..1..2..
        if ($offset < 0) return false;
        $whereClause = $this->view->filter->buildWhereClause();
		$orderClause = $this->view->attributes[$this->order]['orderclause'].' '.$this->sortdir($this->dir);

    	$sql = $this->view->record->sqlForFilters($whereClause, $orderClause, $offset, 1);

    	try {
    		return R::getCell($sql, $this->view->filter->filterValues());
    	} catch (Exception $e) {
            return false;
    	}
    }

    /**
     * Pushes setting bean to the view.
     *
     * @return void
     */
    protected function pushSettingToView()
    {
        global $config;
        if ( ! $this->view->basecurrency = R::findOne('currency', ' iso = ? LIMIT 1', array($config['currency']['base']))) {
            $this->view->basecurrency = R::dispense('currency');
            $this->view->basecurrency->iso = $config['currency']['base'];
            $this->view->basecurrency->exchangerate = 1.0000;
        }
        $this->view->setting = R::load('setting', 1);
    }

    /**
     * Pushes enabled language beans to the view.
     *
     * @return void
     */
    protected function pushEnabledLanguagesToView()
    {
        $this->view->languages = R::dispense('language')->enabled();
    }
}
