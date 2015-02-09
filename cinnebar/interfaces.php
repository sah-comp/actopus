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
  * Interface for language model.
  *
  * @package Cinnebar
  * @subpackage Model
  * @version $Id$
  */
interface iLanguage
{
    /**
     * Returns all enabled languages.
     *
     * @return array of language beans
     */
    public function enabled();
}

/**
 * Interface for token model.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
interface iToken
{
    /**
     * Returns a translation of the token for the given language.
     *
     * @param string $iso code of the wanted translation language
     * @return RedBean_OODBBean $translation
     */
    public function in($iso = 'de');
}

/**
 * Interface for permission.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
interface iPermission
{

    /**
     * Returns wether user is allowed to do action on domain or not.
     *
     * @param mixed $user
     * @param string $domain
     * @param string $action
     * @return bool
     */
    public function allowed($user = null, $domain, $action);

    /**
     * returns an key/value array of all domains where user can do action.
     *
     * @param mixed $user
     * @param string $action
     * @return array
     */
    public function domains($user, $action);

    /**
     * Loads the users permissions and caches them in users session.
     *
     * @param mixed $user
     */
    public function load($user = null);
}

/**
 * Interface for modules.
 *
 * A module has to render a slice bean in either backend or frontend mode.
 *
 * @package Cinnebar
 * @subpackage Module
 * @version $Id$
 */
interface iModule
{
    /**
     * Renders a slice bean in frontend mode.
     */
    public function renderFrontend();

    /**
     * Renders a slice bean in backend mode.
     */
    public function renderBackend();
}
