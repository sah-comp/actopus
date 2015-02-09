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
 * Provides some useful methods to handle file uploads.
 *
 * @package Cinnebar
 * @subpackage Core
 * @version $Id$
 */
class Cinnebar_Upload extends Cinnebar_Element
{
    /**
     * Container for upload configuration.
     *
     * @var array
     */
    public $config;

    /**
     * Holds the raw filename.
     *
     * @var string
     */
    public $filename;
    
    /**
     * Holds the sanitized filename.
     *
     * @var string
     */
    public $sanitizedFilename;
    
    /**
     * Holds the uploaded file exentsion.
     *
     * @var string
     */
    public $extension;

    /**
     * Holds the path to upload dir.
     *
     * @var string
     */
    public $dir;
    
    /**
     * Flag to indicate that an existing file was not replaced by a new upload.
     *
     * @var bool
     */
    public $unchanged = false;

    /**
     * Constructor.
     *
     */
    public function __construct()
    {
        global $config;
        $this->config = $config['upload'];
    }
    
    /**
     * Returns wether the file was changed or not.
     *
     * @return bool
     */
    public function unchanged()
    {
        return $this->unchanged;
    }

    /**
     * Returns a user uploaded file.
     *
     * If there is already a current file and upload error is 4 the current file is returned.
     *
     * @param string (optional) $container is the file input name, defaults to 'upload'
     * @param mixed (optional) $allowedType defaults to null, which means any
     * @param mixed (optional) $cfilename is the current filename
     * @return string $filename
     */
    public function get($container = 'upload', $allowed = null, $cfilename = null)
    {
        if ( ! empty($cfilename) && $_FILES[$container]['error'] == 4) {
            $this->unchanged = true;
            return $cfilename;
        }
        if ($_FILES[$container]['error'] != 0) {
            $this->addError(__('upload_error_' . $_FILES[$container]['error']));
            return null;
        }
        $this->analyzeFilename($container);
        if ( ! $this->allowedExtension($allowed, $this->extension)) {
            $this->addError(__('upload_error_extension_not_allowed'));
            return null;
        }
        $pathtofile = $this->config['dir'].$this->sanitizedFilename.'.'.$this->extension;
        if ( ! move_uploaded_file($_FILES[$container]['tmp_name'], $pathtofile)) {
            $this->addError(__('upload_error_move_uploaded_file'));
            return null;
        }
        $this->dir = $this->config['dir'];
        $this->filesize = filesize($pathtofile);
        return $this->sanitizedFilename.'.'.$this->extension;
    }
    
    /**
     * Returns wether the uploaded file extension is allowed or not.
     *
     * @param mixed $allowed
     * @param string $extension
     * @return bool
     */
    public function allowedExtension($allowed, $extension)
    {
        if ($allowed === null) return true;
        if ( ! is_array($allowed)) $allowed = array($allowed);
        return (in_array($extension, $allowed));
    }
    
    /**
     * Analyzes the filename and extension of the uploaded file.
     *
     * @uses $filename
     * @uses $extension
     * @uses $sanitizedFilename
     * @param string (optional) $container is the file input name, defaults to 'upload'
     */
    protected function analyzeFilename($container = 'upload')
    {
        $file_parts = pathinfo($_FILES[$container]['name']);
        $this->filename = $file_parts['filename'];
        $this->extension = mb_strtolower($file_parts['extension']);
        $this->sanitizedFilename = $this->sanitizeFilename($this->filename);
    }
}
