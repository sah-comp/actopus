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
 * Manages priority.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Priority extends Cinnebar_Model
{
    /**
     * Look up searchtext in all fields of a bean.
     *
     * @param string $searchphrase
     * @return array
     */
    public function searchAllFields($searchphrase = '')
    {
        $searchphrase = '%'.$searchphrase.'%';
        return R::find('priority', ' number LIKE :f', array(':f' => $searchphrase));
    }

    /**
     * Returns a short text describing the bean for humans.
     *
     * @param Cinnebar_View $view
     * @return string
     */
    public function hitname(Cinnebar_View $view)
    {
        $template = '<a href="%s">%s</a>'."\n";
        $name = __('priority_hitname', array($this->bean->number, $this->bean->card->name), null, null, 'Needs placeholders for priority number and the card name');
        return sprintf($template, $view->url(sprintf('/%s/edit/%d', $this->bean->card->getMeta('type'), $this->bean->card->getId())), $name);
    }
  
    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        $this->addConverter('date', 'mySQLDate');
        $this->setAutoTag(true);
    }

    /**
     * Update.
     */
    public function update()
    {
        if ( ! $this->bean->country_id) $this->bean->country_id = null;
        parent::update();
    }
    
    /**
     * Returns keywords from this bean for tagging.
     *
     * @var array
     */
    public function keywords()
    {
        return array(
            $this->bean->number,
            $this->alphanumericonly($this->bean->number)
        );
    }
}