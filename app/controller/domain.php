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
 * Manages CURD on domain beans.
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_Domain extends Controller_Scaffold
{
    /**
     * Holds the bean type to apply scaffolding to.
     *
     * @var string
     */
    public $type = 'domain';
    
    /**
     * This will run before scaffold edit performs.
     *
     * @return void
     */
    public function before_edit()
    {
        $this->pushDomainsToView();
    }
    
    /**
     * This will run before scaffold add performs.
     *
     * @return void
     */
    public function before_add()
    {
        $this->pushDomainsToView();
    }
    
    /**
     * Pushes role beans to the view.
     *
     * @return void
     */
    protected function pushDomainsToView()
    {
        $this->view->domains = R::findAll('domain', 'ORDER BY name');
    }
}
