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
 * Hello World controller.
 *
 * This is a template-less Hello World controller, the smallest controller that can be.
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_Helloworld extends Cinnebar_Controller
{
    /**
     * Displays 'Hello World'.
     */
    public function index()
    {
        //$this->cache()->deactivate();
        echo 'Hello World';
    }
}
