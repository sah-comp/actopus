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
 * Converter to turn a user file upload into a filename.
 *
 * @package Cinnebar
 * @subpackage Converter
 * @version $Id$
 */
class Converter_Fileupload extends Cinnebar_Converter
{
    /**
     * Moves a user uploaded file into the upload directory.
     *
     * @uses Cinnebar_Upload to handle the uploaded file
     * @param mixed $cfilename is the current filename 
     * @return mixed $filename or null
     */
    public function execute($cfilename)
    {
        $upload = new Cinnebar_Upload();
        $filename = $upload->get($this->options['container'], $this->options['extensions'], $cfilename);
        if ($filename && $upload->unchanged()) {
            return $filename;
        }
        elseif ($filename && ! $upload->hasErrors()) {
            $this->bean()->dir = $upload->dir;
            $this->bean()->basename = $upload->sanitizedFilename;
            //if ( ! $this->bean()->name) $this->bean()->name = $this->bean()->basename;
            $this->bean()->extension = $upload->extension;
            $this->bean()->filesize = $upload->filesize;
            return $filename;
        }
        $this->bean()->addError(__('upload_error'), $this->options['container']);
        return null;
    }
}
