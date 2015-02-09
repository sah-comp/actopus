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
 * The sitemap viewhelper class of the cinnebar system.
 *
 * The unordered list rendered will become a sitemap tree view using the jQuery
 * code from {@link http://boagworld.com/dev/creating-a-draggable-sitemap-with-jquery/}.
 *
 * Usage example within a template:
 * <code>
 * <?php
 * echo $this->sitemap($menu);
 * ?>
 * </code>
 *
 * @package Cinnebar
 * @subpackage Viewhelper
 * @version $Id$
 */
class Viewhelper_Sitemap extends Cinnebar_Viewhelper
{
    /**
     * Renders an unordered list as an jQuery idTabs menu.
     *
     * @param RedBean_OODBBean $menu
     * @return string $htmlWithUnorderedList
     */
    public function execute(Cinnebar_Menu $menu)
    {
        $this->view()->addJs('libs/jquery/hs_draggable');
        $this->view()->sitemap = $menu;
        return $this->view()->partial('shared/viewhelper/sitemap');
    }
}