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
 * Class factory.
 *
 * Usage:
 * <code>
 * <?php
 * $menu = Cinnebar_Factory::make('menu'); // gives us a new Cinnebar_Menu instance
 * ?>
 * </code>
 *
 * @package Cinnebar
 * @subpackage Factory
 * @version $Id$
 */
class Cinnebar_Factory
{
    /**
     * Constructor.
     */
    public function __construct()
    {
    }
    
    /**
     * Returns a new instance of a class.
     *
     * @param string $class to instantiate
     * @param string $prefix of the class to instantiate, defaults to 'Cinnebar'
     * @return mixed
     * @throws 
     */
    public static function make($class, $prefix = 'Cinnebar')
    {
        $class_name = ucfirst(strtolower($prefix)).'_'.ucfirst(strtolower($class));
        if (class_exists($class_name)) return new $class_name();
        throw new Exception(sprint(__('Unable to make a new "%s"-class.'), $class_name));
    }
}
