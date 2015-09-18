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
 * Manages CURD on invoice beans.
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_Invoice extends Controller_Scaffold
{
    /**
     * Holds the bean type to apply scaffolding to.
     *
     * @var string
     */
    public $type = 'invoice';
    
    /**
     * Displays a page with a (paginated) selection of beans.
     *
     * @param int $page
     * @param int $limit
     * @param string $layout
     * @param int $order
     */
    public function index($page = 1, $limit = self::LIMIT, $layout = self::LAYOUT, $order = 0, $dir = 1)
    {
        return parent::index($page, $limit, $layout, $order, $dir);
    }
    
    /**
     * Adds a new invoice with given type to a given card and redirects back to that card.
     *
     * @param int $invoicetype_id
     * @param int $card_id
     * @return void
     */
    public function with($invoicetype_id, $card_id)
    {
        $this->cache()->deactivate();
        
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/'.$this->router()->internalUrl())));
        
        if ( ! $this->permission()->allowed($this->user(), $this->type, 'add')) {
			return $this->error('403');
		}
		
		$invoice = R::dispense('invoice');
		$card = R::load('card', $card_id);
		$invtype = R::load('invoicetype', $invoicetype_id);
		if ( ! $card->invreceiver_id) {
		    $client = $card->client();
		    $code = $card->clientcode;
		} else {
		    $client = $card->invreceiver();
		    $code = $card->invreceivercode;
		}
		//$client->validationMode(2);//implicit validation
	    $invoice->invoicetype = $invtype;
	    $invoice->card = $card;
	    $invoice->cardname = $card->name;
	    $invoice->client = $client;
	    $invoice->clientnickname = $client->nickname;
	    $invoice->clientcode = $code;
	    $invoice->clientaddress = $client->addressLabelByType()->formatAddress();
	    $invoice->user = $this->user();
	    $invoice->attorney = $card->user();
	    R::begin();
        try {
            $invoice->setInvoiceNumber();
            R::store($invoice);
            R::commit();
            $message = __('action_add_invoice_success', array($invoice->name));
            with(new Cinnebar_Messenger)->notify($this->user(), $message, 'success');
        } catch (Exception $e) {
            R::rollback();
            Cinnebar_Logger::instance()->log($e, 'exceptions');
            $message = __('action_add_error');
            with(new Cinnebar_Messenger)->notify($this->user(), $message, 'error');   
        }
        $this->redirect(sprintf('/card/edit/%d', $card_id));
    }

    /**
     * Displays the bean in a form so it can be added.
     *
     * Our invoice controller overwrites the scaffold controller add method to allow
     * setting the invoice number. This was neccessary to avoid updating the invoice number (name)
     * when migration from the original database.
     *
     * @param int $page
     * @param int $limit
     * @param string $layout
     * @param int $order
     * @param int $dir
     */
    public function add($id = 0, $page = 1, $limit = self::LIMIT, $layout = self::LAYOUT, $order = 0, $dir = 0)
    {
        $this->cache()->deactivate();
        
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/'.$this->router()->internalUrl())));
        
        if ( ! $this->permission()->allowed($this->user(), $this->type, 'add')) {
			return $this->error('403');
		}
        
        $this->env($page, $limit, $layout, $order, $dir, $id, 'add');
        
        $this->trigger('add', 'before');
        
        if ($this->input()->post()) {
            $this->view->record = R::graph($this->input()->post('dialog'), true);
            R::begin();
            try {
                $this->view->record->setInvoiceNumber();
                R::store($this->view->record);
                R::commit();
                $_SESSION['scaffold']['add']['followup'] = $followup = $this->input()->post('action');
                
                $message = __('action_add_invoice_success', array($this->view->record->name));
                with(new Cinnebar_Messenger)->notify($this->user(), $message, 'success');
                
                $this->trigger('add', 'after');
                
                if ($followup == 'list') {
                    $this->redirect(sprintf('/%s/index/%d/%d/%s/%d/%d/', $this->type, 1, self::LIMIT, $this->layout, $this->order, $this->dir));
                }

                if ($followup == 'update') {
                    $this->redirect(sprintf('/%s/edit/%d/%d/%d/%s/%d/%d/', $this->type, $this->view->record->getId(), 1, self::LIMIT, $this->layout, $this->order, $this->dir));
                }
                
                $this->redirect(sprintf('/%s/add', $this->type));

            } catch (Exception $e) {
                R::rollback();
                Cinnebar_Logger::instance()->log($e, 'exceptions');
                $message = __('action_add_error');
                with(new Cinnebar_Messenger)->notify($this->user(), $message, 'error');
                
            }
        }
        
        $this->view->records = array();
        
        $this->trigger('add', 'after');
        
        echo $this->view->render();
    }

    /**
     * This will run before scaffold edit performs.
     *
     * @return void
     */
    public function before_edit()
    {
        $this->pushEnabledInvoicetypesToView();
    }
    
    /**
     * This will run before scaffold add performs.
     *
     * @return void
     */
    public function before_add()
    {
        $this->pushEnabledInvoicetypesToView();
    }
    
    /**
     * Pushes enabled cardtypes in alphabetic order to the view.
     */
    public function pushEnabledInvoicetypesToView()
    {
        $this->view->invoicetypes = R::find('invoicetype', ' 1 ORDER BY id');
    }
}
