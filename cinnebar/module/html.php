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
 * HTML module will render a string as pure HTML.
 *
 * @package Cinnebar
 * @subpackage Module
 * @version $Id$
 */
class Module_Html extends Cinnebar_Module
{
    /**
     * Renders the slice bean in frontend mode.
     */
    public function renderFrontend()
    {
        return $this->view()->partial('module/html/frontend', array('slice' => $this->bean()));
    }

    /**
     * Renders the slice bean in backend mode.
     */
    public function renderBackend()
    {
        return $this->view()->partial('module/html/backend', array('slice' => $this->bean()));
    }
}
