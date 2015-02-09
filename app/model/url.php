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
 * Manages url.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Url extends Cinnebar_Model
{    
    /**
     * Returns an key/value array with contact infos for this bean.
     *
     * @return array $arrayOfContactInfos
     */
    public function contactInfos()
    {
        return array(
            'url',
            'work',
            'home',
            'other'
        );
    }
}