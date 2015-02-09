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
 * The beanlink viewhelper class of the cinnebar system.
 *
 * The beanlink viewhelper will render a link to the given, if it exists already.
 *
 * @package Cinnebar
 * @subpackage Viewhelper
 * @version $Id$
 */
class Viewhelper_Beanlink extends Cinnebar_Viewhelper
{
    /**
     * Holds the template string for a table row entry.
     *
     * @var string
     */
    public $template = '<a class="ir beanlink" href="%s">%s</a>';

    /**
     * Either renders a link or nothing.
     *
     * @param RedBean_OODBBean $bean
     * @param string $attribute is the name of the bean attribute to display as linktext
     * @return mixed
     * @throws new exception when $attributes array is empty
     */
    public function execute(RedBean_OODBBean $record, $attribute)
    {
        if ( ! $record->getId()) return null;
        return sprintf($this->template, $this->view()->url(sprintf('/%s/edit/%d', $record->getMeta('type'), $record->getId())), $record->$attribute);
    }
}
