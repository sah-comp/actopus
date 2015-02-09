<?php
/**
 * Cinnebar.
 *
 * @package Cinnebar
 * @author $Author$
 * @version $Id$
 */
 
/**
 * Renders a template with data.
 *
 * @package Cinnebar
 * @subpackage View
 * @version $Id$
 */
class Cinnebar_View extends Cinnebar_Element
{
    /**
     * Holds the controller instance.
     *
     * @var Cinnebar_Controller
     */
    public $controller;

    /**
     * Holds the template name.
     *
     * @var string
     */
    public $template;
    
    /**
     * Holds the stylesheets assigned to this view.
     *
     * @var array
     */
    public $stylesheets = array();
    
    /**
     * Holds the javascript-(files) assigned to this view.
     *
     * @var array
     */
    public $javascripts = array();

    /**
     * Constructs a new View Manager.
     *
     * @param string $template
     */
    public function __construct($template)
    {
        $this->template = $template;
    }
    
    /**
     * Returns the controller or sets it when the optional parameter is given.
     *
     * @param mixed (optional) $controller which must be an Cinnebar_Controller
     * @return Cinnebar_Controller
     */
    public function controller($controller = null)
    {
        if ($controller === null) return $this->controller;
        $this->controller = $controller;
        return $this->controller;
    }
    
    /**
     * Returns the current user that uses this view.
     *
     * @return RedBean_OODBBean $user may be an empty guest user
     */
    public function user()
    {
        return $this->controller()->user();
    }
    
    /**
     * Returns the language code.
     *
     * @uses Cinnebar_Router::language()
     * @return string
     */
    public function language()
    {
        return $this->controller()->router()->language();
    }
    
    /**
     * Returns a string containing either the URL or a page title.
     *
     * @return string
     */
    public function title()
    {
        if (isset($this->title)) return $this->title;
        return $this->controller()->router()->basehref();
    }
    
    /**
     * Returns the base href.
     *
     * @uses Cinnebar_Router::basehref()
     * @return string
     */
    public function basehref()
    {
        return $this->controller()->router()->basehref();
    }
    
    /**
     * Resets the stylesheets array.
     *
     * @uses Cinnebar_View::$stylesheets empty array
     * @return bool $reset
     */
    public function resetStyles()
    {
        $this->stylesheets = array();
        return true;
    }
    
    /**
     * Add a file or an array of files to the stylesheets array.
     *
     * @uses Cinnebar_View::$stylesheets
     * @param mixed $files
     * @return bool $added
     */
    public function addStyle($files)
    {
        if ( ! is_array($files)) $files = array($files);
        foreach ($files as $file) {
            $this->stylesheets[] = $file;
        }
        return true;
    }

    /**
     * Returns the stylesheets array.
     *
     * @return array $stylesheets
     */
    public function styles()
    {
        return $this->stylesheets;
    }
    
    /**
     * Resets the javascripts array.
     *
     * @uses Cinnebar_View::$javascripts empty array
     * @return bool $resetted
     */
    public function resetJs()
    {
        $this->javascripts = array();
        return true;
    }
    
    /**
     * Add a file or an array of files to the javascripts array.
     *
     * @uses Cinnebar_View::$javascripts
     * @param mixed $files
     * @return bool $added
     */
    public function addJs($files)
    {
        if ( ! is_array($files)) $files = array($files);
        foreach ($files as $file) {
            $this->javascripts[] = $file;
        }
        return true;
    }
    
    /**
     * Returns the javascripts array.
     *
     * @return array $javascripts
     */
    public function js()
    {
        return $this->javascripts;
    }
    
    /**
     * Runs a viewhelper from the viewhelper directory.
     *
     * @param string $method
     * @param array (optional) $params
     * @return mixed
     */
    public function __call($method, array $params = array())
    {
        $helper_name = 'Viewhelper_'.ucfirst(strtolower($method));
        $helper = new $helper_name($this);
        return call_user_func_array(array($helper, 'execute'), $params);
    }
    
    /**
     * Renders a partial template.
     *
     * @uses workhorse_render()
     * @param string $partial
     * @param array (optional) $values
     * @return mixed
     */
    public function partial($partial, array $values = array())
    {
        return $this->render_workhorse($partial, array_merge($this->data, $values));
    }
    
    /**
     * Renders a template.
     *
     * @uses workhorse_render()
     * @param string ($optional) $template
     * @return string
     */
    public function render($template = null)
    {
        if ( $template === null) $template = $this->template;
        return $this->render_workhorse($template, $this->data);
    }
    
    /**
     * Returns true or false wether the template exists or not.
     *
     * @param string $template
     * @return bool
     */
    public function exists($template)
    {
        $file = BASEDIR.'/themes/'.S_THEME.'/templates/'.$template.'.php';
        if ( ! is_file($file)) {
            Cinnebar_Logger::instance()->log(sprintf('Template "%s" not found', $template), 'warn');
            return false;
        }
        return $file;
    }
    
    /**
     * Returns a string where template and data are mixed.
     *
     * @param string $template
     * @param array (optional) $values
     * @return string
     * @throws Exception if a template is missing
     */
    public function render_workhorse($template, array $values = array())
    {
        if ( ! $file = $this->exists($template)) return '';
        extract($values);
        ob_start();
        include $file;
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
    }
}
