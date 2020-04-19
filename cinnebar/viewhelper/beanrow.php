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
 * The beanrow viewhelper class of the cinnebar system.
 *
 * The beanrow viewhelper will render a all table data cells according to the given attributes
 * array using a bean.
 *
 * @package Cinnebar
 * @subpackage Viewhelper
 * @version $Id$
 */
class Viewhelper_Beanrow extends Cinnebar_Viewhelper
{
    /**
     * Holds the template string for a table row entry.
     *
     * @var string
     */
    public $template = '<td class="%s">%s</td>';

    /**
     * Renders a table row.
     *
     * @param RedBean_OODBBean $bean
     * @param array $attributes is an array with fieldnames to be rendered with the given bean
     * @param bool $trusted is a switch to surpress htmlspecialchars
     * @return string
     * @throws new exception when $attributes array is empty
     */
    public function execute(RedBean_OODBBean $record, array $attributes = array(), $trusted = false)
    {
        if ( empty($attributes)) throw new Exception('Viewhelper_Beanrow cant handle empty attribute array');
        $s = '';
        foreach ($attributes as $n => $attribute) {
            $class = '';
            $content = '';
            if (isset($attribute['class'])) $class .= $attribute['class'];
            if (isset($attribute['viewhelper'])) {
                $content = $this->view->{$attribute['viewhelper']}($record->{$attribute['attribute']});
            } elseif (isset($attribute['callback'])) {
                if (is_array($attribute['callback'])) {
                    $content = $record->{$attribute['callback']['name']}($attribute['attribute']);                
                } else {
                    $content = $record->{$attribute['callback']}($attribute['attribute']);                
                }
            } else {
                $content = $record->{$attribute['attribute']};
            }
            if ( ! $trusted) $content = htmlspecialchars($content);
            $s .= sprintf($this->template, $class, $content)."\n";
        }
        return $s;
    }
}
