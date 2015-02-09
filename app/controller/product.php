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
 * Manages CURD on product beans.
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_Product extends Controller_Scaffold
{
    /**
     * Holds the bean type to apply scaffolding to.
     *
     * @var string
     */
    public $type = 'product';
}
