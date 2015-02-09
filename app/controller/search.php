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
 * Global Search.
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_Search extends Cinnebar_Controller
{
    /**
     * Container for models to search.
     *
     * @var array
     */
    public $types = array(
        'card',
        'priority',
        'person'
    );

    /**
     * Renders the Cinnebar search page.
     *
     * If there is no currently authorized user session the client will be redirected to
     * the login page which may return here after successful login.
     *
     * @uses auth() to check if there is a valid user account
     * @uses Cinnebar_Cache::deactivate() to turn off caching of the welcome page
     * @uses Cinnebar_Controller::makeView() to factory the view
     * @uses Cinnebar_View to assign values, render the view and so on
     * @param bool $phonetics
     * @return void
     */
    public function index($phonetics = true)
    {
        session_start();
        $this->cache()->deactivate();
        if ( ! $this->auth()) $this->redirect(sprintf('login/index/?goto=%s', urlencode('search/index')));
        
        $view = $this->makeView('search/index');
        $view->title = __('gsearch_head_title');
        
        $tags = $view->q = $this->input()->get('q');
        $view->user = $this->user();
        
        /*
		$pattern = array(
			' ',
			', '
		);
		$replace = array(
			',',
			','
		);
		$tags = str_replace($pattern, $replace, $view->q);
		$current_tags = explode(',', $tags);
		*/
		/*
		if ($phonetics) {
			$additional_tags = array();
			foreach ($current_tags as $tag) {
				$additional_tags[] = soundex($tag);
			}
			$tags .= ','.implode(',', $additional_tags);
		}
        */
        $records = array();
        $num_found = 0;
        
        if ( ! empty($tags)) {
    		foreach ($this->types as $type) {
    		    $fbean = R::dispense($type);
    		    $records[$type] = $fbean->searchAllFields($tags);
    			//$records[$type] = R::tagged($type, $tags);
    			$num_found += count($records[$type]);
    		}
    	}
        $view->records = $records;
        $view->num_found = $num_found;
        
        $view->nav = R::findOne('domain', ' blessed = ? LIMIT 1', array(1))->hierMenu($view->url());
        $view->urhere = with(new Cinnebar_Menu())->add(__('gsearch_head_title'), $view->url('/search'));        
        
        echo $view->render();
    }
    
    /**
     * Lookup term of jQuery autocomplete requests and returns results json encoded.
     *
     * @param string $type of bean to search
     * @param string (optional) $layout of the data to return
     * @return string $jsonEncodedArray
     */
    public function autocomplete($type, $layout = 'default')
    {    
        $this->cache()->deactivate();
        $bean = R::dispense($type);
        $result = $bean->clairvoyant($this->input()->get('term'), $layout);
        $this->response()->addHeader('Content-Type', 'application/json; charset=utf-8');
        echo $this->input()->get('callback').'('. json_encode($result) .')';
    }
}