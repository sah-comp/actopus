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
 * Convertes a value.
 *
 * To add your own converter simply add a php file to the converter directory of your Cinnebar
 * installation. Name the converter after the scheme Converter_* extends Cinnebar_Converter and
 * implement a execute() method. As an example see {@link Converter_MySQLDate}.
 *
 * Example usage of the MySQLDate converter:
 * <code>
 * <?php
 * $mysqldate = new Converter_MySQLDate();
 * $attr_date = $mysqldate('01.04.2011'); // will give you '2011-04-01'
 * ?>
 * </code>
 *
 * @package Cinnebar
 * @subpackage Converter
 * @version $Id$
 */
class Cinnebar_Converter
{
    /**
     * Holds the bean on which the container works.
     *
     * @var RedBean_OODBBean
     */
    public $bean;

    /**
     * Container for the converter options.
     *
     * @var array
     */
    public $options = array();

    /**
     * Constructor.
     *
     * @uses Cinnebar_Converter::$options
     * @param Redbean_OODBBean $bean
     * @param array (optional) $options
     */
    public function __construct(RedBean_OODBBean $bean, array $options = array()) {
        $this->bean = $bean;
        $this->options = $options;
    }
    
    /**
     * Returns the bean instance.
     *
     * @return RedBean_OODBBean
     */
    public function bean()
    {
        return $this->bean;
    }

    /**
     * Returns whatever the converters has converted the input to.
     *
     * @param mixed $value
     * @return mixed $convertedValue
     */
    public function execute($value)
    {
        return $value;
    }
}
