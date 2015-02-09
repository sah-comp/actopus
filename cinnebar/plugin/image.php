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
 * Outputs an media file resized (and cached) on the fly.
 *
 * @package Cinnebar
 * @subpackage Plugin
 * @version $Id$
 */
class Plugin_Image extends Cinnebar_Plugin
{
    /**
     * Renders an media file as an image using a certain size.
     *
     * @uses Cinnebar_Cache::deactivate() to turn off caching for the errornous URL
     * @uses Model_Media
     * @param int $id of the media bean to display as image
     * @param int (optional) $width
     * @param int (optional) $height
     * @param int (optional) $quality
     * @param bool (optional) $nocache set this to true and the image wont be cached
     * @return void
     */
    public function execute($id, $width = null, $height = null, $quality = 100, $nocache = false)
    {
        $this->controller()->cache()->deactivate();
        $media = R::load('media', $id);
        $extensions = $media->extensions();
        if ( ! $media->getId()) {
            return $this->empty_image($width, $height);
        }
        if ( ! isset($extensions[$media->extension])) {
            return $this->empty_image($width, $height);
        }
        // scale, based on either width or height or even scale both
        // if nocache if false, we cache the resized image
        $this->controller()->response()->addHeader('Content-Type', $extensions[$media->extension]);
        $this->controller()->response()->addHeader('Content-Length', $media->filesize);
        readfile($media->dir.$media->basename.'.'.$media->extension);
    }

    /**
     * Returns an empty image.
     *
     * @param int (optional) $width
     * @param int (optional) $height
     * @return void
     */
    protected function empty_image($width, $height)
    {
        echo 'noimg';
    }
}