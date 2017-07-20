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
 * The user model manages user accounts of your application.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_User extends Cinnebar_Model
{
    /**
     * Defines the guest user.
     *
     * @var array
     */
    private $unknown_user = array(
        'nickname' => 'Nobody',
        'name' => 'Nobody',
        'email' => 'nobody@example.com',
        'pw' => 'secret',
        'home' => '/home',
        'admin' => false
    );

    /**
     * Holds an instance of a password hashing class.
     *
     * @var mixed
     */
    private $hasher;

    /**
     * Constructor.
     *
     * Depending on your configuration either phpass or {@link Cinnebar_Hash} will be used as
     * an hashing algorithm.
     *
     * @todo Refactor code to get rid of the global stuff and the implicit instanciating of hash class
     * @see $user_phpass to learn what can be configured
     * @uses parent::__construct()
     * @uses PasswordHash() if phpass is configured
     * @uses Cinnebar_Hash() if phpass is turned down
     */
    public function __construct()
    {
        parent::__construct();
        global $config;
        if (isset($config['user']['phpass']) && $config['user']['phpass']) {
            require_once BASEDIR.'/vendors/phpass/PasswordHash.php';
            $this->hasher = new PasswordHash(8, false);
        } else {
            $this->hasher = new Cinnebar_Hash();
        }
    }
    
    /**
     * Returns wether the bean is mulilingual or not.
     *
     * @return bool
     */
    public function isI18n()
    {
        return true;
    }
    
    /**
     * Returns wether the use has a certain role or not.
     *
     * @param string $role name
     * @return bool
     */
    public function hasRole($role)
    {
        if ( ! $role = R::findOne('role', ' name = ? LIMIT 1', array($role))) return false;
        return R::areRelated($this->bean, $role);
    }
    
    /**
     * Returns the first team of this user.
     *
     * @return RedBean_OODBBean $team
     */
    public function getFirstTeam()
    {
        $teams = $this->bean->sharedTeam;
        return array_shift( $teams );
    }
    
    /**
     * Returns an array of active user beans which belong to a certain role.
     *
     * @param mixed $role is either a int or an array of integers
     * @return array
     */
    public function belongsToRole($roles)
    {
        if ( ! is_array($roles)) $roles = array($roles);
        $sql = <<<SQL
		SELECT
			user.id as id  

		FROM
			role_user
		
		LEFT JOIN user ON user.id = role_user.user_id

		WHERE
		    role_user.role_id IN (%s) AND user.deleted = 0 AND user.banned = 0

		ORDER BY user.name
SQL;
        $sql = sprintf($sql, R::genSlots($roles));
        //R::debug(true);
        $assoc = R::$adapter->getAssoc($sql, $roles);
        //R::debug(false);
        return R::batch('user', array_keys($assoc));
    }
    
    /**
     * Returns the current iso language code for backend activities.
     *
     * @return string
     */
    public function language()
    {
        return $_SESSION['backend']['language'];
    }

    /**
     * Returns the current user bean or an (empty) guest user.
     *
     * <b>Attention</b>: A session must have been started.
     *
     * @uses $_SESSION['user']['id'] to determine the current user id
     * @return RedBean_OODBBean
     */
    public function current()
    {
        if ( ! isset($_SESSION['user']['id'])) {
            $this->bean->import($this->unknown_user);
            return $this->bean;
        }
        return $this->bean = R::load('user', $_SESSION['user']['id']);
    }
    
    /**
     * Adds a notification message to this user.
     *
     * @uses R
     * @uses Model_Notification
     * @param string $message
     * @param string (optional) $template Name of the template
     * @return bool $notificationAddedOrNot
     */
    public function notify($message, $template = 'info')
    {
        $notification = R::dispense('notification');
        $notification->payload = $message;
        $notification->template = $template;
        $notification->once = true;
        try {
            R::associate($this->bean, $notification);
            R::store($notification);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Returns an array with this users notifications and dismisses them.
     *
     * @param string $sql optional $sql
     * @param array $values optional array with values for the sql jokers
     * @return array $arrayOfNotifications
     */
    public function notifications($sql = '', array $values = array())
    {
        $all = R::related($this->bean, 'notification', $sql, $values);
        R::trashAll($all);
        return $all;
    }
    
    /**
     * Returns the users email-address or another user attribute working as a user name.
     *
     * If a 'screenname' setting exists for this user it will be used otherwise this will return the
     * users e-mail address.
     *
     * @uses getSetting() to retrieve a setting bean for 'screenname'
     * @return string $nameOrEmailAddressOrScreenname
     */
    public function name()
    {
        return $this->bean->email;
    }

    /**
     * Returns array with strings or empty array.
     *
     * @return array
     */
    public function keywords()
    {
        return array(
            $this->bean->email,
            $this->bean->name
        );
    }
    
    /**
     * Returns the users homepage or URL given in the optional parameter.
     *
     * @param string (optional) $gotoURL If given this is the prefered home URL
     * @return string $homeURL
     */
    public function home($goto = '')
    {
        if ($goto) return $goto;
        return $this->bean->home;
    }
    
    /**
     * This is called before a user bean is updated.
     *
     * If this bean has never been stored the password is hashed. If you want to change
     * password later on you must use changePassword().
     *
     * @uses PasswordHash::HashPassword() is used to hash password on creation
     * @uses parent::update()
     */
    public function update()
    {
        if ( ! $this->bean->getId()) {
            $this->bean->pw = $this->hasher->HashPassword($this->bean->pw);
        }
        $this->bean->ego = md5($this->bean->email);
        parent::update();
    }
    
    /**
     * Define validators and set auto info to true.
     */
    public function dispense()
    {
        //$this->bean->setMeta('buildcommand.unique', array(array('nickname')));
        //$this->bean->setMeta('buildcommand.unique', array(array('email')));
        $this->addValidator('email', 'isemail');
        $this->addValidator('email', 'isunique', array('bean' => $this->bean, 'attribute' => 'email'));
        $this->addValidator('name', 'hasvalue');
        $this->addValidator('name', 'isunique', array('bean' => $this->bean, 'attribute' => 'name'));
        $this->setAutoInfo(true);
    }
    
    /**
     * Changes the password if old password is good and new one matches repetition.
     *
     * @uses PasswordHash::CheckPassword() to compare the users password with the one given
     * @uses PasswordHash::HashPassword() to hash the new password
     * @param string $password The currently active password
     * @param string $new The new password
     * @param string $repeated The new password, repeated for safety
     * @return bool
     */
    public function changePassword($password, $new, $repeated)
    {
        if ( ! trim($new)) {
            $this->addError('error_password_cant_be_empty', 'pw');
            return false;
        }
        if ($new != $repeated) {
            $this->addError('error_passwords_do_not_match', 'pw_new');
            $this->addError('error_passwords_do_not_match', 'pw_repeated');
            return false;
        }
        if ( ! $this->hasher->CheckPassword($password, $this->bean->pw)) {
            $this->addError('error_password_wrong', 'pw');
            return false;
        };
        $this->bean->pw = $this->hasher->HashPassword($new);
        return true;
    }

    /**
     * Returns true if a user was found and the passwords match and the account is not banned.
     *
     * If a user account was found, the passwords match and the account is not banned
     * the user bean is loaded into the model.
     *
     * @uses PasswordHash::CheckPassword()
     * @param string $name Either the users e-mail address or the users nickname
     * @param string $password
     * @return mixed false if no user qualified or the user bean if one was logged
     */
    public function login($name, $password)
    {
        if ( ! $user = R::findOne('user', ' email=:name OR name=:name LIMIT 1', array(':name' => $name))) {
            $this->addError('error_no_user_found', 'name');
            return false;
        }
        if ( ! $this->hasher->CheckPassword($password, $user->pw)) {
            $this->addError('error_wrong_password', 'pw');
            return false;
        }
        if ($user->deleted()) {
            $this->addError('error_user_deleted');
            return false;
        }
        if ($user->banned()) {
            $this->addError('error_user_banned');
            return false;
        }
        return $user;
    }
    
    /**
     * Returns wether the user account is deleted or not.
     *
     * @return bool
     */
    public function deleted()
    {
        if ($this->bean->deleted) return true;
        return false;
    }
    
    /**
     * Returns wether the user account is banned or not.
     *
     * @return bool
     */
    public function banned()
    {
        if ($this->bean->banned) return true;
        return false;
    }
    
    /**
     * Logs out this user and unsets the current user id in the session.
     *
     * @return bool $loggedOutOrNot
     */
    public function logout()
    {
        $this->bean->sid = null;
        return true;
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
			DISTINCT(user.id) as id  

		FROM
			user

		WHERE {$where_clause}

		ORDER BY {$order_clause}

		LIMIT {$offset}, {$limit}
SQL;
        return $sql;
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
        				'attribute' => 'email',
        				'orderclause' => 'email',
        				'class' => 'text',
        				'filter' => array(
        				    'tag' => 'text'
                        )
        			),
        			array(
        				'attribute' => 'name',
        				'orderclause' => 'name',
        				'class' => 'text',
        				'filter' => array(
        				    'tag' => 'text'
                        )
        			),
        			array(
        				'attribute' => 'admin',
        				'orderclause' => 'admin',
        				'class' => 'bool',
        				'viewhelper' => 'bool',
        				'filter' => array(
        				    'tag' => 'bool'
        				)
        			)
        		);
        }
        return $ret;
	}
	
    /**
     * Searches for given searchterm within bean and returns the result-set as an multi-dim array
     * after the given layout.
     *
     * @param string $term contains the searchterm as given by jQuery.autocomplete
     * @param string (optional) $layout defaults to "default"
     * @return array
     */
    public function clairvoyant($term, $layout = 'default')
    {   
        switch ($layout) {
            case 'email':
                $sql = <<<SQL

                SELECT
                    id AS id,
                    email AS label

                FROM
                    user

                WHERE
                    email like ?

                ORDER BY
                    email

SQL;
                break;
            default:
                $sql = <<<SQL

                SELECT
                    id AS id,
                    name AS label

                FROM
                    user

                WHERE
                    name like ?

                ORDER BY
                    name

SQL;
        }
        return $res = R::getAll($sql, array($term.'%'));
    }

	/**
	 * Returns a customized menu.
	 *
	 * @param string $action
	 * @param Cinnebar_View $view
 	 * @param Cinnebar_Menu (optional) $menu
 	 * @return Cinnebar_Menu
 	 */
 	public function makeMenu($action, Cinnebar_View $view, Cinnebar_Menu $menu = null)
	{
        $menu = parent::makeMenu($action, $view, $menu);
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
     * Really sends out the newsletter to given email addresses.
     *
     * @uses PHPMailer
     *
     * @param Cinnebar_Controller $controller
     * @return void
     */
    public function sendInvite(Cinnebar_Controller $controller)
    {
        global $config;
        require_once BASEDIR.'/vendors/PHPMailer_5.2.4/class.phpmailer.php';
        
		$mail = new PHPMailer();
		$mail->CharSet = 'UTF-8';
		$mail->Subject = __('invitation_subject').' '.htmlspecialchars($this->bean->name);
		
		$mail->From = $config['listmanager']['email'];
		$mail->FromName = $config['listmanager']['name'];
  		$mail->AddReplyTo($config['listmanager']['email'], $config['listmanager']['name']);

		$mail->IsSMTP();
		//$mail->SMTPDebug = 2;
		$mail->SMTPAuth = true;
		$mail->SMTPKeepAlive = true;
		$mail->Host = $config['smtp']['host'];
		$mail->Port = $config['smtp']['port'];
		$mail->Username = $config['smtp']['user'];
		$mail->Password = $config['smtp']['pw'];

		$result = true;

        $body_html = $controller->makeView(sprintf('user/mail/%s/html', $controller->router()->language()));
		$body_text = $controller->makeView(sprintf('user/mail/%s/text', $controller->router()->language()));
		$body_html->record = $body_text->record = $this->bean;
		$mail->MsgHTML($body_html->render());
		$mail->AltBody = $body_text->render();
	    
	    $mail->AddAddress($this->bean->email);
        $result = $mail->Send();

		return $result;
    }

	/**
	 * Returns users who are on-line, that is having a session not older than given seconds.
	 *
	 * @param int $period of seconds a user session may have aged
	 * @return array
	 */
	public function whoisonline($period = 120)
	{
		$sql = <<<SQL
			SELECT
				user.id AS id
			FROM user
			LEFT JOIN session on session.token = user.sid
			WHERE
			session.lastupdate >= (unix_timestamp(now()) - ?) AND
			user.id != ?
			ORDER BY user.name
SQL;
		try {
			$assoc = R::$adapter->getAssoc($sql, array($period, $this->bean->getId()));
			return R::batch('user', array_keys($assoc));
		} catch (Exception $e) {
            Cinnebar_Logger::instance()->log('Model_User::whoisonline '.$e);
			return array();
		}
	}
}
