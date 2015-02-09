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
 * Manages a http response.
 *
 * @package Cinnebar
 * @subpackage Response
 * @version $Id$
 */
class Cinnebar_Response
{
    /**
     * Holds the headers for this response.
     *
     * @var array
     */
    public $headers = array();

    /**
     * Holds the replacements for this response.
     *
     * @var array
     */
    public $replacements = array();
    
    /**
     * Holds the response payload.
     *
     * @var string
     */
    public $payload = '';

    /**
     * Constructor.
     */
    public function __construct()
    {
    }
    
    /**
     * Starts an output buffer.
     *
     */
    public function start()
    {
		ob_start();
    }
    
    /**
     * Send all headers and return the payload as a string.
     *
     * @uses Cinnebar_Response::$payload
     * @uses replacements() to replace eventually tokens with values
     * @uses headers() to send headers
     * @return string $payload
     */
    public function flush()
    {
        $this->payload = ob_get_contents();
        $this->replacements();
		ob_end_clean();
		$this->headers();
        return $this->payload;
    }
    
    /**
     * Add a header to this response.
     *
     * Usage from a controller may look like this:
     * <code>
     * <?php
     * // ...
     * // ... your code within a controller method
     * // ...
     * $this->response->addHeader('X-CINNEBAR-GREETZ', 'Hello World');
     * // ...
     * // ...
     * ?>
     * </code>
     *
     * @uses Cinnebar_Response::$headers
     * @param string $header
     * @param string $content
     * @return bool $added
     */
    public function addHeader($header, $content)
    {
        $this->headers[$header] = $content;
        return true;
    }
    
    /**
     * Add a replacement token/value for this response.
     *
     * Replacements are strings in a template or payload of the response which are surrounded
     * by double curly brackets like this for example: {{memory_usage}}.
     *
     * The following replacement tokens are preset by {@link Cinnebar_Facade::run()}:
     * - memory_usage
     * - remote_addr
     * - execution_time
     *
     * Usage from a controller may look like this:
     * <code>
     * <?php
     * // ...
     * // ... your code within a controller method
     * // ...
     * $this->response->addReplacement('memory_usage', '320 MB');
     * // ...
     * // ...
     * ?>
     * </code>
     *
     * @uses Cinnebar_Response::$replacements
     * @param string $token
     * @param string $value
     * @return bool $added
     */
    public function addReplacement($token, $value)
    {
        $this->replacements[$token] = $value;
        return true;
    }
    
	/**
	 * send headers to the client.
	 *
     * @uses Cinnebar_Response::$headers
	 */
	public function headers()
	{
		foreach ($this->headers as $header=>$value)
		{
			header("{$header}: {$value}");
		}
	}
	
	/**
	 * Replaces all tokens like {{yourtoken}} in payload.
	 *
     * @uses Cinnebar_Response::$replacements
     * @uses Cinnebar_Response::$payload
	 * @return bool $replaced
	 */
	protected function replacements()
	{
	    if ( empty($this->replacements)) return false;
		foreach ($this->replacements as $key=>$value) {
			$needle = '{{'.$key.'}}';
			$this->payload = str_replace($needle, $value, $this->payload);
		}
		return true;
	}
}
