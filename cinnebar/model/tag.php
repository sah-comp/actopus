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
 * Manages tags on other beans.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Tag extends Cinnebar_Model
{
    /**
     * Searches for given searchterm within bean and returns the result-set as an multi-dim array
     * after the given layout.
     *
     * @param string $term contains the searchterm as given by jQuery.autocomplete
     * @param string (optional) $layout defaults to "default"
     */
    public function clairvoyant($term, $layout = 'default')
    {   
        switch ($layout) {
            default:
                $sql = <<<SQL

                SELECT
                    id AS id,
                    title AS label

                FROM
                    tag

                WHERE
                    title like ?

                ORDER BY
                    title

SQL;
        }
        return $res = R::getAll($sql, array($term.'%'));
    }
}
