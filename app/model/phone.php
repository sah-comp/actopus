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
 * Manages phone.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Phone extends Cinnebar_Model
{    
    /**
     * Returns an key/value array with contact infos for this bean.
     *
     * @return array $arrayOfContactInfos
     */
    public function contactInfos()
    {
        return array(
            'phone',
            'work',
            'home',
            'fax'
        );
    }
}