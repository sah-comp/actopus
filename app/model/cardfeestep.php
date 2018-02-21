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
 * Manages cardfeestep.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Cardfeestep extends Cinnebar_Model
{
    /**
     * Return wether a link which lets user download a pdftk flattend pdf or nothing.
     *
     * @param Cinnebar_View $view
     * @return string
     */
    public function genLink(Cinnebar_View $view)
    {
        if ($this->bean->paymentstyle == 1) {
            return '<a href="'.$view->url(sprintf('/card/dpmaform/%d/%d/', $this->bean->card->getId(), $this->bean->getId())).'" class="ir dpma-form" title="'.__('pdf_link_title').'">'.__('pdf_link').'</a>';
        }
        return '&nbsp;';
    }

    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        $this->addConverter('net', 'decimal');
        $this->addConverter('additional', 'decimal');

        $this->addConverter('awarenessdate', 'mySQLDate');
        $this->addConverter('awarenessnet', 'decimal');
        $this->addConverter('invoicedate', 'mySQLDate');
        $this->addConverter('invoicenet', 'decimal');
        $this->addConverter('paymentdate', 'mySQLDate');
        $this->addConverter('paymentnet', 'decimal');
    }

    /**
     * update.
     */
    public function update()
    {
        if (! $this->bean->sequence) {
            $this->bean->sequence = 0;
        }
        parent::update();
    }
}
