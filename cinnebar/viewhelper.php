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
 * The basic viewhelper class of the cinnebar system.
 *
 * To add your own viewhelper simply add a php file to the viewhelper directory of your Cinnebar
 * installation. Name the viewhelper after the scheme Viewhelper_* extends Cinnebar_Viewhelper and
 * implement a execute() method. You will not call a viewhelper directly, but you will use it from
 * a view or a template. As an example see {@link Viewhelper_Textile}.
 *
 * Example usage of the textile viewhelper in an template of a view:
 * <code>
 * <?php
 * echo $this->textile('h1. Hello _World_, how are _you_?');
 * ?>
 * </code>
 *
 * @package Cinnebar
 * @subpackage Viewhelper
 * @version $Id$
 */
class Cinnebar_Viewhelper
{
    /**
     * Holds the instance of the view in which this viewhelper runs.
     *
     * @var Cinnebar_View
     */
    public $view;

    /**
     * Constructor.
     * @param Cinnebar_View $view
     */
    public function __construct(Cinnebar_View $view)
    {
        $this->view = $view;
    }

    /**
     * Returns an instance of the view from which this helper was called.
     *
     * @return Cinnebar_View
     */
    public function view()
    {
        return $this->view;
    }
}
