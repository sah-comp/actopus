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
 * Manages multipay on card beans.
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_Multipay extends Controller_Scaffold
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
    public $type = 'multipay';
    
    /**
     * Container for actions.
     *
     * @var array
     */
    public $actions = array(
        'table' => array('expunge'),
        'edit' => array('next', 'prev', 'update', 'list'),
        'add' => array('continue', 'update', 'list')
    );
    
    /**
     * Generates a PDF from the multipayfee beans of this bean.
     *
     * @param int $id Id of the multipay bean
     */
    public function pdf($id)
    {
        $multipay = R::load( 'multipay', $id );
        
        $view = $this->makeView('model/multipay/pdf/paidfee');
        $view->record = $multipay;
        
    	require_once BASEDIR.'/vendors/mpdf/mpdf.php';
        $docname = $multipay->name;
        $filename = $multipay->name . '.pdf';
        $mpdf = new mPDF('c', 'A4');
        $mpdf->SetTitle($docname);
        $mpdf->SetAuthor( $multipay->user->name );
        $mpdf->SetDisplayMode('fullpage');

        $html = $view->render();

        $mpdf->WriteHTML( $html );
        $mpdf->Output($filename, 'D');
        exit;
    }
    
    /**
     * Generates a XML batch payment file for use with EPA online tool.
     *
     * @param int $id of the multipay bean
     */
    public function xmltool($id)
    {
        $setting = R::load( 'setting', 1 );
        $multipay = R::load( 'multipay', $id );
        
        $filename = str_replace( ' ', '-', $multipay->name ) . '.xml';
        
        $total_amount = 0;
        $total_records = 0;
        
        $mfees = $multipay->ownMultipayfee;
        
        
        // the epa batch payment xml
        $xml = new SimpleXMLElement( "<?xml version='1.0' encoding='utf-8'?><!DOCTYPE batch-payment SYSTEM 'batch-payment.dtd'><batch-payment/>" );
            $xml->addAttribute( 'dtd-version', '' );
            $xml->addAttribute( 'date-produced', '' );
            $xml->addAttribute( 'ro', '' );

        // <header>
        $header = $xml->addChild( 'header' );
            $sender = $header->addChild( 'sender' );
                $senderName = $sender->addChild( 'name', $setting->housename1 );
                $senderRegisteredNumber = $sender->addChild( 'registered-number', '' );
            $sendDate = $header->addChild( 'send-date', '' );
            $h_modeOfPayment = $header->addChild( 'mode-of-payment' );
                $h_modeOfPayment->addAttribute( 'payment-type', 'deposit' );
                    $h_depositAccount = $h_modeOfPayment->addChild( 'deposit-account' );
                    $h_accountNo = $h_depositAccount->addChild( 'account-no', str_replace( ' ', '', $setting->houseepoaccount ) );
            $paymentReferenceId = $header->addChild( 'payment-reference-id', $multipay->name );
        // </header>
        
        // <detail>
        $detail = $xml->addChild( 'detail' );
        
        foreach ( $mfees as $id => $mfee ) {
            
            $total_amount += $mfee->amount;
            $total_records++;
            
            // <fee>
            $fees = $detail->addChild( 'fees' );
                $documentId = $fees->addChild( 'document-id');
                    $country = $documentId->addChild( 'country', 'EP' );
                    $docNumber = $documentId->addChild( 'doc-number', $mfee->applicationnumber );
                $fileReferenceId = $fees->addChild( 'file-reference-id', $mfee->cardname );
                $owner = $fees->addChild( 'owner', $mfee->applicantnickname );

                $fee = $fees->addChild( 'fee' );
                    $typeOfFee = $fee->addChild( 'type-of-fee', $mfee->paymentcode );
                    $feeSubAmount = $fee->addChild( 'fee-sub-amount', $mfee->amount );
                    $feeFactor = $fee->addChild( 'fee-factor', 1 );
                    $feeTotalAmount = $fee->addChild( 'fee-total-amount', $mfee->amount );
                    $feeDateDue = $fee->addChild( 'fee-date-due', date( 'd.m.Y', strtotime( $mfee->datedue ) ) );
            // </fee>
        }
        
        // </detail>
        
        // <trailer>
        $trailer = $xml->addChild( 'trailer' );
            $t_modeOfPayment = $trailer->addChild( 'mode-of-payment' );
                $t_modeOfPayment->addAttribute( 'payment-type', 'deposit' );
                    $t_depositAccount = $t_modeOfPayment->addChild( 'deposit-account' );
                    $t_accountNo = $t_depositAccount->addChild( 'account-no', str_replace( ' ', '', $setting->houseepoaccount ) );
            $batchPayTotalAmount = $trailer->addChild( 'batch-pay-total-amount', $total_amount );
                $batchPayTotalAmount->addAttribute( 'currency', '' );
            $totalRecords = $trailer->addChild( 'total-records', $total_records);

        // </trailer>
        
        // send it to browser as a xml file download
        header('Content-type: text/xml');
        header('Content-Disposition: attachment; filename="' . $filename .'"');
        print( $xml->asXML() );
        exit();
    }
}
