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
 * Manages CURD on person beans.
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_Person extends Controller_Scaffold
{
    /**
     * Holds the bean type to apply scaffolding to.
     *
     * @var string
     */
    public $type = 'person';
    
    /**
     * This will run before scaffold edit performs.
     *
     * @return void
     */
    public function before_edit()
    {
        $this->pushEnabledUsersToView();
        $this->pushEnabledLanguagesToView();
        $this->pushEnabledPricetypesToView();
    }
    
    /**
     * This will run before scaffold add performs.
     *
     * @return void
     */
    public function before_add()
    {
        $this->pushEnabledUsersToView();
        $this->pushEnabledLanguagesToView();
        $this->pushEnabledPricetypesToView();
    }
    
    /**
     * Pushes enabled attorney in alphabetic order to the view.
     */
    public function pushEnabledUsersToView()
    {
        $this->view->users = R::find('user', ' deleted = 0 AND banned = 0 ORDER BY name');
    }
    
    /**
     * Pushes enabled pricetypes to the view.
     */
    public function pushEnabledPricetypesToView()
    {
        $this->view->pricetypes = R::findAll('pricetype', ' ORDER BY name');
    }
}
