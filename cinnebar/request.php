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
 * Manages a http request.
 *
 * @package Cinnebar
 * @subpackage Request
 * @version $Id$
 */
class Cinnebar_Request
{
    /**
     * String for HTTP protocol.
     *
     * @var string
     */
    const PROTOCOL_HTTP = '//';

    /**
     * String for HTTPS protocol.
     *
     * @var string
     */
    const PROTOCOL_HTTPS = '//';

    /**
     * Constructor.
     */
    public function __construct()
    {
    }

    /**
     * Returns the protocol of the request.
     *
     * @return string $protocol
     */
    public function protocol()
    {
        if (! isset($_SERVER['HTTPS']) || ! $_SERVER['HTTPS']) {
            return self::PROTOCOL_HTTP;
        }
        return self::PROTOCOL_HTTPS;
    }

    /**
     * Returns the host.
     *
     * @return string $host
     */
    public function host()
    {
        return $_SERVER['HTTP_HOST'];
    }

    /**
     * Returns a string with port if port differs from 80.
     *
     * @return string $port
     */
    public function port()
    {
        return '';
    }

    /**
     * Returns the clients request type, either post or get.
     *
     * @return string $getOrPost
     */
    public function getOrPost()
    {
        if (count($_POST) == 0) {
            return 'get';
        }
        return 'post';
    }

    /**
     * Returns the full URL of the request.
     *
     * @return string $url
     */
    public function url()
    {
        return $this->protocol().$this->host().$_SERVER['REQUEST_URI'];
    }

    /**
     * Returns true if the clients request was an ajax call.
     *
     * Checks the SERVER variable to see if this was an ajax initiated request.
     * Your controller can then decide wether to output a complete HTML page or
     * only a certain partial view.
     *
     * @return bool $isAjaxOrNormalHTTPRequest
     */
    public function isAjax()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
    }
}
