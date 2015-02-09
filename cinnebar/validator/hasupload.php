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
 * Validator to check if there is a file in your upload directory.
 *
 * @package Cinnebar
 * @subpackage Validator
 * @version $Id$
 */
class Validator_Hasupload extends Cinnebar_Validator
{
    /**
     * Returns wether a file is in your upload directory.
     *
     * @param mixed $filename
     * @return bool $filenameIsInUploadDirOrNot
     */
    public function execute($filename)
    {
        global $config;
        $filename = $config['upload']['dir'].$filename;
        return is_file($filename);
    }
}
