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
 * The multipay model class.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Multipay extends Cinnebar_Model
{
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
        				'orderclause' => 'multipay.name',
        				'class' => 'text'
        			)
        		);
        }
        return $ret;
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
	 * update.
	 */
	public function update()
	{
        parent::update();
	}
	
	/**
	 * dispense.
	 */
	public function dispense()
	{
	    if ( ! $this->bean->getId() ) {
	        $this->bean->name = R::dispense( 'user' )->current()->name . ' ' . __('from') . ' ' . date( 'd-m-Y');
	        $this->bean->sent = FALSE;
	    }
	}
	
	/**
	 * Returns a menu object.
	 *
	 * @param string $action
	 * @param Cinnebar_View $view
	 * @param Cinnebar_Menu (optional) $menu
	 * @return Cinnebar_Menu
	 */
	public function makeMenu($action, Cinnebar_View $view, Cinnebar_Menu $menu = null)
	{
        $menu = new Cinnebar_Menu();
        $layouts = $this->layouts();
        if (count($layouts) > 1) {
            foreach ($layouts as $layout) {
                $menu->add(__('layout_'.$layout), $view->url(sprintf('/%s/index/%d/%d/%s/%d/%d', $this->bean->getMeta('type'), 1, Controller_Scaffold::LIMIT, $layout, $view->order, $view->dir)), 'scaffold-layout');
            }
        }
        $menu->add(__('scaffold_add'), $view->url(sprintf('/%s/add', $this->bean->getMeta('type'))), 'scaffold-add');
        if ( $this->bean->getId() ) {
            $menu->add(__('multipay_xml'), $view->url(sprintf('/%s/xmltool/%d', $this->bean->getMeta('type'), $this->bean->getId() )), 'multipay_xml_download');
        }
        $menu->add(__('scaffold_browse'), $view->url(sprintf('/%s/index', $this->bean->getMeta('type'))), 'scaffold-browse');
        return $menu;
	}
}
