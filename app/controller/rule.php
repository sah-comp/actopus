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
 * Manages CURD on rule beans.
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_Rule extends Controller_Scaffold
{
    /**
     * Holds the bean type to apply scaffolding to.
     *
     * @var string
     */
    public $type = 'rule';

    /**
     * This will run before scaffold edit performs.
     *
     * @return void
     */
    public function before_edit()
    {
        $this->pushEnabledCardtypesToView();
        $this->pushEnabledCountriesToView();
    }
    
    /**
     * This will run before scaffold add performs.
     *
     * @return void
     */
    public function before_add()
    {
        $this->pushEnabledCardtypesToView();
        $this->pushEnabledCountriesToView();
    }
    
    /**
     * Pushes enabled templates in alphabetic order to the view.
     */
    public function pushEnabledCountriesToView()
    {
        $this->view->countries = R::find('country', ' enabled = 1 ORDER BY name');
    }

    /**
     * Pushes enabled cardtypes in alphabetic order to the view.
     */
    public function pushEnabledCardtypesToView()
    {
        $this->view->cardtypes = R::find('cardtype', ' 1 ORDER BY name');
    }
}
