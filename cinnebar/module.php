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
 * The basic module class of the cinnebar system.
 *
 * A module is used to render a bean (e.g. slice) in a certain fashion
 * where it may be rendered in view or edit style.
 *
 * @package Cinnebar
 * @subpackage Module
 * @version $Id$
 */
class Cinnebar_Module implements iModule
{
    /**
     * Switch to indicate wether to render in frontend or backend mode.
     *
     * @var bool
     */
    public $backend = false;

    /**
     * Container for the bean.
     *
     * @var RedBean_OODBBean
     */
    protected $bean;

    /**
     * Container for the view the module is working in.
     *
     * @var Cinnebar_View
     */
    protected $view;
    
    /**
     * Constructs a new instance of a module.
     *
     * @param Cinnebar_View $view is the view the module works in
     * @param RedBean_OODBean $bean is the bean that the module works on
     */
    public function __construct(Cinnebar_View $view, RedBean_OODBBean $bean)
    {
        $this->view = $view;
        $this->bean = $bean;
    }
    
    /**
     * Returns the instance of the view this modules is running in.
     *
     * @return Cinnebar_View
     */
    public function view()
    {
        return $this->view;
    }
    
    /**
     * Returns the instance of the bean this modules is running in.
     *
     * @return RedBean_OODBBean
     */
    public function bean()
    {
        return $this->bean;
    }
    
    /**
     * Sets the rendering mode to either backend or frontend.
     *
     * @param bool (optional) $switch
     */
    public function backend($switch = null)
    {
        if ( $switch !== null) $this->backend = $switch;
        return $this->backend;
    }

    /**
     * Execute the module by rendering either in back- or frontend mode.
     *
     * @return string
     */
    public function execute()
    {
        if ($this->backend()) return $this->renderBackend();
        return $this->renderFrontend();
    }
    
    /**
     * Renders the slice bean in frontend mode.
     */
    public function renderFrontend()
    {
        return 'Your code should render a slice bean in frontend mode.';
    }

    /**
     * Renders the slice bean in backend mode.
     */
    public function renderBackend()
    {
        return 'Your code should render a slice bean in backend mode.';
    }
}