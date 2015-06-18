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
 * Manages CURD on card beans.
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_Card extends Controller_Scaffold
{
    /**
     * Holds the name of the attorney role.
     *
     * @const
     */
    const NAMEOFATTORNEYROLE = 'attorney';

    /**
     * Holds the bean type to apply scaffolding to.
     *
     * @var string
     */
    public $type = 'card';
    
    /**
     * Displays a page with a (paginated) selection of beans.
     *
     * Default order dir is descending.
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
     * Displays a page with a (paginated) selection of beans.
     *
     * Default order dir is descending.
     *
     * @param int $page
     * @param int $limit
     * @param string $layout
     * @param int $order
     */
    public function report($page = 1, $limit = self::LIMIT, $layout = self::LAYOUT, $order = 0, $dir = 1)
    {
        return parent::report($page, $limit, $layout, $order, $dir);
    }
    
    /**
     * Dispatches to a certain PDF generator method depending on the template given.
     *
     * @param int $page
     * @param int $limit
     * @param string $layout
     * @param int $order
     * @param int $dir
     * @param string (optional) $template defaults to 'card'
     */
    public function pdf($id, $page = 1, $limit = self::LIMIT, $layout = self::LAYOUT, $order = 0, $dir = 0, $template = 'card')
    {
        $this->cache()->deactivate();
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/'.$this->router()->internalUrl())));
        if ( ! $this->permission()->allowed($this->user(), $this->type, 'edit')) {
    		return $this->error('403');
    	}
    	
    	require_once BASEDIR.'/vendors/fpdf/fpdf.php';
    	$this->trigger('pdf', 'before');
        $callback = 'pdf_'.strtolower($template);
        $this->$callback($id, $page, $limit, $layout, $order, $dir);
        $this->trigger('pdf', 'after');
    }
    
    /**
     * Generates a card PDF.
     *
     * @param int $page
     * @param int $limit
     * @param string $layout
     * @param int $order
     * @param int $dir
     */
    protected function pdf_card($id, $page = 1, $limit = self::LIMIT, $layout = self::LAYOUT, $order = 0, $dir = 0)
    {
    	$this->path = 'model/card/pdf/';
        $this->env($page, $limit, $layout, $order, $dir, $id, 'pdf');
        
        $this->view->title = $this->view->record->name;
        
        $data = $this->view->record->genDataPdf($this->view);
        $title = 'Akte '.$this->view->record->name;
        $this->trigger('pdf', 'before');
        
		$x_offset = -5;
		$y_offset = 0;
		$font_family = 'Helvetica';

        //get rid of undefinded characters in pdf
        $pats = array(
            '–'
        );
        $reps = array(
            '-'
        );

        $blocks = array(
            'client' => array(
                'x' => 12,
                'y' => 13,
                'w' => 70,
                'h' => 4,
                'size' => 10
            ),
            'title' => array(
                'x' => 12,
                'y' => 50,
                'w' => 110,
                'h' => 4,
                'size' => 10
            ),
            'codeword' => array(
                'x' => 12,
                'y' => 68,
                'w' => 110,
                'h' => 4,
                'size' => 10
            ),
            'note' => array(
                'x' => 12,
                'y' => 88,
                'w' => 110,
                'h' => 4,
                'size' => 10
            ),
            'classes' => array(
                'x' => 90,
                'y' => 123,
                'w' => 50,
                'h' => 4,
                'size' => 10,
                'only_if_not_empty' => true,
                'label' => __('card_tab_pattern')
            ),
            'priority' => array(
                'x' => 12,
                'y' => 105,
                'w' => 110,
                'h' => 4,
                'size' => 10,
                'only_if_not_empty' => true,
                'label' => __('card_tab_priority')
            ),
            'foreign' => array(
                'x' => 12,
                'y' => 123,
                'w' => 110,
                'h' => 4,
                'size' => 10
            ),
            'number' => array(
                'x' => 150,
                'y' => 8,
                'w' => 45,
                'h' => 5,
                'size' => 16,
                'font-style' => 'b'
            ),
            'type' => array(
                'x' => 150,
                'y' => 17,
                'w' => 45,
                'h' => 5,
                'size' => 10
            ),
            'country' => array(
                'x' => 150,
                'y' => 27,
                'w' => 45,
                'h' => 5,
                'size' => 10
            ),
			'original_number' => array(
                'x' => 160,
                'y' => 37,
                'w' => 45,
                'h' => 5,
                'size' => 10
            ),
            'application_date' => array(
                'x' => 160,
                'y' => 45,
                'w' => 45,
                'h' => 5,
                'size' => 10
            ),
            'application_number' => array(
                'x' => 160,
                'y' => 53,
                'w' => 45,
                'h' => 5,
                'size' => 10
            ),
            'disclosure_date' => array(
                'x' => 142,
                'y' => 73,
                'w' => 22,
                'h' => 5,
                'size' => 10
            ),
            'disclosure_number' => array(
                'x' => 170,
                'y' => 62,
                'w' => 30,
                'h' => 5,
                'size' => 10
            ),
            'issue_date' => array(
                'x' => 142,
                'y' => 88,
                'w' => 22,
                'h' => 5,
                'size' => 10
            ),
            'issue_number' => array(
                'x' => 170,
                'y' => 80,
                'w' => 30,
                'h' => 5,
                'size' => 10
            )
        );

		$pdf = new FPDF('p', 'mm', 'A4');
		$pdf->SetAutoPageBreak(true, 0);
		$pdf->SetTitle($title);
		$pdf->SetSubject($title);
		//$pdf->SetAuthor('Gonzo');
		//$pdf->SetCreator('Gonzo');
		$pdf->AddPage();
		foreach ($blocks as $attribute=>$options) {
            if (isset($options['only_if_not_empty']) && $options['only_if_not_empty'] && ! $data[$attribute]) {
                continue;
    		}
		    $content = utf8_decode(str_replace($pats, $reps, $data[$attribute]));
		    $pdf->SetY($options['y'] + $y_offset);
		    $pdf->SetX($options['x'] + $x_offset);
		    $font_style = '';
		    $font_size = 10;
		    if (isset($options['font-style'])) {
		        $font_style = $options['font-style'];
		    }
		    if (isset($options['size'])) {
		        $font_size = $options['size'];
		    }
		    $pdf->SetFont($font_family, $font_style, $font_size);
            if (isset($options['label'])) {
                $label = utf8_decode($options['label']);
                $pdf->MultiCell($options['w'], $options['h'], $label, 0, 'L');
                $pdf->SetX($options['x'] + $x_offset);
            }
            $pdf->MultiCell($options['w'], $options['h'], $content, 0, 'L');
		}
		$pdf->Close();
		$pdf->Output($title, 'I');
    }
    
    /**
     * Generates a family PDF.
     *
     * @param int $page
     * @param int $limit
     * @param string $layout
     * @param int $order
     * @param int $dir
     */
    protected function pdf_family($id, $page = 1, $limit = self::LIMIT, $layout = self::LAYOUT, $order = 0, $dir = 0)
    {
    	$this->path = 'model/card/pdf/';
        $this->env($page, $limit, $layout, $order, $dir, $id, 'pdf');
        
        $this->view->title = $this->view->record->name;
        
        //$data = $this->view->record->genDataPdf($this->view);
        $title = 'Akte '.$this->view->record->name;
        $this->trigger('pdf_family', 'before');
        
        $parent = $this->view->record->parent();
        if ( ! $parent->getId()) {
            $parent = $this->view->record;
        }
        $children = array($parent) + $parent->children('sortnumber');
        
        usort($children, function($a, $b) {
            return strcasecmp($a['sortnumber'], $b['sortnumber']);
        });
        
        $printed_on = $this->view->date(date('Y-m-d'));

        $pdf = new FPDF($orientation = 'P', $unit = 'mm', 
        										$format = array(90, 210.35));
        $pdf->setLeftMargin(20);
        $pdf->SetAutoPageBreak(true, 0);
        $pdf->SetTitle($this->record);
        $pdf->SetSubject($this->record);
        $pdf->AddPage();
        $pdf->setFont('Helvetica', 'B', 10);
        $pdf->Cell(90, 3, utf8_decode(__('card_tab_family')), 0, 1);
        $pdf->setFont('Helvetica', '', 7);
        $pdf->Cell(90, 5, utf8_decode($printed_on));
        $pdf->Ln();
        // }}}
        foreach ($children as $id=>$child)
        {
        	$dnumber = $child->name;
        	$dcountry = strtoupper($child->country()->iso);
        	$dtype = $child->cardtypeName();
        	$dstatus = $child->cardstatusName();

            $pdf->Cell(15, 4, utf8_decode($dnumber), 'R');
            $pdf->Cell(8, 4, utf8_decode($dcountry), 'R');
            $pdf->Cell(20, 4, utf8_decode($dtype), 'R');
            $pdf->Cell(20, 5, utf8_decode($dstatus));
            
            $pdf->Ln();
        }

		$pdf->Close();
		$pdf->Output($this->record, 'I');
    }
    
    
    /**
     * Fills a PDF Form using pdftk with given card, setting and cardfeestep.
     *
     * @param int $id of the card bean
     * @param int $id of the cardfeestep bean
     * @return void
     */
    public function dpmaform($id, $cardfeestep_id)
    {
        global $config;
        session_start();
        $cuser = R::dispense('user')->current();
        
        $setting = R::load('setting', 1);// load setting for house data
        $card = R::load('card', $id);// load our card
        $cardfeestep = R::load('cardfeestep', $cardfeestep_id);
        
        require_once BASEDIR.'/vendors/pdftkphp/pdftk-php.php';
        // Initiate the class
		$pdfmaker = new pdftk_php;
		// Fill the data arrays
		$fdf_data_strings = array(
            'First name of payer' => utf8_decode($setting->housename1),
            'Surame of payer' => utf8_decode($setting->housename2),
            'Address line 1' => utf8_decode($setting->houseaddr1),
            'Address line 2' => utf8_decode($setting->houseaddr2 . ' ' . $setting->houseaddr3),
            'Address line 3' => utf8_decode($setting->houseaddr4),
            'Reference of payer' => utf8_decode($card->name . '.' . $card->user()->num . '.' .$cuser->shortname. ' (' . $card->client()->nickname . ')'),
            'EP Number' => utf8_decode($card->applicationnumber),
            'Place and Date' => utf8_decode($setting->houseaddr3 . ', ' . strftime('%B %e, %Y')),
            'Fee no. x11' => utf8_decode('0' . (32 + $cardfeestep->sequence)),
            'Text x11' => utf8_decode(__('dpma_renewal_stamp', array($cardfeestep->sequence + 2), 'en')),
            'EUR_34' => utf8_decode(number_format($cardfeestep->paymentnet, 2, '.', '')),
            'TOTAL EUR' => utf8_decode(number_format($cardfeestep->paymentnet, 2, '.', ''))
		);
		$fdf_data_names = array();
		
		// Setup checkboxes for mode of payment K1 = bank payment, K2 = debit from deposit
		
		if ($setting->houseepoaccount != '') {
		    // mode of payment is K2
		    //$fdf_data_names['K1'] = 'Off';
    		$fdf_data_names['K2'] = 'Ja';
    		$fdf_data_strings['Deposit account no'] = utf8_decode($setting->houseepoaccount);
		} else {
		    // mode of payment is K1
		    $fdf_data_names['K1'] = 'Ja';
    		//$fdf_data_names['K2'] = 'Off';
    		$fdf_data_strings['Name of EPO bank'] = utf8_decode($setting->houseepobank);
		}
		
		//$fdf_data_names['K1'] = 'Ja';
		//$fdf_data_names['K2'] = 'Off';
		
		$fields_hidden = array();
		$fields_readonly = array();
		$pdf_filename = utf8_decode(__('dpma_renewal_pdfname', array($card->name, 2+$cardfeestep->sequence), 'en'));
		$pdf_original = $config['upload']['dir'].'150618-F1010_Gebühren_DE_4-14.pdf';// ??
		//$pdf_original = $config['upload']['dir'].'EPA-Abbuchung-EN-03_15_arial.pdf';//this is new, arial
		// Finally make the actual PDF file!
		$pdfmaker->make_pdf($fdf_data_strings, $fdf_data_names, $fields_hidden, $fields_readonly, $pdf_original, $pdf_filename);
		exit;
    }

    /**
     * Displays a partial dialog to set up fee according to pricetype, country and cardtype.
     *
     * This is called from a ajax post request.
     *
     * @param int (optional) $card_id of the card
     * @param int (optional) $pricetype_id of the pricetype
     * @param int (optional) $country_id of the country
     * @param int (optional) $cardtype_id of the cardtype
     * @param int (optional) $card_rerule flag that decides wether to overwrite existings steps or not
     * @return void
     */
    public function fee($card_id = null, $pricetype_id = null, $country_id = null, $cardtype_id = null,
                        $card_rerule = null)
    {
        session_start();
        $this->cache()->deactivate();
        $this->view = $this->makeView(null);
        $this->before_edit();
        $card = R::load('card', $card_id);
        $cardfeesteps = $card->own('cardfeestep', false);
        if ( $card_rerule) {
            //kill all existing cardfeestep beans
            error_log('Kill existing fee steps');
            $cardfeesteps = array();
        }
        if ( ! $cardfeesteps ) {
            error_log('Build fee steps');
            // do we have a rule?
            if ( ! $rule = R::findOne('rule', ' country_id = ? AND cardtype_id = ? LIMIT 1', array($country_id, $cardtype_id))) {
                $rule = R::dispense('rule');
            }
            if ( ! $fee = R::findOne('fee', ' rule_id = ? AND pricetype_id = ? LIMIT 1', array($rule->getId(), $pricetype_id))) {
                $fee = R::dispense('fee');
            }
            $setting = R::load('setting', 1);
            if ( ! $feebase = R::findOne('fee', ' rule_id = ? AND pricetype_id = ? LIMIT 1', array($rule->getId(), $setting->feebase()->getId()))) {
                $feebase = R::dispense('fee');
            }
            $cardfeesteps = $rule->setupCard($card, $fee, $feebase);   
        }
        $this->view->cardfeesteps = $cardfeesteps;
        //$card->cardtype_id = $cardtype_id;
        //$card->feetype_id = $feetype_id;
        //$card->pricetype_id = $pricetype_id;
        $this->view->record = $card;
        echo $this->view->partial('model/card/form/fee/cardfeesteps');
        return;
    }
    
    /**
     * Marrys two cards.
     *
     * This is called from a ajax post request.
     *
     * @param int (optional) $card_id of the card
     * @param int (optional) $sibling_id of the other card
     * @return void
     */
    public function marry($card_id = null, $sibling_id = null)
    {
        session_start();
        $this->cache()->deactivate();
        $this->view = $this->makeView(null);
        $card = R::load('card', $card_id);
        $sibling = R::load('card', $sibling_id);
        
        $this->marry_workhorse($card, $sibling);

        $this->view->record = $card;
        $this->pushFamilyToView();
        
        echo $this->view->partial('model/card/form/family/members');
        return;
    }
    
    /**
     * Divorces two cards.
     *
     * This is called from a ajax post request.
     *
     * @param int (optional) $card_id of the card
     * @param int (optional) $sibling_id of the other card
     * @return void
     */
    public function divorce($card_id = null, $sibling_id = null)
    {
        session_start();
        $this->cache()->deactivate();
        $this->view = $this->makeView(null);
        $card = R::load('card', $card_id);
        $sibling = R::load('card', $sibling_id);
        
        unset($sibling->card);
        try {
            R::store($sibling);
        } catch (Exception $e) {
            Cinnebar_Logger::instance()->log($e, 'exceptions');
        }
    }
    
    /**
     * Joins the two beans together into one family.
     *
     * Wether the first bean is added to the second beans family or vice versa depends on
     * which one is an orphan. If both are orphans the first bean will be the family owning bean.
     * If the two beans could be joined into a family a redirect to the current record takes place.
     *
     * @param RedBean_OODBBean $male card
     * @param RedBean_OODBBean $female card
     * @return bool $falseWhenTheMarriageFailed
     */
    protected function marry_workhorse(RedBean_OODBBean $male, RedBean_OODBBean $female)
    {
        $grand_dad = $male->parent();
        if ( ! $grand_dad->getId()) {
            $grand_dad = $male;
        }
        $grand_mum = $female->parent();
        if ( ! $grand_mum->getId()) {
            $grand_mum = $female;
        }
        $dad_children = $grand_dad->children();
        $mum_children = $grand_mum->children();
        
        if ( ! $dad_children && ! $mum_children) {
            $grand_mum->card = $grand_dad;
            try {
                R::store($grand_mum);
                return true;
            } catch (Exception $e) {
                Cinnebar_Logger::instance()->log($e, 'exceptions');
                return false;
            }
        }
        
        if ( $dad_children && $mum_children) {
            $grand_mum->card = $grand_dad;
            foreach ($mum_children as $id => $child) {
                $mum_children[$id]->card = $grand_dad;
            }
            try {
                R::storeAll($mum_children);
                R::store($grand_mum);
                return true;
            } catch (Exception $e) {
                Cinnebar_Logger::instance()->log($e, 'exceptions');
                return false;
            }
        }
        
        if ( $dad_children && ! $mum_children) {     
            $grand_mum->card = $grand_dad;       
            try {
                R::store($grand_mum);
                return true;
            } catch (Exception $e) {
                Cinnebar_Logger::instance()->log($e, 'exceptions');
                return false;
            }
        }
        
        // matriachat: grand_dad is an orphan joining grand_mum's family
        // join male to grand_mum
        // return
        $grand_dad->card = $grand_mum;
        try {
            R::store($grand_dad);
            return true;
        } catch (Exception $e) {
            Cinnebar_Logger::instance()->log($e, 'exceptions');
            return false;
        }
    }

    /**
     * This will run before scaffold edit performs.
     *
     * @return void
     */
    public function before_edit()
    {
        $this->pushEnabledCardtypesToView();
        $this->pushEnabledCardstatiToView();
        $this->pushEnabledPricetypesToView();
        $this->pushEnabledFeetypesToView();
        $this->pushEnabledCountriesToView();
        $this->pushEnabledAttorneysToView();
        $this->pushEnabledPaymentstylesToView();
        //$this->pushEnabledPersonsToView();
        $this->pushClaimTypesToView();
        $this->pushFamilyToView();
    }
    
    /**
     * This will run before scaffold add performs.
     *
     * @return void
     */
    public function before_add()
    {
        $this->pushEnabledCardtypesToView();
        $this->pushEnabledCardstatiToView();
        $this->pushEnabledPricetypesToView();
        $this->pushEnabledFeetypesToView();
        $this->pushEnabledCountriesToView();
        $this->pushEnabledAttorneysToView();
        $this->pushEnabledPaymentstylesToView();
        //$this->pushEnabledPersonsToView();
        $this->pushClaimTypesToView();
    }
    
    /**
     * Pushes enabled cardtypes in alphabetic order to the view.
     */
    public function pushEnabledCardtypesToView()
    {
        $this->view->cardtypes = R::find('cardtype', ' 1 ORDER BY name');
    }
    
    /**
     * Pushes enabled paymentstyles in alphabetic order to the view.
     */
    public function pushEnabledPaymentstylesToView()
    {
        $this->view->paymentstyles = R::find('paymentstyle', ' 1 ORDER BY code');
    }
    
    /**
     * Pushes enabled cardtypes in alphabetic order to the view.
     */
    public function pushEnabledPersonsToView()
    {
        $this->view->persons = R::find('person', ' 1 ORDER BY nickname');
    }
    
    /**
     * Pushes enabled cardstati in alphabetic order to the view.
     */
    public function pushEnabledCardstatiToView()
    {
        $this->view->cardstati = R::find('cardstatus', ' 1 ORDER BY name');
    }
    
    /**
     * Pushes enabled pricetypes in alphabetic order to the view.
     */
    public function pushEnabledPricetypesToView()
    {
        $this->view->pricetypes = R::find('pricetype', ' 1 ORDER BY name');
    }
    
    /**
     * Pushes enabled feetypes in alphabetic order to the view.
     */
    public function pushEnabledFeetypesToView()
    {
        $this->view->feetypes = R::find('feetype', ' 1 ORDER BY name');
    }
    
    /**
     * Pushes enabled templates in alphabetic order to the view.
     */
    public function pushEnabledCountriesToView()
    {
        $this->view->countries = R::find('country', ' enabled = 1 ORDER BY name');
    }
    
    /**
     * Pushes enabled attorney in alphabetic order to the view.
     */
    public function pushEnabledAttorneysToView()
    {
        $attorney = R::findOne('role', ' name = ? LIMIT 1', array(self::NAMEOFATTORNEYROLE));
        $this->view->attorneys = R::dispense('user')->belongsToRole($attorney->getId());
    }

    /**
     * Pushes all cards that belong to this cards family to the view.
     */
    public function pushFamilyToView()
    {
        if ( ! $this->view->record || ! $this->view->record->getId()) {
            $this->view->members = array();
            return;
        }
        $parent = $this->view->record->parent();
        if ( ! $parent->getId()) {
            $parent = $this->view->record;
        }
        //$children = array($parent) + $parent->children('sortnumber');
        //$this->view->members = array($parent) + $children;
        //$this->view->members = $children;
        $this->view->members = array($parent) + $parent->children('sortnumber');
    }

    /**
     * Pushes enabled attorney in alphabetic order to the view.
     */
    public function pushClaimTypesToView()
    {
        $this->view->claimtypes = array(
            'application',
            'issue',
            'disclosure'
        );
    }
}
