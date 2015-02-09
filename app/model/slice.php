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
 * Manages slice.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Slice extends Cinnebar_Model
{    
    /**
     * Returns an key/value array with contact infos for this bean.
     *
     * @return array $arrayOfContactInfos
     */
    public function modes()
    {
        return array(
            'textonly',
            'textile',
            'html'
        );
    }
    
    /**
     * Returns a string where this bean was rendered into a model template.
     *
     * @param Cinnebar_View $view
     * @param bool $mode true = backend mode, false = frontend mode
     * @return string
     */
    public function render(Cinnebar_View $view, $backend = false)
    {
        if (empty($this->bean->mode)) $this->bean->mode = 'text';
        $moduleName = 'Module_'.ucfirst(strtolower($this->bean->mode));
        if ( ! class_exists($moduleName, true)) {
            Cinnebar_Logger::instance()->log(sprintf('Module "%s" not found', $this->bean->mode), 'warn');
            exit(sprintf('Module "%s" not found', $this->bean->mode));            
        }
        $module = new $moduleName($view, $this->bean);
        $module->backend($backend); // set the rendering mode for the module
        return $module->execute();
    }
    
    /**
     * Dispense a new bean.
     *
     * @todo an empty slice is created when mode is preset (or any other attr) because of R::graph()
     */
    public function dispense()
    {
        $this->bean->sequence = null; // force this one
    }
}