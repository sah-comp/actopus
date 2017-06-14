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
 * Manages cardtype.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Invoice extends Cinnebar_Model
{
    /**
     * Returns an array with possible actions for scaffolding.
     *
     * @param array (optional) $presetActions
     * @return array
     */
    public function makeActions(array $actions = array())
    {
        $actions['table'] = array('contra', 're');
        return $actions;
    }

    /**
     * Returns the invoice number, optionally wrapped in a span when canceled (deleted).
     *
     * @return string
     */
    public function invoiceName()
    {
        $deleted = '';
        if ($this->bean->deleted) $deleted = ' class="deleted"';
        return '<span'.$deleted.'>'.$this->bean->name.'</span>';
    }

    /**
     * Set the invoice to canceled (deleted).
     *
     * An invoice can not be deleted after booking, so we have to set it to deleted
     * which is understood as financ. cancelation.
     *
     * @return void
     */
    public function contra()
    {
        $this->bean->deleted = true;
        try {
            R::store($this->bean);
            return true;
        } catch (Exception $e) {
            Cinnebar_Logger::instance()->log($e, 'exceptions');
            return false;
        }
    }
    
    /**
     * Uncanceles (undeletes) the invoice.
     *
     * @return void
     */
    public function re()
    {
        $this->bean->deleted = false;
        try {
            R::store($this->bean);
            return true;
        } catch (Exception $e) {
            Cinnebar_Logger::instance()->log($e, 'exceptions');
            return false;
        }
    }
    
    /**
     * Alias for contra, overrides the expunge method which would actually delete the bean.
     *
     * @uses contra()
     */
    public function expunge()
    {
        return $this->contra();
    }

    /**
     * Returns a invoicetype bean.
     *
     * @return RedBean_OODBBean
     */
    public function invoicetype()
    {
        if ( ! $this->bean->invoicetype) $this->bean->invoicetype = R::dispense('invoicetype');
        return $this->bean->invoicetype;
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
     * Returns a invoicetype name.
     *
     * @return string
     */
    public function invoicetypeName()
    {
        return $this->bean->invoicetype()->name;
    }
    
    /**
     * Returns a user name.
     *
     * @return string
     */
    public function invoiceuser()
    {
        return $this->bean->info()->user()->name();
    }
    
    /**
     * Returns a client name.
     *
     * @return RedBean_OODBBean
     */
    public function clientName()
    {
        return $this->bean->client()->name;
    }
    
    /**
     * Returns a client nickname.
     *
     * @return RedBean_OODBBean
     */
    public function clientNickname()
    {
        return $this->bean->client()->nickname;
    }

    /**
     * Returns a person as client bean.
     *
     * @return RedBean_OODBBean
     */
    public function client()
    {
        if ( ! $this->bean->fetchAs('person')->client) $this->bean->client = R::dispense('person');
        return $this->bean->fetchAs('person')->client;
    }

    /**
     * Returns a card name.
     *
     * @return RedBean_OODBBean
     */
    public function cardName()
    {
        return $this->bean->card()->name;
    }
    
    /**
     * Returns a user name.
     *
     * @return RedBean_OODBBean
     */
    public function userName()
    {
        return $this->bean->user()->name();
    }
    
    
    /**
     * Returns a user alias attorney bean.
     *
     * @return RedBean_OODBBean
     */
    public function attorney()
    {
        if ( ! $this->bean->fetchAs('user')->attorney) $this->bean->attorney = R::dispense('user');
        return $this->bean->attorney;
    }

    /**
     * Returns a attorney name.
     *
     * @return RedBean_OODBBean
     */
    public function attorneyName()
    {
        return $this->bean->attorney()->name;
    }

    /**
     * Returns a person as client bean.
     *
     * @return RedBean_OODBBean
     */
    public function card()
    {
        if ( ! $this->bean->card) $this->bean->card = R::dispense('card');
        return $this->bean->card;
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
        				'attribute' => 'year',
        				'orderclause' => 'invoice.year',
        				'class' => 'number',
        				'filter' => array(
        				    'tag' => 'number'
        				)
        			),
        			array(
        				'attribute' => 'm',
        				'orderclause' => 'invoice.m',
        				'class' => 'number',
        				'filter' => array(
        				    'tag' => 'number'
        				)
        			),
        			array(
        				'attribute' => 'd',
        				'orderclause' => 'invoice.d',
        				'class' => 'number',
        				'filter' => array(
        				    'tag' => 'number'
        				)
        			),
                    array(
        			    'attribute' => 'invoicetype_id',
        			    'orderclause' => 'invoicetype.name',
        			    'class' => 'text',
        			    'callback' => array(
        			        'name' => 'invoicetypeName'
        			    ),
        				'filter' => array(
        				    'tag' => 'text'
        				)
        			),
        			array(
        				'attribute' => 'name',
        				'orderclause' => 'invoice.name',
        				'class' => 'number',
        				'filter' => array(
        				    'tag' => 'number'
        				),
        				'callback' => array(
        				    'name' => 'invoiceName'
        				)
        			),
        			array(
        				'attribute' => 'deleted',
        				'orderclause' => 'invoice.deleted',
        				'class' => 'bool',
        				'filter' => array(
        				    'tag' => 'bool'
        				),
        				'callback' => array(
        				    'name' => 'isDeletedHumanReadable'
        				)
        			),
        			array(
        			    'attribute' => 'client_id',
        			    'orderclause' => 'client.nickname',
        			    'class' => 'text',
        			    'callback' => array(
        			        'name' => 'clientNickname'
        			    ),
        				'filter' => array(
        				    'tag' => 'text'
        				)
        			),
                    array(
        			    'attribute' => 'clientcode',
        			    'orderclause' => 'invoice.clientcode',
        			    'class' => 'text',
        				'filter' => array(
        				    'tag' => 'text'
        				)
        			),
        			array(
        			    'attribute' => 'card_id',
        			    'orderclause' => 'card.name',
        			    'class' => 'text',
        			    'callback' => array(
        			        'name' => 'cardName'
        			    ),
        				'filter' => array(
        				    'tag' => 'text'
        				)
        			),
        			array(
        			    'attribute' => 'attorney_id',
        			    'orderclause' => 'attorney.name',
        			    'class' => 'text',
        			    'callback' => array(
        			        'name' => 'attorneyName'
        			    ),
        				'filter' => array(
        				    'tag' => 'text'
        				)
        			),
        			array(
        			    'attribute' => 'user_id',
        			    'orderclause' => 'user.name',
        			    'class' => 'text',
        			    'callback' => array(
        			        'name' => 'userName'
        			    ),
        				'filter' => array(
        				    'tag' => 'text'
        				)
        			)
        		);
        }
        return $ret;
	}
	
    /**
     * Returns the translation of token invoice_deleted or an empty string.
     *
     * @param string $attribute
     * @return string
     */
    public function isDeletedHumanReadable($attribute)
    {
        if ( $this->bean->deleted) return __('invoice_label_deleted');//e.g. Storno in german
        return '';
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
			DISTINCT(invoice.id) as id  

		FROM
			invoice

		LEFT JOIN card ON card.id = invoice.card_id
		LEFT JOIN user AS attorney ON attorney.id = invoice.attorney_id
		LEFT JOIN person AS client ON client.id = invoice.client_id
		LEFT JOIN invoicetype ON invoicetype.id = invoice.invoicetype_id
		
		WHERE {$where_clause}

		ORDER BY {$order_clause}

		LIMIT {$offset}, {$limit}
SQL;
        return $sql;
    }

    /**
     * Returns SQL for total.
     *
     * @uses R
     * @param string $where_clause
     * @return string $SQL
     */
    public function sqlForTotal($where_clause = '1')
    {
		$sql = <<<SQL
		SELECT
			COUNT(DISTINCT(invoice.id)) as total

		FROM
			invoice

		LEFT JOIN card ON card.id = invoice.card_id
		LEFT JOIN user AS attorney ON attorney.id = invoice.attorney_id
		LEFT JOIN person AS client ON client.id = invoice.client_id
		LEFT JOIN invoicetype ON invoicetype.id = invoice.invoicetype_id

		WHERE {$where_clause}
SQL;
        return $sql;
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
            $this->bean->name
        );
    }

    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        //$this->bean->setMeta('buildcommand.unique', array(array('name')));
        $this->addConverter('invoicedate', 'mySQLDate');
        $this->addValidator('name', 'hasvalue');
        $this->addValidator('yearname', 'hasvalue');
        $this->addValidator('yearname', 'isunique', array('bean' => $this->bean, 'attribute' => 'yearname'));
        $this->addValidator('invoicedate', 'isdate');
        $this->setAutoInfo(true);
        if ( ! $this->bean->getId()) {
            $this->bean->invoicedate = date('Y-m-d'); // now
        }
    }
    
    /**
     * Set the serial number for this invoice depending on its type.
     *
     * @return int $serialNumber
     */
    public function setInvoiceNumber()
    {        
        // get next number according to invoicetype
        return $this->bean->name = $this->bean->invoicetype()->nextSerial();
    }
    
    /**
     * Update.
     *
     * Once first storage the name attribute will be set to the next
     * number of the given invoice type.
     *
     */
    public function update()
    {
        if ( ! $this->bean->getId()) {
            $this->bean->user = R::dispense('user')->current();
        }
        $this->bean->year = date('Y', strtotime($this->bean->invoicedate));
        $this->bean->m = date('m', strtotime($this->bean->invoicedate));
        $this->bean->d = date('d', strtotime($this->bean->invoicedate));
        $this->bean->yearname = $this->bean->year.$this->bean->name;
        parent::update();
    }
}
