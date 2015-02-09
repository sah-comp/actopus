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
 * The hash class of the cinnebar system.
 *
 * This class is super simple and it is much better to use phpass, which can be configured in
 * your Cinnebar installations config.php file. See {@link config.example.php} for more information
 * about how to activate phpass, which is recommended over this class.
 *
 * @package Cinnebar
 * @subpackage Hash
 * @version $Id$
 */
class Cinnebar_Hash
{
    /**
     * Holds the algorithm for hashing.
     *
     * @var string
     */
    public $hash_algo;
    
    /**
     * Holds the salt for our hash.
     *
     * @todo Implement a much better default hash thingy
     * @var string
     */
    public $salt = '&5889Hghgjhj5%&%/ftddsop==9987897';

    /**
     * Constructor.
     *
     * @param string $hash_algo
     */
    public function __construct($hash_algo = null)
    {
        if (null === $hash_algo) $hash_algo = 'md5';
        $this->hash_algo = $hash_algo;
    }
    
    /**
     * Returns a salted hash for a given string.
     *
     * @param string $pw
     * @return string $hash
     */
    public function HashPassword($pw)
    {
        $callback = $this->hash_algo;
        return $callback($this->salt.$pw);
    }
    
    /**
     * Returns wether the password given matches the stored password.
     *
     * @param string $pw
     * @param string $pw_stored
     * @return bool $WetherThePasswordMatchesOrNot
     */
    public function CheckPassword($pw, $pw_stored)
    {
        return ($this->HashPassword($pw) == $pw_stored);
    }
}
