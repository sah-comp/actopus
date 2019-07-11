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
 * Manages persons.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Person extends Cinnebar_Model
{
    /**
     * Look up searchtext in all fields of a bean.
     *
     * @param string $searchphrase
     * @return array
     */
    public function searchAllFields($searchphrase = '')
    {
        $searchphrase = '%'.$searchphrase.'%';
        return R::find('person', ' nickname LIKE :f OR organization LIKE :f OR firstname LIKE :f OR lastname LIKE :f OR note LIKE :f OR attention LIKE :f OR account LIKE :f OR taxid LIKE :f OR title LIKE :f OR iso LIKE :f OR name LIKE :f OR suffix LIKE :f OR jobtitle LIKE :f OR department LIKE :f', array(':f' => $searchphrase));
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
        return sprintf($template, $view->url(sprintf('/%s/edit/%d', $this->bean->getMeta('type'), $this->bean->getId())), $this->bean->name);
    }

    /**
     * Returns a person name wrapped up in a span.
     *
     * @return string
     */
    public function personNickname()
    {
        $deleted = '';
        if ($this->bean->deleted) $deleted = ' class="deleted"';
        return '<span'.$deleted.'>'.$this->bean->nickname.'</span>';
    }

    /**
     * Marks the bean for deletion.
     *
     * A person is not immediatly deleted from the database because
     * we must asure that no related stuff (card, etc) get lost.
     *
     * @return void
     */
    public function expunge()
    {
        $cards = R::find('card', ' client_id = :pid OR invreceiver_id = :pid OR applicant_id = :pid OR foreign_id = :pid LIMIT 1 ', array(':pid' => $this->bean->getId()));
        $invoices = R::find('invoice', ' client_id = ? LIMIT 1', array($this->bean->getId()));
        try {
            if ( ! $cards && ! $invoices ) {
                R::trash($this->bean);
            } else {
                $this->bean->deleted = true;
                R::store($this->bean);
            }
            return true;
        } catch (Exception $e) {
            Cinnebar_Logger::instance()->log($e, 'exceptions');
            return false;
        }
    }

    /**
     * Returns a user bean.
     *
     * @return RedBean_OODBBean
     */
    public function user()
    {
        if ( ! $this->bean->user) $this->bean->user = R::dispense('user');
        return $this->bean->user;
    }
    
    /**
     * Returns a pricetype bean.
     *
     * @return RedBean_OODBBean
     */
    public function pricetype()
    {
        if ( ! $this->bean->pricetype) $this->bean->pricetype = R::dispense('pricetype');
        return $this->bean->pricetype;
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
			DISTINCT(person.id) as id  

		FROM
			person

		WHERE {$where_clause}

		ORDER BY {$order_clause}

		LIMIT {$offset}, {$limit}
SQL;
        return $sql;
    }
    
    /**
     * Returns SQL for fetch the total of all beans.
     *
     * @todo get rid of global language code
     *
     * @uses R
     * @param string $where_clause
     * @return string $SQL
     */
    public function sqlForTotal($where_clause = '1')
    {
		$sql = <<<SQL
		SELECT
			COUNT(DISTINCT(person.id)) as total
		FROM
			person

		WHERE {$where_clause}
SQL;
        return $sql;
    }
    
    /**
     * Returns a address label.
     *
     * @deprecated since its only used by @Migrator_Gvr
     *
     * @param bool (optional) $legacy defaults to false, if true the legacy formatted address is used
     * @return string
     */
    public function addressLabel($legacy = false)
    {
        return sprintf("%s\n%s", $this->bean->name, $this->bean->legacyaddresswork);
    }
    
    /**
     * Returns an address label of a certain type.
     *
     * @param string $label
     * @return RedBean_OODBBean $address
     */
    public function addressLabelByType($label = 'work')
    {
        $address = R::findOne('address', ' person_id = ? AND label = ? LIMIT 1', array(
            $this->bean->getId(),
            $label
        ));
        if ( ! $address) $address = R::dispense('address');
        return $address;
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
            default:
                $sql = <<<SQL

                SELECT
                    person.id AS id,
                    person.nickname AS nickname,
                    CONCAT_WS(' ', person.nickname, person.name, address.label, REPLACE(address.formattedaddress, CHAR(10), ' ')) AS label,
                    person.id AS person_id,
                    CONCAT_WS(CHAR(10), person.name, address.formattedaddress) AS address

                FROM
                    person
                    
                LEFT JOIN
                    address ON address.person_id = person.id

                WHERE
                    (person.nickname like :searchtext OR person.name like :searchtext) AND
                    (person.deleted IS NULL OR person.deleted = 0)

                ORDER BY
                    person.name

SQL;
        }
        return $res = R::getAll($sql, array(':searchtext' => $term.'%'));
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
        				'attribute' => 'nickname',
        				'orderclause' => 'person.nickname',
        				'class' => 'text',
        				'filter' => array(
        				    'tag' => 'text'
        				),
        				'callback' => array(
        				    'name' => 'personNickname'
        				)
        			),
        			array(
        				'attribute' => 'account',
        				'orderclause' => 'person.account',
        				'class' => 'text',
        				'filter' => array(
        				    'tag' => 'text'
        				)
        			),
        			array(
        				'attribute' => 'lastname',
        				'orderclause' => 'person.lastname',
        				'class' => 'text',
        				'filter' => array(
        				    'tag' => 'text'
        				)
        			),
        			array(
        				'attribute' => 'firstname',
        				'orderclause' => 'person.firstname',
        				'class' => 'text',
        				'filter' => array(
        				    'tag' => 'text'
        				)
        			),
        			array(
        				'attribute' => 'organization',
        				'orderclause' => 'person.organization',
        				'class' => 'text',
        				'filter' => array(
        				    'tag' => 'text'
        				)
        			)
        		);
        }
        return $ret;
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
     * Returns keywords from this bean for tagging.
     *
     * @var array
     */
    public function keywords()
    {
        return array(
            $this->bean->firstname,
            $this->bean->lastname,
            $this->bean->organization,
            $this->bean->nickname,
            $this->bean->phoneticfirstname,
            $this->bean->phoneticlastname
        );
    }

    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        //$this->bean->setMeta('buildcommand.unique', array(array('nickname')));
        $this->setAutoTag(true);
        $this->setAutoInfo(true);
        $this->addValidator('nickname', 'hasvalue');
        $this->addValidator('nickname', 'isunique', array('bean' => $this->bean, 'attribute' => 'nickname'));
        $this->addValidator('name', 'hasvalue');
        //$this->addConverter('birthdate', 'mySQLDate');
    }
    
    /**
     * Update.
     *
     * @todo Implement a switch to decide wether to use first/last or last/first name order
     */
    public function update()
    {
		// set the phonetic names
		$this->bean->phoneticlastname = soundex($this->bean->lastname);
		$this->bean->phoneticfirstname = soundex($this->bean->firstname);
		// set the name according to sort rule
		$this->bean->name = implode(' ', array($this->bean->firstname, $this->bean->lastname));
		// company name
		if (trim($this->bean->name) == '' && $this->bean->organization || $this->bean->company) {
			$this->bean->name = $this->bean->organization;
		}
		if (trim($this->bean->name) == '') {
			$this->bean->name = $this->bean->nickname;
		}
		parent::update();
    }
    
    /**
     * after update all card *address attributes have to be updated.
     */
    public function after_update()
    {
        $this->rebuildCardWithPerson();
        $this->rebuildInvoiceWithPerson();
    }
    
    /**
     * Update all card beans that have this person as client or otherwise related.
     */
    private function rebuildCardWithPerson()
    {   
        $addrLabel = $this->addressLabelByType()->formatAddress();
        $sql = "UPDATE card set clientnickname = :cnick, clientaddress = :caddr WHERE client_id = :pid";
        R::exec($sql, array(
            ':pid' => $this->bean->getId(),
            ':cnick' => $this->bean->nickname,
            ':caddr' => $addrLabel
        ));
        $sql = "UPDATE card set invreceivernickname = :cnick, invreceiveraddress = :caddr WHERE invreceiver_id = :pid";
        R::exec($sql, array(
            ':pid' => $this->bean->getId(),
            ':cnick' => $this->bean->nickname,
            ':caddr' => $addrLabel
        ));
        $sql = "UPDATE card set applicantnickname = :cnick, applicantaddress = :caddr WHERE applicant_id = :pid";
        R::exec($sql, array(
            ':pid' => $this->bean->getId(),
            ':cnick' => $this->bean->nickname,
            ':caddr' => $addrLabel
        ));
        $sql = "UPDATE card set foreignnickname = :cnick, foreignaddress = :caddr WHERE foreign_id = :pid";
        R::exec($sql, array(
            ':pid' => $this->bean->getId(),
            ':cnick' => $this->bean->nickname,
            ':caddr' => $addrLabel
        ));
        /*
        $offset = 0;
        $limit = 500;
        while ($records = R::findAll('card', ' client_id = :pid OR invreceiver_id =:pid OR applicant_id = :pid OR foreign_id = :pid ORDER BY id LIMIT '.$limit.' OFFSET '.$offset, array(':pid' => $this->bean->getId()))) {
            foreach ($records as $id => $record) {
                $update = false;
                if ($record->client_id == $this->bean->getId()) {
                    $record->clientnickname = $this->bean->nickname;
                    $record->clientaddress = $addrLabel;
                    $update = true;
                }
                if ($record->invreceiver_id == $this->bean->getId()) {
                    $record->invreceivernickname = $this->bean->nickname;
                    $record->invreceiveraddress = $addrLabel;
                    $update = true;
                }
                if ($record->applicant_id == $this->bean->getId()) {
                    $record->applicantnickname = $this->bean->nickname;
                    $record->applicantaddress = $addrLabel;
                    $update = true;
                }
                if ($record->foreign_id == $this->bean->getId()) {
                    $record->foreignnickname = $this->bean->nickname;
                    $record->foreignaddress = $addrLabel;
                    $update = true;
                }
                if ($update) {
                    try {
                        $record->setAutoInfo(false);
                        $record->setAutoTag(false);
                        R::store($record);
                    }
                    catch (Exception $e) {
                    }
                }
            }
            $offset = $offset + $limit;    
        }
        */
    }
    
    /**
     * Update all invoice beans that have this person as client.
     */
    private function rebuildInvoiceWithPerson()
    {
        $addrLabel = $this->addressLabelByType()->formatAddress();
        $sql = "UPDATE card set clientnickname = :cnick, clientaddress = :caddr WHERE client_id = :pid";
        R::exec($sql, array(
            ':pid' => $this->bean->getId(),
            ':cnick' => $this->bean->nickname,
            ':caddr' => $addrLabel
        ));
        /*
        $offset = 0;
        $limit = 500;
        while ($records = R::findAll('card', ' client_id = :pid ORDER BY id LIMIT '.$limit.' OFFSET '.$offset, array(':pid' => $this->bean->getId()))) {
            foreach ($records as $id => $record) {
                $update = false;
                if ($record->client_id == $this->bean->getId()) {
                    $record->clientnickname = $this->bean->nickname;
                    //$record->clientaddress = $addrLabel;
                    $update = true;
                }
                if ($update) {
                    try {
                        $record->setAutoInfo(false);
                        $record->setAutoTag(false);
                        R::store($record);
                    }
                    catch (Exception $e) {
                    }
                }
            }
            $offset = $offset + $limit;    
        }
        */
    }

}
