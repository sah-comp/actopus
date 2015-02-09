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
 * Hello World says Hello world in many languages.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Helloworld extends Cinnebar_Model
{
    /**
     * Returns the phrase Hello world in the given iso language code.
     *
     * @param string $language
     * @return string
     */
    public function lingua($language = 'de')
    {
        $phrases = array(
            'de' => 'Hallo Welt',
            'en' => 'Hello World'
        );
        return $phrases[$language];
    }
}
