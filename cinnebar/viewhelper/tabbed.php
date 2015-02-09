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
 * The tabbed viewhelper class of the cinnebar system.
 *
 * The unordered list rendered will become a tabbed interface using the jQuery
 * plugin idTabs {@link http://www.sunsean.com/idTabs/}.
 *
 * Usage example within a template:
 * <code>
 * <?php
 * echo $this->tabbed('tabs', array(
 *    'tab-1' => 'Sun',
 *    'tab-2' => 'Earth',
 *    'tab-3' => 'Moon'
 * ));
 * ?>
 * </code>
 *
 * @package Cinnebar
 * @subpackage Viewhelper
 * @version $Id$
 */
class Viewhelper_Tabbed extends Cinnebar_Viewhelper
{
    /**
     * Renders an unordered list as an jQuery idTabs menu.
     *
     * @param string $id of the html element that contains the tabbed navigation
     * @param array $tabs
     * @return string $htmlWithUnorderedList
     */
    public function execute($id, array $tabs = array())
    {
        $this->view()->addJs('libs/jquery/jquery.idTabs.min');
        $this->view()->tabbed = array(
            'id' => $id,
            'tabs' => $tabs
        );
        return $this->view()->partial('shared/viewhelper/tabbed');
    }
}