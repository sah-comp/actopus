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
 * Manages meta beans.
 *
 * A meta bean holds meta information about other beans. This starts with keywords, descriptions
 * and ends with alternative representations, file downloads and so on.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Meta extends Cinnebar_Model
{
    /**
     * update.
     *
     * This will check for file uploads.
     */
    public function update()
    {
        parent::update();
    }
}