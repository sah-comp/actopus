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
 * Welcome controller.
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_Welcome extends Cinnebar_Controller
{
    /**
     * Displays the welcome page.
     */
    public function index()
    {
        $this->cache()->deactivate();
        $view = $this->makeView(sprintf('welcome/%s/index', $this->router()->language()));
        echo $view->render();
    }
}
