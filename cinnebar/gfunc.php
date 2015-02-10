<?php
/**
 * Global functions to be used around the whole application.
 *
 * @package Cinnebar
 * @author $Author$
 * @version $Id$
 */

/**
 * Returns a translated string or the given token.
 *
 * @todo get rid of the global language or make that a fashion and cool down!
 *
 * @param string $text
 * @param mixed (optional) replacement values
 * @param string $lng iso-code of the language to use for translation
 * @param string (optional) $mode defines the mode that is used to render the token, e.g. textile
 * @param string (optional) $desc may describe the token, e.g. to help the translation team
 * @return string
 */
function __($text, $replacements = null, $lng = null, $mode = null, $desc = null)
{
    global $language;
    if (empty($lng)) $lng = $language;
    if ( ! $token = R::findOne('token', ' name = ? LIMIT 1', array($text))) {
        $token = R::dispense('token')->setAttr('name', $text);
        $token->mode = $mode;
        R::store($token);
    }
    if ( $replacements !== null) {
        if ( ! is_array($replacements)) $replacements = array($replacements);
        return vsprintf($token->in($lng)->payload, $replacements);
    }
    return $token->in($lng)->payload;
}

/**
 * Returns the given object for easier chaining.
 *
 * You can use this to directly chain method calls to an object on instantiation. In PHP < 5.4 you can
 * not do new Foo()->bar(), but you can use with(new Foo)->bar() as an escape.
 *
 * @param mixed $object
 * @return mixed $object
 */
function with($object)
{
    return $object;
}
