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
 * The Memory sessionhandler class.
 *
 * This sessionhandler uses PHP memory to handle user sessions. While is it of no use for
 * production sites or applications it will be useful in unit and integration tests.
 *
 * @package Cinnebar
 * @subpackage Sessionhandler
 * @version $Id$
 */
class Sessionhandler_Memory extends Cinnebar_Sessionhandler
{
    /**
     * Container for sessions.
     *
     * @var array
     */
    public $sessions = array();

	/**
	 * Opens a new session.
	 *
	 * @param string $path
	 * @param string $id
	 * @return bool
	 */
	public function open($path, $id)
    {
        return true;
    }
    
	/**
	 * Closes the session.
	 *
	 * @return bool
	 */
    public function close()
	{
	    return true;
	}
    
	/**
	 * Returns the session data or an empty string.
	 *
	 * @param string $id
	 * @return string
	 */
	public function read($id)
	{
        if ( ! isset($this->sessions[$id])) return '';
        return $this->sessions[$id];
	}
	
	/**
	 * Writes the session to APC user values.
	 *
	 * @param string $id
	 * @param string $data
	 * @return bool
	 */
    public function write($id, $data)
	{
	    $this->sessions[$id] = $data;
        return true;
	}

	/**
	 * Deletes the session record from APC user values.
	 *
	 * @param string $id
	 * @return bool
	 */
    public function destroy($id)
	{
        if (isset($this->sessions[$id])) unset ($this->sessions[$id]);
        return true;
	}

	/**
	 * Perform a garbage collection for outdated sessions.
	 *
	 * @param int $max_lifetime
	 * @return bool
	 */
    public function gc($max_lifetime)
	{
        return true;
	}
}
