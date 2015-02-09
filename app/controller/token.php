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
 * Manages CURD on token beans.
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_Token extends Controller_Scaffold
{
    /**
     * Holds the bean type to apply scaffolding to.
     *
     * @var string
     */
    public $type = 'token';
    
    /**
     * This will run before scaffold edit performs.
     *
     * @uses pushEnabledLanguagesToView()
     * @return void
     */
    public function before_edit()
    {
        $this->pushEnabledLanguagesToView();
    }
    
    /**
     * This will run before scaffold add performs.
     *
     * @uses pushEnabledLanguagesToView()
     * @return void
     */
    public function before_add()
    {
        $this->pushEnabledLanguagesToView();
    }
}
