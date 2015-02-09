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
 * Text module will render a string as Text-Only.
 *
 * @package Cinnebar
 * @subpackage Module
 * @version $Id$
 */
class Module_Text extends Cinnebar_Module
{
    /**
     * Renders the slice bean in frontend mode.
     */
    public function renderFrontend()
    {
        return $this->view()->partial('module/text/frontend', array('slice' => $this->bean()));
    }

    /**
     * Renders the slice bean in backend mode.
     */
    public function renderBackend()
    {
        return $this->view()->partial('module/text/backend', array('slice' => $this->bean()));
    }
}
