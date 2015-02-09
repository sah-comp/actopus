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
 * Image module will render a string as image in frontend mode.
 *
 * @package Cinnebar
 * @subpackage Module
 * @version $Id$
 */
class Module_Image extends Cinnebar_Module
{
    /**
     * Renders the slice bean in frontend mode.
     */
    public function renderFrontend()
    {
        return $this->view()->partial('module/image/frontend', array('slice' => $this->bean()));
    }

    /**
     * Renders the slice bean in backend mode.
     */
    public function renderBackend()
    {
        $extensions = array('jpg', 'gif', 'png');
        $this->view()->medias = R::find('media', ' extension IN ('.R::genSlots($extensions).')', $extensions);
        return $this->view()->partial('module/image/backend', array('slice' => $this->bean()));
    }
}
