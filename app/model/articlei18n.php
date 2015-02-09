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
 * Manages articlei18n.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Articlei18n extends Cinnebar_Model
{
    /**
     * Returns the template for this localized article.
     *
     * @return RedBean_OODBBean
     */
    public function template()
    {
        if ( ! $this->bean->template) $this->bean->template = R::dispense('template');
        return $this->bean->template;
    }
    
    /**
     * Return ownRegion(s).
     *
     * @param bool $add if true an empty records gets added.
     * @return array
     */
    public function getownRegion($add)
    {
        $own = R::find('region', ' articlei18n_id = ? ORDER BY sequence', array($this->bean->getId()));
        if ($add) $own[] = R::dispense('region');
        return $own;
    }

    /**
     * Returns regions for this localized article.
     *
     * @return array
     */
    public function regions()
    {
        return $this->template()->regions();
    }

    /**
     * update.
     */
    public function update()
    {
        if ( ! $this->bean->template_id) $this->bean->template_id = null;
        parent::update();
    }

    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        if ( ! $this->bean->template_id) $this->bean->template_id = null;        
    }
}
