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
 * Provides some useful methods to maninulate arrays and strings and so on.
 *
 * @package Cinnebar
 * @subpackage Core
 * @version $Id$
 */
class Cinnebar_Element
{
    /**
     * Container for dependencies.
     *
     * @var array
     */
    public $deps = array();

    /**
     * Defines the checked attribute for checkboxes.
     *
     * @var string
     */
    const CHECKED = ' checked="checked"';

    /**
     * Defines the selected attribute for select options.
     *
     * @var string
     */
    const SELECTED = ' selected="selected"';
    
    /**
     * Defines the disabled attribute for input tags.
     *
     * @var string
     */
    const DISABLED = ' disabled="disabled"';
    
    /**
     * Defines the reda-only attribute for input tags.
     *
     * @var string
     */
    const READONLY = ' readonly="readonly"';
    
    /**
     * Defines the value of CSS style display block.
     *
     * @var string
     */
    const DISPLAY_BLOCK = 'block';

    /**
     * Defines the value of CSS style display none.
     *
     * @var string
     */
    const DISPLAY_NONE = 'none';

    /**
     * Holds errors messages for this element.
     *
     * @var array
     */
    public $errors = array();
    
    /**
     * Container for attributes.
     *
     * @var array
     */
    public $data = array();

    /**
     * Constructor.
     *
     */
    public function __construct()
    {
    }
    
    /**
     * Sets the value of attribute.
     *
     * @uses $data
     * @param string $attribute
     * @param mixed (optional) $value
     */
    public function __set($attribute, $value = null)
    {
        $this->data[$attribute] = $value;
    }
    
    /**
     * Unsets the value of attribute.
     *
     * @param string $attribute
     */
    public function __unset($attribute)
    {
        unset($this->data[$attribute]);
    }
    
    /**
     * Returns wether a value is set or not.
     *
     * @param string $attribute
     * @return bool
     */
    public function __isset($attribute)
    {
        return isset($this->data[$attribute]);
    }
    
    /**
     * Returns the value of an attribute or NULL if value is not set.
     *
     * @param string $attribute
     * @return mixed
     */
    public function __get($attribute)
    {
        if (array_key_exists($attribute, $this->data)) return $this->data[$attribute];
        return null;
    }
    
    /**
     * Inject dependencies.
     *
     * @uses $deps
     * @param array $deps
     */
    public function di(array $deps)
    {
        $this->deps = $deps;
    }

    /**
     * Adds an error to our error container.
     *
     * @param string $error_text
     * @param string (optional) $error_type
     * @return bool
     */
    public function addError($error_text, $error_type = '')
    {
        $this->errors[$error_type][] = $error_text;
        return true;
    }
    
    /**
     * Returns true if there are errors.
     *
     * @return bool
     */
    public function hasErrors()
    {
        return count($this->errors);
    }
    
    /**
     * Returns this elements errors.
     *
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }
    
    /**
     * Returns a the given string safely to use as filename or url.
     *
     * @link http://stackoverflow.com/questions/2668854/sanitizing-strings-to-make-them-url-and-filename-safe
     *
     * What it does:
     * - Replace all weird characters with dashes
     * - Only allow one dash separator at a time (and make string lowercase)
     *
     * @param string $string the string to clean
     * @param bool $is_filename false will allow additional filename characters
     * @return string
     */
    public function sanitizeFilename($string = '', $is_filename = false)
    {
        $string = preg_replace('/[^\w\-'. ($is_filename ? '~_\.' : ''). ']+/u', '-', $string);
        return mb_strtolower(preg_replace('/--+/u', '-', $string));
    }

    /**
     * Glues together an array of key/values as a string and returns it.
     *
     * Usage Example:
     * <code>
     * <?php
     * $text = glue(array('title' => 'Test', 'lenght' => '4'));
     * ?>
     * </code>
     *
     * @param mixed (required) $dict
     * @param string (optional) $glueOpen
     * @param string (optional) $glueClose
     * @param string (optional) $pre
     * @param string (optional) $impChar
     * @return string $gluedString
     */
    public static function glue($dict, $glueOpen = '="', $glueClose = '"', $pre = ' ', $impChar = ' ')
    {
    	if (empty($dict)) return '';
    	$stack = array();
    	foreach ($dict as $key=>$value) {
    		$stack[] = $key.$glueOpen.htmlspecialchars($value).$glueClose;
    	}
    	return $pre.implode($impChar, $stack);
    }
    
    /**
     * Replaces some charactes with others and returns the stripped string.
     *
     * @param string $source
     * @param array (optional) $tokens
     * @param array (optional) $replacements
     * @return string
     */
    public static function stripped($source, array $tokens = array('[', ']'), array $replacements = array('-', ''))
    {
        return str_replace($tokens, $replacements, $source);
    }
}
