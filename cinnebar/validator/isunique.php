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
 * Validator to check if a bean unique key is really not yet stored.
 *
 * @package Cinnebar
 * @subpackage Validator
 * @version $Id$
 */
class Validator_Isunique extends Cinnebar_Validator
{
    /**
     * Checks if the value of a beans attribute exists only once in the database.
     *
     * @param mixed $value
     * @return bool $validOrInvalid
     */
    public function execute($value)
    {
        if ( ! isset($this->options['bean']) || ! isset($this->options['attribute']) || ! is_a($this->options['bean'], 'RedBean_OODBBean')) {
            throw new Exception('A unique validator needs type and attribute as parameters');
        }
        
        if ( $this->options['bean']->getId() &&
                ! $this->options['bean']->hasChanged($this->options['attribute'])) {
            return true;
        }
        if (R::findOne($this->options['bean']->getMeta('type'), $this->options['attribute'].' = ? LIMIT 1', array($value))) return false; // ... because you are not allowed to store it a second time
        return true;
    }
}
