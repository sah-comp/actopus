<?php
/**
 * ownMultipayfee of Multipay fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div id="multipayfee-<?php echo $n ?>" class="item multipayfee">
	<a
		href="<?php echo $this->url(sprintf('/multipay/detach/own/multipayfee/%d', $n)) ?>"
		class="detach ask"
		data-target="multipayfee-<?php echo $n ?>">
			<span><?php echo __('scaffold_detach') ?></span>
	</a>
	<div>
		<input type="hidden" name="dialog[ownMultipayfee][<?php echo $n ?>][type]" value="multipayfee" />
		<input type="hidden" name="dialog[ownMultipayfee][<?php echo $n ?>][id]" value="<?php echo $multipayfee->getId() ?>" />
	</div>
	<div class="row">
	    <div class="span2">
            <input
                id="multipayfee-<?php echo $n ?>-card"
                type="hidden"
                name="dialog[ownMultipayfee][<?php echo $n ?>][card_id]"
                value="<?php echo $multipayfee->card_id ?>" />
            <input
                id="multipayfee-<?php echo $n ?>-cardname"
                type="text"
                name="dialog[ownMultipayfee][<?php echo $n ?>][cardname]"
                class="autocomplete"
                data-source="<?php echo $this->url('/search/autocomplete/card/name?callback=?') ?>"
                data-spread='<?php echo json_encode(array('multipayfee-' . $n . '-card' => 'id', 'multipayfee-' . $n . '-cardname' => 'cardname', 'multipayfee-' . $n . '-applicantnickname' => 'applicantnickname', 'multipayfee-' . $n . '-applicationnumber' => 'epo')) ?>'
                value="<?php echo htmlspecialchars($multipayfee->cardname) ?>"
                placeholder="<?php echo __('multipayfee_placeholder_cardname') ?>" />
        </div>
	    <div class="span3">
    		<input
    		    id="multipayfee-<?php echo $n ?>-applicantnickname"
    			type="text"
    			name="dialog[ownMultipayfee][<?php echo $n; ?>][applicantnickname]"
    			placeholder="<?php echo __('multipayfee_placeholder_applicantnickname'); ?>"
    			value="<?php echo htmlspecialchars($multipayfee->applicantnickname); ?>" />
    	</div>
	    <div class="span2">
    		<input
    		    id="multipayfee-<?php echo $n ?>-applicationnumber"
    			type="text"
    			name="dialog[ownMultipayfee][<?php echo $n; ?>][applicationnumber]"
    			placeholder="<?php echo __('multipayfee_placeholder_applicationumber'); ?>"
    			value="<?php echo htmlspecialchars($multipayfee->applicationnumber); ?>" />
    	</div>
	    <div class="span1">
    		<input
    		    id="multipayfee-<?php echo $n ?>-paymentcode"
    			type="text"
    			name="dialog[ownMultipayfee][<?php echo $n; ?>][paymentcode]"
    			placeholder="<?php echo __('multipayfee_placeholder_paymentcode'); ?>"
    			value="<?php echo htmlspecialchars($multipayfee->paymentcode); ?>" />
    	</div>
	    <div class="span1">
    		<input
    		    id="multipayfee-<?php echo $n ?>-amount"
    			type="text"
    			class="number"
    			name="dialog[ownMultipayfee][<?php echo $n; ?>][amount]"
    			placeholder="<?php echo __('multipayfee_placeholder_amount'); ?>"
    			value="<?php echo $this->decimal($multipayfee->amount, 2); ?>" />
    	</div>
	    <div class="span2">
    		<input
    		    id="multipayfee-<?php echo $n ?>-datedue"
    			type="text"
    			name="dialog[ownMultipayfee][<?php echo $n; ?>][datedue]"
    			placeholder="<?php echo __('multipayfee_placeholder_datedue'); ?>"
    			value="<?php echo $this->date($multipayfee->datedue); ?>" />
    	</div>

	</div>
	<?php if ( $multipayfee->errorcode ): ?>
	<div class="row">
			<div class="span12">
					<p class="info error"><?php echo __( 'multipayfee_errorcode_' . $multipayfee->errorcode ) ?></p>
			</div>
	</div>
	<?php endif; ?>
</div>
