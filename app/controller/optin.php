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
 * Manages CURD on optin beans.
 *
 * A optin bean works in conjunction with the newsletter. It stores an email address that has
 * to be activated by a dbl-opt-in action within a certain time period if the address was sampled
 * through the newsletter controller performing the optin method.
 * Of course a optin can be entered by the admin of the system.
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_Optin extends Controller_Scaffold
{
    /**
     * Holds the bean type to apply scaffolding to.
     *
     * @var string
     */
    public $type = 'optin';
    
    /**
     * This will run before scaffold edit performs.
     *
     * @return void
     */
    public function before_edit()
    {
        $this->pushCampaignsToView();
    }
    
    /**
     * This will run before scaffold add performs.
     *
     * @return void
     */
    public function before_add()
    {
        $this->pushCampaignsToView();
    }
    
    /**
     * Pushes role beans to the view.
     *
     * @return void
     */
    protected function pushCampaignsToView()
    {
        $this->view->campaigns = R::findAll('campaign', 'ORDER BY name');
    }
}
