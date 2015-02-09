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
 * Manages CURD on page beans.
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_Page extends Controller_Scaffold
{
    /**
     * Holds the bean type to apply scaffolding to.
     *
     * @var string
     */
    public $type = 'page';

    /**
     * This will run before scaffold edit performs.
     *
     * @return void
     */
    public function before_edit()
    {
        $this->pushEnabledLanguagesToView();
        $this->pushEnabledTemplatesToView();
    }
    
    /**
     * This will run before scaffold add performs.
     *
     * @return void
     */
    public function before_add()
    {
        $this->pushEnabledLanguagesToView();
        $this->pushEnabledTemplatesToView();
    }
    
    /**
     * Pushes enabled templates in alphabetic order to the view.
     */
    public function pushEnabledTemplatesToView()
    {
        $this->view->templates = R::find('template', ' enabled = 1 ORDER BY name');
    }
}
