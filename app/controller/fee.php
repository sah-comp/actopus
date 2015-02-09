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
 * Manages CURD on fee beans.
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_Fee extends Controller_Scaffold
{
    /**
     * Holds the bean type to apply scaffolding to.
     *
     * @var string
     */
    public $type = 'fee';
    
    /**
     * Container for rule style partials to use in a fee form.
     *
     * @var array
     */
    public $rulestyles = array(
        0 => 'limit',
        1 => 'perpetual'
    );
    
    /**
     * Container for multipliers.
     *
     * @var array
     */
    public $multipliers = array(
        'none',
        'patterncount'
    );
    
    /**
     * Displays a partial dialog to set up feesteps according to the given rule.
     *
     * This is called from a ajax post request.
     *
     * @param int $fee_id of the fee
     * @param int $rule_id of the rule
     * @return void
     */
    public function rule($fee_id, $rule_id)
    {
        session_start();
        $this->cache()->deactivate();
        $this->view = $this->makeView(null);
        $fee = R::load('fee', $fee_id);
        // load the rule ...
        $rule = R::load('rule', $rule_id);
        $this->view->fee = $fee;
        $this->view->rule = $rule;
        if ($rule->getId()) {
            $this->pushMultipliersToView();
            echo $this->view->partial(sprintf('model/fee/form/rule/%s', $this->rulestyles[$rule->style]));
        } else {
            echo $this->view->partial('model/fee/form/rule/undefined');
        }
        return;
    }

    /**
     * This will run before scaffold edit performs.
     *
     * @return void
     */
    public function before_edit()
    {
        $this->pushEnabledPricetypesToView();
        $this->pushEnabledRulesToView();
        $this->pushRulestylesToView();
        $this->pushMultipliersToView();
    }
    
    /**
     * This will run before scaffold add performs.
     *
     * @return void
     */
    public function before_add()
    {
        $this->pushEnabledPricetypesToView();
        $this->pushEnabledRulesToView();
        $this->pushRulestylesToView();
        $this->pushMultipliersToView();
    }
    
    /**
     * Pushes the rulestyles array to the view.
     */
    public function pushRulestylesToView()
    {
        $this->view->rulestyles = $this->rulestyles;
    }
    
    /**
     * Pushes the multipliers array to the view.
     */
    public function pushMultipliersToView()
    {
        $this->view->multipliers = $this->multipliers;
    }
    
    /**
     * Pushes enabled pricetypes in alphabetic order to the view.
     */
    public function pushEnabledPricetypesToView()
    {
        $this->view->pricetypes = R::find('pricetype', ' 1 ORDER BY name');
    }
    
    /**
     * Pushes enabled rules in alphabetic order to the view.
     */
    public function pushEnabledRulesToView()
    {
        $this->view->rules = R::find('rule', ' 1 ORDER BY name');
    }
}
