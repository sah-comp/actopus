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
 * Handles all belongings of your business model.
 *
 * Cinnebar models depend on RedBeanPHP as an ORM or Database Abstraction Layer. To learn more
 * about RedBeanPHP visit the RedBean website at {@link http://redbeanphp.com/}.
 *
 * To add your own models:
 * - Add a new file named after your model to the model directory
 * - Name that class after the scheme Model_*
 * - Your own model must extend this class
 * - Implement methods like dispense, update, etc. to react on RedBean Signals
 * - Implement any other method you need in your business model
 * - Do not instantiate your class, but instead R::dispense() or other RedBean methods are used
 * - RedBean will FUSE to your model while it provides persistence and db abstraction
 * - As an example see {@link Model_User}
 *
 * Here is an example:
 * <code>
 * <?php
 * require_once 'vendor/redbean/rb.php';
 * require_once 'model/user.php';
 * R::setup();
 * $p = R::dispense('person');
 * $p->first_name = 'John';
 * $p->last_name = 'Doe';
 * $name = $p->getName(); // this is a method you have implemented
 * R::store($p);
 * ?>
 * </code>
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Cinnebar_Model extends RedBean_SimpleModel
{
    /**
     * Defines the validation mode to throw an exception.
     */
    const VALIDATION_MODE_EXCEPTION = 1;

    /**
     * Defines the validation mode to store an valid or invalid state with the bean.
     */
    const VALIDATION_MODE_IMPLICIT = 2;

    /**
     * Defines the validation mode to store the valid or invalid state as a shared bean.
     */
    const VALIDATION_MODE_EXPLICIT = 4;
 
    /**
     * Switch to decide if this model will be automactially tagged or not.
     *
     * @var bool
     */
    private $auto_tag = false;
    
    /**
     * Switch to decide if this models history will be logged.
     *
     * @var bool
     */
    private $auto_info = false;

    /**
     * Contains errors of this model.
     *
     * @var array
     */
    protected $errors = array();
    
    /**
     * Holds the validation mode where 1 = Exception, 2 = Implicit attribute, 4 = Explicit which
     * effects all beans.
     *
     * @var int
     */
    protected static $validation_mode = self::VALIDATION_MODE_EXCEPTION;

    /**
     * Default template when rendering the model.
     *
     * @var string
     */
    private $template = 'default';
    
    /**
     * Container for list of callback validators.
     *
     * @var array
     */
    private $validators = array();
    
    /**
     * State of validation.
     *
     * @var bool
     */
    private $valid = true;
    
    /**
     * Container for a list of converters.
     *
     * @var array
     */
    private $converters = array();
    
    /**
     * Constructs a new model.
     *
     */
    public function __construct()
    {
    }
    
    /**
     * Returns a short text describing the bean for humans.
     *
     * @param Cinnebar_View $view
     * @return string
     */
    public function hitname(Cinnebar_View $view)
    {
        $template = '<a href="%s">%s</a>'."\n";
        return sprintf($template, $view->url(sprintf('/%s/edit/%d', $this->bean->getMeta('type'), $this->bean->getId())), $this->bean->getId());
    }
    
    /**
     * Returns a string where this bean was rendered into a model template.
     *
     * @param string $template
     * @param Cinnebar_View $view
     * @return string
     */
    public function render($template, Cinnebar_View $view)
    {
        return $view->partial(sprintf('model/%s/%s', $this->bean->getMeta('type'), $template), array('record' => $this->bean));
    }
    
    /**
     * Returns own(ed) beans that belong to this bean.
     *
     * @uses R
     * @param string $type
     * @param bool (optional) $add defaults to false
     * @return array $arrayOfOwnedBeans
     */
    public function own($type, $add = false)
    {
        $own_type = 'own'.ucfirst(strtolower($type));
        if (method_exists($this, 'get'.$own_type)) {
            $own_type = 'get'.$own_type;
            return $this->$own_type($add);
        }
        $own = $this->bean->$own_type;
        if ($add) $own[] = R::dispense($type);
        return $own;
    }
    
    /**
     * Returns shareded beans that belong to this bean.
     *
     * @uses R
     * @param string $type
     * @param bool (optional) $add defaults to false
     * @return array $arrayOfOwnedBeans
     */
    public function shared($type, $add = false)
    {
        $shared_type = 'shared'.ucfirst(strtolower($type));
        $shared = $this->bean->$shared_type;
        if ($add) $shared[] = R::dispense($type);
        return $shared;
    }
    
    /**
     * Returns wether the bean is mulilingual or not.
     *
     * @return bool
     */
    public function isI18n()
    {
        return false;
    }
    
    /**
     * Returns a i18n bean for this bean.
     *
     * A i18n bean means an internationalized version of a bean where the localizeable fields
     * are stored in a bean that extends the original beans name with the string 'i18n'.
     * If there is no i18n version for the asked language then the default language is
     * looked up and duplicated.
     *
     * @todo get rid of global language code
     *
     * @global $language
     * @global $config
     * @param mixed $iso code of the wanted translation language or null to use current language
     * @return RedBean_OODBBean $translation
     */
    public function i18n($iso = null)
    {
        global $language, $config;
        if ($iso === null && isset($_SESSION['backend']['language'])) {
            $iso = $_SESSION['backend']['language'];
        } elseif ($iso === null) {
            $iso = $language;
        }
        $i18n_type = $this->bean->getMeta('type').'i18n';
        if ( ! $i18n = R::findOne($i18n_type, $this->bean->getMeta('type').'_id = ? AND iso = ? LIMIT 1', array($this->bean->getId(), $iso))) {
            $i18n = R::dispense($i18n_type);
            $i18n->iso = $iso;
        }
        return $i18n;
    }
    
    /**
     * Returns an array with words splitters from a text.
     *
     * I found this regex on the web, but i can not remember where.
     *
     * @param string $text
     * @return array
     */
    public function splitToWords($text)
    {
    	return preg_split('/((^\p{P}+)|(\p{P}*\s+\p{P}*)|(\p{P}+$))/', $text, -1, PREG_SPLIT_NO_EMPTY);
    }
    
    /**
     * Returns only alphanumeric characters of the given string.
     *
     * @see http://stackoverflow.com/questions/5199133/function-to-return-only-alpha-numeric-characters-from-string
     *
     * @param string $text
     * @return string
     */
    public function alphanumericonly($text)
    {
        return preg_replace("/[^a-zA-Z0-9]+/", "", $text);
    }
    
    /**
     * Returns the content of an localized attribute.
     *
     * @param string $attribute
     * @param string (optional) $iso code of the language to translate to
     * @return string
     */
    public function translated($attribute, $iso = null)
    {
        return $this->i18n($iso)->$attribute;
    }
    
    /**
     * Returns the validation mode or sets it if optional parameter is given.
     *
     * @uses $validation_mode
     * @param int (optional) $mode the new validation mode
     * @return int $currentValidationMode
     */
    public function validationMode($mode = null)
    {
        if ($mode !== null) self::$validation_mode = $mode;
        return self::$validation_mode;
    }
    
    /**
     * Deletes the bean from the database.
     *
     * @return void
     */
    public function expunge()
    {
        R::trash($this->bean);
    }

    /**
     * This is called before the bean is updated.
     *
     * @uses validate()
     * @return void
     */
    public function update()
    {
        $this->convert();
        $this->validate();
    }
    
    /**
     * This is called after the bean was updated.
     *
     * @return void
     */
    public function after_update()
    {
        $this->info_workhorse();
        $this->tag_workhorse();
    }


    /**
     * Checks if the bean has a flag deleted which is true.
     *
     * @return bool
     */
    public function deleted()
    {
        return $this->bean->deleted;
    }
    
    /**
     * This is called when a bean was loaded.
     *
     * @return void
     */
    public function open()
    {
    }
    
    /**
     * This is called before a bean will be deleted.
     *
     * @return void
     */
    public function delete()
    {
    }
    
    /**
     * This is called after a bean has been deleted.
     *
     * @return void
     */
    public function after_delete()
    {
    }
    
    /**
     * This is called when a bean is dispended.
     *
     * This is the place where you would add validator callbacks or preset values in your model.
     *
     * @return void
     */
    public function dispense()
    {
    }
    
    /**
     * Sets the auto tag mode.
     *
     * @param int $flag
     * @return bool
     */
    public function setAutoTag($flag)
    {
        return $this->auto_tag = $flag;
    }

    /**
     * Returns the current auto tag flag.
     *
     * @return bool $autoTagOrNot
     */
    public function autoTag()
    {
        return $this->auto_tag;
    }

    /**
     * Sets the auto info mode.
     *
     * @param int $flag
     * @return bool
     */
    public function setAutoInfo($flag)
    {
        return $this->auto_info = $flag;
    }

    /**
     * Returns the current auto info flag.
     *
     * @return bool $autoInfoOrNot
     */
    public function autoInfo()
    {
        return $this->auto_info;
    }
    
	/**
	 * Returns an array with possible attributes for order clauses and such.
	 *
	 * @param string (optional) $layout defaults to table and can be of any value
	 * @return array
	 */
	public function attributes($layout = 'table')
	{
	    switch ($layout) {
	        default:
    	        $ret = array(
        			array(
        				'attribute' => 'id',
        				'orderclause' => 'id',
        				'class' => 'number',
        				'filter' => array(
        				    'type' => 'number'
        				)
        			)
        		);
	    }
        return $ret;
	}
	
	/**
	 * Returns an array of the bean.
	 *
	 * @param bool $header defaults to false, if true then column headers are returned
	 * @return array
	 */
	public function exportToCSV($header = false)
	{
	    if ($header === true) {
	        return array(
	        );
	    }
        return $this->bean->export();
	}
	
    /**
     * Returns a message string for an action on this bean.
     *
     * @param string (optional) $action defaults to 'idle'
     * @param string $type may be 'success', 'failure' or whatever you fancy, defaults to 'success'
     * @param RedBean_OODBBean (optional) $user
     * @return string $message
     */
    public function actionAsHumanText($action = 'idle', $type = 'success', $user = null)
    {
        $subject = __('you');
        if ( is_a($user, 'RedBean_OODBBean')) $subject = $user->name();
        return __('action_'.$action.'_on_'.$this->bean->getMeta('type').'_'.$type, array($subject));
    }
    
    /**
     * Returns an array with possible actions for scaffolding.
     *
     * @param array (optional) $presetActions
     * @return array
     */
    public function makeActions(array $actions = array())
    {
        return $actions;
    }
	
	/**
	 * Returns a menu object.
	 *
	 * Overwrite this method in your models to achieve a custom menu for any bean you want.
	 *
	 * @param string $action
	 * @param Cinnebar_View $view
	 * @param Cinnebar_Menu (optional) $menu
	 * @return Cinnebar_Menu
	 */
	public function makeMenu($action, Cinnebar_View $view, Cinnebar_Menu $menu = null)
	{
        $menu = new Cinnebar_Menu();
        $layouts = $this->layouts();
        if (count($layouts) > 1) {
            foreach ($layouts as $layout) {
                $menu->add(__('layout_'.$layout), $view->url(sprintf('/%s/index/%d/%d/%s/%d/%d', $this->bean->getMeta('type'), 1, Controller_Scaffold::LIMIT, $layout, $view->order, $view->dir)), 'scaffold-layout');
            }
        }
        $menu->add(__('scaffold_add'), $view->url(sprintf('/%s/add', $this->bean->getMeta('type'))), 'scaffold-add');
        $menu->add(__('scaffold_browse'), $view->url(sprintf('/%s/index', $this->bean->getMeta('type'))), 'scaffold-browse');
        return $menu;
	}
	
	/**
	 * Returns an array with possible layout for list view (index).
	 *
	 * @return array
	 */
	public function layouts()
	{
        return array('table');
	}
	
    /**
     * Returns SQL for total of all beans.
     *
     * @todo why sqlForThis and sqlForThat, unify!
     *
     * @uses R
     * @param string $where_clause
     * @return string $SQL
     */
    public function sqlForTotal($where_clause = '1')
    {
		$sql = <<<SQL
		SELECT
			COUNT(DISTINCT({$this->bean->getMeta('type')}.id)) as total
		FROM
			{$this->bean->getMeta('type')}

		WHERE {$where_clause}
SQL;
        return $sql;
    }
	
    /**
     * Returns SQL for filtering these beans.
     *
     * @uses R
     * @param string $where_clause
     * @param string $order_clause
     * @param int $offset
     * @param int $limit
     * @return string $SQL
     */
    public function sqlForFilters($where_clause = '1', $order_clause = 'id', $offset = 0, $limit = 1)
    {
		$sql = <<<SQL
		SELECT
            DISTINCT(id)  

		FROM
			{$this->bean->getMeta('type')}

		WHERE {$where_clause}

		ORDER BY {$order_clause}

		LIMIT {$offset}, {$limit}
SQL;
        return $sql;
    }
    
    /**
     * Returns array with strings or empty array.
     *
     * You must implement this method into your own model so that it returns keywords if you
     * want to auto tag your beans. Do not forget to setAutoTag(true) or no tags will be added.
     *
     * This is how an implementation in your own class could look like:
     * <code>
     * <?php
     * public funtion keywords() {
     *    $keywords = array(
     *        $this->bean->firstname,
     *        $this->bean->lastname
     *    );
     *    $keywords = array_merge($keywords, $this->split_text_into_words($this->long_desc));
     *    return $keywords;
     * }
     * ?>
     * </code>
     *
     * @return array
     */
    public function keywords()
    {
        return array();
    }
    
    /**
     * Searches for given searchterm within bean and returns the result-set as an multi-dim array
     * after the given layout.
     *
     * @param string $term contains the searchterm
     * @param string (optional) $layout defaults to "default"
     */
    public function clairvoyant($term, $layout = 'default')
    {
        $result = R::getAll(sprintf('select id as id, id as label, id as value from %s', $this->bean->getMeta('type')));
        return $result;
    }
    
    /**
     * Adds an error to the general errors or to a certain attribute if the optional parameter is set.
     *
     * @param string $errorText
     * @param string (optional) $attribute
     * @return void
     */
    public function addError($errorText, $attribute = '')
    {
        $this->errors[$attribute][] = $errorText;
    }

    /**
     * Sets the complete errors array at once.
     *
     * @param array $errors
     */
    public function setErrors(array $errors = array())
    {
        $this->errors = $errors;
    }

    /**
     * Returns the errors of this model.
     *
     * @return array $errors
     */
    public function errors()
    {
        return $this->errors;
    }
    
    /**
     * Returns the latest info bean of this bean.
     *
     * This uses a SQL query instead of R::relatedOne() because that was darn slow when
     * a bean has a lot of related info beans.
     *
     * @return RedBean_OODBBean $info
     */
    public function info()
    {
        if ( ! $this->autoInfo()) return R::dispense('info');
        if ( ! $this->bean->getId()) return R::dispense('info');
        try {
            $relation = array($this->bean->getMeta('type'), 'info');
            asort($relation); // because RB orders the table names
            $info_relation = implode('_', $relation);
            $bean_id_column = $this->bean->getMeta('type').'_id';
    		$sql = <<<SQL
    		SELECT
    			info.id AS info_id
    		FROM
    			{$this->bean->getMeta('type')}
		
    		LEFT JOIN {$info_relation} AS rinfo ON rinfo.{$bean_id_column} = {$this->bean->getMeta('type')}.id
    		LEFT JOIN info ON rinfo.info_id = info.id

    		WHERE
    		    {$this->bean->getMeta('type')}.id = ?
    		ORDER BY
    		    info.stamp DESC
    		LIMIT 1
SQL;
            $info_id = R::getCell($sql, array($this->bean->getId()));
        } catch (Exception $e) {
            Cinnebar_Logger::instance()->log($e, 'exceptions');
        }
        $info = R::load('info', $info_id);
    	if ( ! $info->getId()) {
    		$info = R::dispense('info');
    	}
    	return $info;
    }
    
    /**
     * Import data from csv array using a import map(per).
     *
     * @todo use a "splitter" to refer to a "nested" bean, e.g. optin: email and person.lastname
     *
     * @param RedBean_OODBBean $import is the import bean
     * @param array $data is an array of csv records
     * @param array $mappers is an array of map beans
     * @return void
     */
    public function csvImport(RedBean_OODBBean $import, array $data, array $mappers)
    {
        foreach ($mappers as $id=>$map) {
            if ($map->target == '__none__') continue; // we skip unsoliciated ?? import fields
            if ( empty($data[$map->source]) && ! empty($map->default)) {
                $this->bean->{$map->target} = $map->default;
            } else {
                $this->bean->{$map->target} = $data[$map->source];
            }
        }
    }
    
    /**
     * Returns wether the bean is invalid or not.
     *
     * A bean may have the attribute invalid if it ever has been used together with
     * validation mode implicit and validation a been failed.
     *
     * @return bool
     */
    public function invalid()
    {
        if ( isset($this->bean->invalid) && $this->bean->invalid) return true;
        return false;
    }
    
    /**
     * Returns the meta bean of this bean.
     *
     * @return RedBean_OODBBean $meta
     */
    public function meta()
    {
        if ( ! $this->bean->meta) $this->bean->meta = R::dispense('meta');
    	return $this->bean->meta;
    }
    
    /**
     * Returns the parent bean of this bean or an empty bean of same type if there is no parent.
     *
     * @uses R::load()
     * @return RedBean_OODBBean
     */
    public function parent()
    {
        $fn_parent = $this->bean->getMeta('type').'_id';
        if ( ! $this->bean->$fn_parent) return R::dispense($this->bean->getMeta('type'));
        return R::load($this->bean->getMeta('type'), $this->bean->$fn_parent);
    }
    
    /**
     * Returns an array of beans that are subordinated to this bean, aka children.
     *
     * @param string $orderfields
     * @param string $criteria
     * @return array $itemsFoundOrEmptyArray
     */
    public function children($orderfields = 'id', $criteria = null)
    {
        $fn_parent = $this->bean->getMeta('type').'_id';
        return R::find($this->bean->getMeta('type'), sprintf('%s = ? %s ORDER BY %s', $fn_parent, $criteria, $orderfields), array($this->bean->getId()));
    }
    
    /**
     * Returns the contents of an attribute from either this bean or the next bean up the tree.
     *
     * There is a check if this bean has attribute set. If so, that attribute will be returned.
     * Otherwise it starts to bubble up in the tree and looks for that attribute being set in
     * the next bean up in the tree. If there are no more parents and the attribute is still not
     * set NULL is returned.
     *
     * @uses bubble()
     * @uses R::load()
     * @param string $attribute
     * @return mixed
     */
    public function bubble($attribute)
    {
        $fn_parent = $this->bean->getMeta('type').'_id';
        if ( ! $this->bean->$fn_parent) return $this->bean->$attribute;
        if ($this->bean->$attribute) return $this->bean->$attribute;
        $parent = R::load($this->bean->getMeta('type'), $this->bean->$fn_parent);
        if ( ! $parent->getId()) return null;
        return $parent->bubble($attribute);
    }

    /**
     * Returns true if model has errors.
     *
     * If the optional parameter is set a certain attribute is tested for having an error or not.
     *
     * @uses Cinnebar_Model::$errors
     * @param string (optional) $attribute
     * @return bool $hasErrorOrHasNoError
     */
    public function hasError($attribute = '')
    {
        if ($attribute === '') return ! empty($this->errors);
        return isset($this->errors[$attribute]);
    }

    /**
     * Alias for {@link hasError()} call without an special attribute.
     *
     * @return bool $hasErrorsOrNone
     */
    public function hasErrors()
    {
        return $this->hasError();
    }
    
    /**
     * Calls the converters of this bean.
     */
    public function convert()
    {
        if (empty($this->converters)) return;
        foreach ($this->converters as $attribute=>$callbacks) {
            foreach ($callbacks as $n=>$param) {
                $converter_name = 'Converter_'.ucfirst(strtolower($param['converter']));
                $converter = new $converter_name($this->bean, $param['options']);
                $this->bean->$attribute = $converter->execute($this->bean->$attribute);
            }
        }
    }
    
    /**
     * Adds a converter callback name for an attribute.
     *
     * @uses Cinnebar_Model::$__converters
     * @param string $attribute
     * @param string $converter
     * @param array (optional) $options
     * @return void
     */
    public function addConverter($attribute, $converter, array $options = array())
    {
        $this->converters[$attribute][] = array(
            'converter' => $converter,
            'options' => $options
        );
    }
    
    /**
     * Validates this model and returns the result or throws an exception if invalid.
     *
     * Wether a exception is thrown or the validation result is returned depends of the
     * validation mode set.
     *
     * @todo Implement validation mode explicit to actually store a shared bean
     *
     * @uses Cinnebar_Model::workhorse_validate()
     * @return bool $validOrInvalid
     * @throws Exception in case the validation mode is set to do so
     */
    public function validate()
    {
        if (isset($this->invalid) && $this->invalid) $this->invalid = false;
        if ($valid = $this->validate_workhorse()) return true;
        if (self::VALIDATION_MODE_EXCEPTION === self::$validation_mode) {
            throw new Exception(__CLASS__.'_invalid: '.$this->bean->getMeta('type'));
        }
        if (self::VALIDATION_MODE_IMPLICIT === self::$validation_mode) {
            $this->invalid = true;
        }
        return false;
    }
    
    /**
     * Adds a validator callback name for an attribute.
     *
     * @uses Cinnebar_Model::$__validators
     * @param string $attribute
     * @param string $validator
     * @param array (optional) $options
     * @return void
     */
    public function addValidator($attribute, $validator, array $options = array())
    {
        $this->validators[$attribute][] = array(
            'validator' => $validator,
            'options' => $options
        );
    }
    
    /**
     * Loop through all validator callbacks and returns the state of validation.
     *
     * If a validator fails an error is added for that attribute. All validators are executed,
     * so you have a complete state of validation afterwards.
     * @uses Cinnebar_Validator
     * @return bool $validOrInvalid
     */
    protected function validate_workhorse()
    {
        if (empty($this->validators)) return true;
        $state = true;
        foreach ($this->validators as $attribute=>$callbacks) {
            foreach ($callbacks as $n=>$param) {
                $validator_name = 'Validator_'.ucfirst(strtolower($param['validator']));
                $validator = new $validator_name($param['options']);
                if ( ! $validator->execute($this->bean->$attribute)) {
                    $state = false;
                    $this->addError(sprintf($this->bean->getMeta('type').'_invalid_%s_%s', strtolower($param['validator']), strtolower($attribute)), $attribute);
                }
            }
        }
        return $state;
    }
    
    /**
     * If auto info is true a history entry will be added to this bean.
     *
     * If there is a current user with a valid session that guy is linked as a user, otherwise
     * the user relation of the auto info bean is NULL.
     *
     * @return bool $autoInfoAssciatedOrNot
     */
    protected function info_workhorse()
    {
        if ( ! $this->autoInfo()) return false;
        if ( ! $this->bean->getId()) return false;
        $info = R::dispense('info');
        $user = R::dispense('user')->current();
        if ($user->getId()) $info->user = $user;
        $info->stamp = time();
        try {
            R::store($info);
            R::associate($this->bean, $info);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * If auto tag is true a all keywords of this bean will be added as tags.
     *
     * @uses keywords()
     * @return bool $autoTaggedOrNot
     */
    protected function tag_workhorse()
    {
        if ( ! $this->autoTag()) return false;
        if ( ! $this->bean->getId()) return false;
        $tags = array();
        foreach ($this->keywords() as $n=>$keyword) {
            if (empty($keyword)) continue;
            $tags[] = $keyword;
        }
        try {
            R::tag($this->bean, $tags);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
