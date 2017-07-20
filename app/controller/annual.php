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
 * Manages card beans that are "gebührentaschen".
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_Annual extends Controller_Card
{
    /**
     * Default layout for index.
     */
    const LAYOUT = 'annual';
    
    /**
     * Holds the name of the attorney role.
     *
     * @const
     */
    const NAMEOFATTORNEYROLE = 'attorney';

    /**
     * Holds the bean type to apply scaffolding to.
     *
     * @var string
     */
    public $type = 'card';
    
    /**
     * Holds the alias for the bean type to apply pageflip to.
     *
     * @var string
     */
    public $typeAlias = 'annual';
    
    /**
     * Displays a page with a (paginated) selection of beans.
     *
     * @todo maybe override env() because we reset so much anyway
     *
     * @param int $page
     * @param int $limit
     * @param string $layout
     * @param int $order
     */
    public function index($page = 1, $limit = self::LIMIT, $layout = self::LAYOUT, $order = 0, $dir = 0)
    {
        $this->cache()->deactivate();
        
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/'.$this->router()->internalUrl())));
        
        if ( ! $this->permission()->allowed($this->user(), $this->type, 'index')) {
			return $this->error('403');
		}

        $this->path = 'annual/';

        $this->env($page, $limit, $layout, $order, $dir, null, 'index');
        $this->view->title = __('annual_head_title_'.$this->view->action);
        $this->view->nav = R::findOne('domain', ' blessed = ? LIMIT 1', array(1))->hierMenu($this->view->url());
        $this->view->urhere = with(new Cinnebar_Menu())->add(__('annual_head_title'), $this->view->url('/annual'));
        
        if ( ! isset($_SESSION['annual'])) {
            $_SESSION['annual'] = array(
                'year' => date('Y'),
                'month' => date('m'),
                'attorney' => null,
                'team' => R::dispense('user')->current()->getFirstTeam()->name,
                'status' => null
            );
        }
        
        $this->trigger('index', 'before');
        
        
        if ($dialog = $this->input()->post('dialog')) {
            $_SESSION['annual'] = array(
                'year' => $dialog['year'],
                'month' => $dialog['month'],
                'attorney' => $dialog['attorney'],
                'team' => $dialog['team'],
                'status' => $dialog['status']
            );
            $this->trigger('index', 'after');
            $this->redirect('/annual/index/');
        }
        
        $this->view->year = $_SESSION['annual']['year'];
        $this->view->month = $_SESSION['annual']['month'];
        $this->view->attorney = $_SESSION['annual']['attorney'];
        $this->view->team = $_SESSION['annual']['team'];
        $this->view->status = $_SESSION['annual']['status'];
        
        $this->collection();
        
        /*
        $this->view->pagination = new Cinnebar_Pagination(
            $this->view->url(sprintf('/%s/index/', $this->type)),
            $this->page,
            $this->limit,
            $this->layout,
            $this->order,
            $this->dir,
            $this->view->total
        );
        */
        $this->trigger('index', 'after');
        
        echo $this->view->render();
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
    	list($whereClause, $tokens) = $this->buildWhereClause();
		$orderClause = $this->view->attributes[$this->order]['orderclause'].' '.$this->sortdir($this->dir);
		
		/*
		$tokens = array(
            $_SESSION['annual']['year'],
            $_SESSION['annual']['month']
		);
		if ( $_SESSION['annual']['attorney'] && $_SESSION['annual']['team'] ) {
		    $sql = $this->view->record->sqlForAnnuityComplete();
		    $tokens[] = $_SESSION['annual']['attorney'];
		    $tokens[] = '%'.$_SESSION['annual']['team'].'%';
		} elseif ( $_SESSION['annual']['attorney'] && ! $_SESSION['annual']['team']) {
		    $tokens[] = $_SESSION['annual']['attorney'];
		    $sql = $this->view->record->sqlForAnnuityAttorney();
		} elseif ( $_SESSION['annual']['team'] && ! $_SESSION['annual']['attorney'] ) {
		    $tokens[] = '%'.$_SESSION['annual']['team'].'%';
		    $sql = $this->view->record->sqlForAnnuityTeam();
		} else {
		    $sql = $this->view->record->sqlForAnnuityNone();
		}
		*/
		
		$sql = $this->view->record->sqlForAnnuity($whereClause);

		
		$this->view->total = 0;
		
		try {
			//R::debug(true);
			$assoc = R::$adapter->getAssoc($sql, $tokens);
			//R::debug(false);
			$this->view->records = R::batch($this->type, array_keys($assoc));
			//R::debug(true);
            $this->view->total = count($this->view->records);//R::getCell($this->view->record->sqlForTotal($whereClause), $this->view->filter->filterValues());
			//R::debug(false);
			//error_log(count($this->records));
			return true;
		} catch (Exception $e) {
            Cinnebar_Logger::instance()->log('Annual Collection has issues: '.$e.' '.$sql, 'sql');
			$this->view->records = array();
			return false;
		}    
    }
    
    /**
     * Builds the where clause for annual index.
     *
     * @return string
     */
    public function buildWhereClause()
    {
        if ( empty($_SESSION['annual'])) return '1';
        
        $where = '';
        $values = array();
        
        $where .= ' card.feeinactive = 0 ';
        
        if ( $_SESSION['annual']['year'] ) {
            $where .= ' AND YEAR(card.feeduedate) <= ? ';
            $values[] = $_SESSION['annual']['year'];
        };
        if ( $_SESSION['annual']['month'] ) {
            $where .= ' AND MONTH(card.feeduedate) <= ? ';
            $values[] = $_SESSION['annual']['month'];
        };
        if ( $_SESSION['annual']['attorney'] ) {
            $where .= ' AND card.user_id = ? ';
            $values[] = $_SESSION['annual']['attorney'];
        };
        if ( $_SESSION['annual']['team'] ) {
            $where .= ' AND card.teammashup LIKE ? ';
            $values[] = '%' . $_SESSION['annual']['team'] . '%';
        };
        if ( $_SESSION['annual']['status'] ) {
            $where .= ' AND card.status = ? ';
            $values[] = $_SESSION['annual']['status'];
        };
        //$where = implode(' ', $where);
        return array($where, $values);
    }
    
    /**
     * This will run before scaffold add performs.
     *
     * @return void
     */
    public function before_index()
    {
        $this->pushMonthsToView();
        $this->pushPossibleYearsToView();
        $this->pushEnabledAttorneysToView();
        $this->pushStatiToView();
    }
    
    /**
     * Pushes enabled attorney in alphabetic order to the view.
     */
    public function pushEnabledAttorneysToView()
    {
        $attorney = R::findOne('role', ' name = ? LIMIT 1', array(self::NAMEOFATTORNEYROLE));
        $this->view->attorneys = R::dispense('user')->belongsToRole($attorney->getId());
    }
    
    /**
     * Pushes enabled cardtypes in alphabetic order to the view.
     */
    public function pushPossibleYearsToView()
    {
        $this->view->years = $this->view->record->possibleFeeYears();
    }

    /**
     * Pushes months to view.
     */
    public function pushMonthsToView()
    {
        $this->view->months = array(
            '01' => __('month_label_01'),
            '02' => __('month_label_02'),
            '03' => __('month_label_03'),
            '04' => __('month_label_04'),
            '05' => __('month_label_05'),
            '06' => __('month_label_06'),
            '07' => __('month_label_07'),
            '08' => __('month_label_08'),
            '09' => __('month_label_09'),
            '10' => __('month_label_10'),
            '11' => __('month_label_11'),
            '12' => __('month_label_12')
        );
    }
    
    /**
     * Pushes stati to view.
     */
    public function pushStatiToView()
    {
        $this->view->stati = array(
            'due' => __('annual_label_due'),
            'inactive' => __('annual_label_inactive'),
            'onhold' => __('annual_label_onhold'),
            'done' => __('annual_label_done'),
            'paid' => __('annual_label_paid'),
            'billed' => __('annual_label_billed'),
            'ordered' => __('annual_label_ordered'),
            'awareness' => __('annual_label_awareness'),
            'maintain' => __('annual_label_maintain')
        );
    }
}
