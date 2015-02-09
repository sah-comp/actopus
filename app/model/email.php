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
 * Manages email.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Email extends Cinnebar_Model
{    
    /**
     * Returns an key/value array with contact infos for this bean.
     *
     * @return array $arrayOfContactInfos
     */
    public function contactInfos()
    {
        return array(
            'email',
            'work',
            'home'
        );
    }
}