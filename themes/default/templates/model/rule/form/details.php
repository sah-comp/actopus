<?php
/**
 * Rule fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div>    
    <input type="hidden" name="dialog[country][type]" value="country" />
    <input type="hidden" name="dialog[country][id]" value="" />
    <input type="hidden" name="dialog[cardtype][type]" value="cardtype" />
    <input type="hidden" name="dialog[cardtype][id]" value="" />
</div>
<fieldset class="sticky">
    <legend class="verbose"><?php echo __('rule_legend') ?></legend>
    <div class="row">
        <div class="span3">&nbsp;</div>
        <div class="span3">
            <label
                for="rule-country"
                class="left <?php echo ($record->hasError('country_id')) ? 'error' : ''; ?>"><?php echo __('rule_label_country') ?>
            </label>
        </div>
        <div class="span3">
            <label
                for="rule-cardtype"
                class="left <?php echo ($record->hasError('cardtype_id')) ? 'error' : ''; ?>"><?php echo __('rule_label_cardtype') ?>
            </label>
        </div>
    </div>
    <div class="row">
        <div class="span3">&nbsp;</div>
        <div class="span3">
            <select
                id="rule-country"
                class="autowidth"
                name="dialog[country][id]">
                <option value=""><?php echo __('select_a_option') ?></option>
                <?php foreach ($countries as $_country_id => $_country): ?>
                <option
                    value="<?php echo $_country->getId() ?>"
                    <?php echo ($record->country()->getId() == $_country->getId()) ? self::SELECTED : '' ?>><?php echo $_country->name ?></option>
                <?php endforeach ?>
            </select>
        </div>


        <div class="span3">
            <select
                id="rule-cardtype"
                class="autowidth"
                name="dialog[cardtype][id]">
                <option value=""><?php echo __('select_a_option') ?></option>
                <?php foreach ($cardtypes as $_cardype_id => $_cardtype): ?>
                <option
                    value="<?php echo $_cardtype->getId() ?>"
                    <?php echo ($record->cardtype()->getId() == $_cardtype->getId()) ? self::SELECTED : '' ?>><?php echo $_cardtype->name ?></option>
                <?php endforeach ?>
            </select>
        </div>
    </div>
    <div class="row">
        <label for="rule-description"><?php echo __('rule_label_description') ?></label>
        <textarea
            id="rule-description"
            name="dialog[description]"
            class="scaleable"><?php echo htmlspecialchars($record->description) ?></textarea>
    </div>
</fieldset>
<div id="rule-tabs" class="bar tabbed">
    <?php echo $this->tabbed('rule-tabs', array(
        'rule-step' => __('rule_tab_step')
    )) ?>
</div>
<div class="tab-container">
    <fieldset
        id="rule-step"
        class="tab">
        <legend class="verbose"><?php echo __('rule_legend_step') ?></legend>
        
        <!-- Select menu to toggle the form subpart -->
        <div class="row">
            <div class="span3">&nbsp;</div>
            <div class="span3">
                <select
                    name="dialog[style]"
                    class="autowidth"
                    onchange="if ($(this).val() == 0) {$('#rule-perpetual').hide(); $('#rule-limit').show()} else {$('#rule-limit').hide(); $('#rule-perpetual').show()}; return false;">
                    <option value="0" <?php echo ($record->style == 0) ? self::SELECTED : '' ?>><?php echo __('rule_style_0_limit') ?></option>
                    <option value="1" <?php echo ($record->style == 1) ? self::SELECTED : '' ?>><?php echo __('rule_style_1_perpetual') ?></option>
                </select>
            </div>
        </div>
        <!-- End of Select menu for toggeling -->
        
        <!-- container for rule limited -->
        <div
            id="rule-limit"
            style="display: <?php echo ($record->style == 0) ? 'block' : 'none' ?>;">
        
            <div class="row"></div>
        	<div class="row">
        	    <div class="span3"><?php echo __('rule_label_offset') ?></div>
            	<div class="span9"><?php echo __('rule_label_name') ?></div>
        	</div>
        
            <div id="step-container" class="container attachable detachable rulestep">
            <?php foreach ($record->own('rulestep', true) as $_n => $_record): ?>
                <?php echo $this->partial(sprintf('model/%s/form/own/%s', $record->getMeta('type'), 'rulestep'), array('n' => $_n, 'rulestep' => $_record)) ?>
            <?php endforeach ?>    
        	    <a
        			href="<?php echo $this->url(sprintf('/%s/attach/own/%s', $record->getMeta('type'), 'rulestep')) ?>"
        			class="attach"
        			data-target="step-container">
        				<span><?php echo __('scaffold_attach') ?></span>
        		</a>
    		</div>
		
		</div>
		<!-- end of rule-limited -->
		
        <!-- container for rule perpetual -->
        <div
            id="rule-perpetual"
            style="display: <?php echo ($record->style == 1) ? 'block' : 'none' ?>;">
        
            <div class="row">
                <label
                    for="rule-period"
                    class="<?php echo ($record->hasError('period')) ? 'error' : ''; ?>">
                    <?php echo __('rule_label_period') ?>
                </label>
                <input
                    id="rule-perion"
                    type="number"
                    name="dialog[period]"
                    value="<?php echo htmlspecialchars($record->period) ?>" />
            </div>
		
		</div>
		<!-- end of rule perpetual -->
		
	
		
    </fieldset>
</div>
