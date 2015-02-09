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
 * Manages CURD on currency beans.
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_Currency extends Controller_Scaffold
{
    /**
     * Holds the bean type to apply scaffolding to.
     *
     * @var string
     */
    public $type = 'currency';
    
    /**
     * Before edit.
     */
    public function before_edit()
    {
        $this->pushSettingToView();
    }
    /**
     * Before add.
     */
    public function before_add()
    {
        $this->pushSettingToView();
    }
}
