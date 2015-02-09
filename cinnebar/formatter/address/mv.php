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
 * Formats a postal address for maledives (mv).
 *
 * See {@link http://www.upu.int/fileadmin/documentsFiles/activities/addressingUnit/mdvEn.pdf} on how
 * a postal address in the united kingdom should be formatted.
 *
 * @package Cinnebar
 * @subpackage Formatter
 * @version $Id$
 */
class Formatter_Address_MV extends Cinnebar_Formatter
{
    /**
     * Formats attributes of a bean.
     *
     * @param RedBean_OODBBean $bean to format
     * @return string $formattedString
     */
    public function execute(RedBean_OODBBean $bean)
    {
        return sprintf("%s\n%s %s\nMALEDIVES", mb_strtoupper($bean->street), mb_strtoupper($bean->city), mb_strtoupper($bean->zip));
    }
}
