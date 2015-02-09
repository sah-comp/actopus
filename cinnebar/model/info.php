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
 * Manages information on other beans.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Info extends Cinnebar_Model
{
    /**
     * Returns a user bean (might be empty).
     *
     * @return RedBean_OODBBean $user
     */
    public function user()
    {
        if ( ! $this->bean->user) return R::dispense('user');
        return $this->bean->user;
    }
    
    /**
     * dispense.
     */
    public function dispense()
    {
        $this->action = 'edit';
    }
}
