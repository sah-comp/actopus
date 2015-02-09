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
 * Textile module will render a string as textile in frontend mode.
 *
 * @package Cinnebar
 * @subpackage Module
 * @version $Id$
 */
class Module_Textile extends Cinnebar_Module
{
    /**
     * Renders the slice bean in frontend mode.
     */
    public function renderFrontend()
    {
        return $this->view()->partial('module/textile/frontend', array('slice' => $this->bean()));
    }

    /**
     * Renders the slice bean in backend mode.
     */
    public function renderBackend()
    {
        return $this->view()->partial('module/textile/backend', array('slice' => $this->bean()));
    }
}
