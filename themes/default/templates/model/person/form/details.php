<?php
/**
 * Person fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div>
    <input type="hidden" name="dialog[user][type]" value="user" />
    <input type="hidden" name="dialog[user][id]" value="" />
</div>
<fieldset
    class="sticky">
    <legend class="verbose"><?php echo __('person_legend') ?></legend>
    <div class="row">
        <div class="span3">&nbsp;</div>
        <div class="span2">
            <label
                for="person-nickname"
                class="<?php echo ($record->hasError('nickname')) ? 'error' : ''; ?>"><?php echo __('person_label_nickname') ?>
            </label>
        </div>
        <div class="span2">
            <label
                for="person-language"
                class="<?php echo ($record->hasError('language_id')) ? 'error' : ''; ?>"><?php echo __('person_label_language') ?>
            </label>
        </div>
        <div class="span2">
            <label
                for="person-account"
                class="<?php echo ($record->hasError('account')) ? 'error' : ''; ?>"><?php echo __('person_label_account') ?>
            </label>
        </div>
        <div class="span2">
            <label
                for="person-taxid"
                class="<?php echo ($record->hasError('taxid')) ? 'error' : ''; ?>"><?php echo __('person_label_taxid') ?>
            </label>
        </div>
        <div class="span1">
            <label
                for="person-user"
                class="<?php echo ($record->hasError('user_id')) ? 'error' : ''; ?>"><?php echo __('person_label_user') ?>
            </label>
        </div>
    </div>
    <div class="row">
        <div class="span3">&nbsp;</div>
        <div class="span2">
            <input
                id="person-nickname"
                class="autowidth"
                type="text"
                name="dialog[nickname]"
                value="<?php echo htmlspecialchars($record->nickname) ?>"
                placeholder="<?php echo __('person_placeholder_nickname') ?>"
                required="required"
                <?php //echo (($this->user()->hasRole('administrator') && $record->getId()) || ! $record->getId()) ? '' : self::READONLY ?> />
        </div>
        
        <div class="span2">
            <select
                id="person-language"
                class="autowidth"
                name="dialog[iso]">
                <?php foreach ($languages as $_language_id => $_language): ?>
                <option
                    value="<?php echo $_language->iso ?>"
                    <?php echo ($record->iso == $_language->iso) ? self::SELECTED : '' ?>><?php echo __('language_'.$_language->iso) ?></option>
                <?php endforeach ?>
            </select>
        </div>


        <div class="span2">
            <input
                id="person-account"
                class="autowidth"
                type="text"
                name="dialog[account]"
                value="<?php echo htmlspecialchars($record->account) ?>"
                placeholder="<?php echo __('person_placeholder_account') ?>" />
        </div>
        
        <div class="span2">
            <input
                id="person-taxid"
                class="autowidth"
                type="text"
                name="dialog[taxid]"
                value="<?php echo htmlspecialchars($record->taxid) ?>"
                placeholder="<?php echo __('person_placeholder_taxid') ?>" />
        </div>
        <div class="span1">

            <select
                id="person-user"
                class="autowidth"
                name="dialog[user][id]">
                <?php foreach ($users as $_user_id => $_user): ?>
                <option
                    value="<?php echo $_user->getId() ?>"
                    <?php echo ($record->user()->getId() == $_user->getId()) ? self::SELECTED : '' ?>><?php echo $_user->name ?></option>
                <?php endforeach ?>
            </select>
        </div>
    </div>
</fieldset>



<fieldset>
    <legend class="verbose"><?php echo __('person_legend_details') ?></legend>
    <div class="row">
        <label
            for="person-attention"
            class="<?php echo ($record->hasError('attention')) ? 'error' : ''; ?>">
            <?php echo __('person_label_attention') ?>
        </label>
        <input
            id="person-attention"
            type="text"
            name="dialog[attention]"
            value="<?php echo htmlspecialchars($record->attention) ?>" />
    </div>
    <div class="row">
        <label
            for="person-title"
            class="<?php echo ($record->hasError('title')) ? 'error' : ''; ?>">
            <?php echo __('person_label_title') ?>
        </label>
        <input
            id="person-title"
            type="text"
            name="dialog[title]"
            value="<?php echo htmlspecialchars($record->title) ?>" />
    </div>
    <div class="row">
        <label
            for="person-firstname"
            class="<?php echo ($record->hasError('firstname')) ? 'error' : ''; ?>">
            <?php echo __('person_label_firstname') ?>
        </label>
        <input
            id="person-firstname"
            type="text"
            name="dialog[firstname]"
            value="<?php echo htmlspecialchars($record->firstname) ?>" />
    </div>
    <div class="row">
        <label
            for="person-lastname"
            class="<?php echo ($record->hasError('lastname')) ? 'error' : ''; ?>">
            <?php echo __('person_label_lastname') ?>
        </label>
        <input
            id="person-lastname"
            type="text"
            name="dialog[lastname]"
            value="<?php echo htmlspecialchars($record->lastname) ?>" />
    </div>
    <div class="row">
        <label
            for="person-suffix"
            class="<?php echo ($record->hasError('suffix')) ? 'error' : ''; ?>">
            <?php echo __('person_label_suffix') ?>
        </label>
        <input
            id="person-suffix"
            type="text"
            name="dialog[suffix]"
            value="<?php echo htmlspecialchars($record->suffix) ?>" />
    </div>
    <div class="row">
        <label
            for="person-organization"
            class="<?php echo ($record->hasError('organization')) ? 'error' : ''; ?>">
            <?php echo __('person_label_organization') ?>
        </label>
        <textarea
            id="person-organization"
            class="scaleable"
            name="dialog[organization]"
            rows="2"><?php echo htmlspecialchars($record->organization) ?></textarea>
    </div>
    <div class="row">
        <input
            type="hidden"
            name="dialog[company]"
            value="0" />
        <input
            id="person-company"
            type="checkbox"
            name="dialog[company]"
            <?php echo ($record->company) ? self::CHECKED : '' ?>
            value="1" />
        <label
            for="person-company"
            class="cb <?php echo ($record->hasError('company')) ? 'error' : ''; ?>">
            <?php echo __('person_label_company') ?>
        </label>
    </div>
    <div class="row">
        <label
            for="person-jobtitle"
            class="<?php echo ($record->hasError('jobtitle')) ? 'error' : ''; ?>">
            <?php echo __('person_label_jobtitle') ?>
        </label>
        <input
            id="person-jobtitle"
            type="text"
            name="dialog[jobtitle]"
            value="<?php echo htmlspecialchars($record->jobtitle) ?>" />
    </div>
    <div class="row">
        <label
            for="person-department"
            class="<?php echo ($record->hasError('department')) ? 'error' : ''; ?>">
            <?php echo __('person_label_department') ?>
        </label>
        <input
            id="person-department"
            type="text"
            name="dialog[department]"
            value="<?php echo htmlspecialchars($record->department) ?>" />
    </div>
</fieldset>
<div id="person-tabs" class="bar tabbed">
    <?php echo $this->tabbed('person-tabs', array(
        'person-email' => __('person_tab_email'),
        'person-phone' => __('person_tab_phone'),
        'person-url' => __('person_tab_url'),
        'person-address' => __('person_tab_address'),
        'person-card' => __('person_tab_card'),
        'person-invoice' => __('person_tab_invoice')
    )) ?>
</div>
<div class="tab-container">
    <fieldset
        id="person-email"
        class="tab">
        <legend class="verbose"><?php echo __('email_legend') ?></legend>
        
    	<div class="row">
    	    <div class="span3"><?php echo __('email_label_label') ?></div>
        	<div class="span9"><?php echo __('email_label_value') ?></div>
    	</div>
    	
        <div id="email-container" class="container attachable detachable email">
        <?php foreach ($record->own('email', true) as $_n => $_record): ?>
            <?php echo $this->partial(sprintf('model/%s/form/own/%s', $record->getMeta('type'), 'email'), array('n' => $_n, 'email' => $_record)) ?>
        <?php endforeach ?>    
    	    <a
    			href="<?php echo $this->url(sprintf('/%s/attach/own/%s', $record->getMeta('type'), 'email')) ?>"
    			class="attach"
    			data-target="email-container">
    				<span><?php echo __('scaffold_attach') ?></span>
    		</a>
		</div>
    </fieldset>
    <fieldset
        id="person-phone"
        class="tab">
        <legend class="verbose"><?php echo __('phone_legend') ?></legend>
        
    	<div class="row">
    	    <div class="span3"><?php echo __('phone_label_label') ?></div>
        	<div class="span9"><?php echo __('phone_label_value') ?></div>
    	</div>
    
        <div id="phone-container" class="container attachable detachable phone">
        <?php foreach ($record->own('phone', true) as $_n => $_record): ?>
            <?php echo $this->partial(sprintf('model/%s/form/own/%s', $record->getMeta('type'), 'phone'), array('n' => $_n, 'phone' => $_record)) ?>
        <?php endforeach ?>    
    	    <a
    			href="<?php echo $this->url(sprintf('/%s/attach/own/%s', $record->getMeta('type'), 'phone')) ?>"
    			class="attach"
    			data-target="phone-container">
    				<span><?php echo __('scaffold_attach') ?></span>
    		</a>
		</div>
    </fieldset>
    <fieldset
        id="person-url"
        class="tab">
        <legend class="verbose"><?php echo __('url_legend') ?></legend>
        
    	<div class="row">
    	    <div class="span3"><?php echo __('url_label_label') ?></div>
        	<div class="span9"><?php echo __('url_label_value') ?></div>
    	</div>
    
        <div id="url-container" class="container attachable detachable url">
        <?php foreach ($record->own('url', true) as $_n => $_record): ?>
            <?php echo $this->partial(sprintf('model/%s/form/own/%s', $record->getMeta('type'), 'url'), array('n' => $_n, 'url' => $_record)) ?>
        <?php endforeach ?>    
    	    <a
    			href="<?php echo $this->url(sprintf('/%s/attach/own/%s', $record->getMeta('type'), 'url')) ?>"
    			class="attach"
    			data-target="url-container">
    				<span><?php echo __('scaffold_attach') ?></span>
    		</a>
		</div>
    </fieldset>
    <fieldset
        id="person-address"
        class="tab">
        <legend class="verbose"><?php echo __('address_legend') ?></legend>
        
    	<div class="row">
    	    <div class="span3"><?php echo __('address_label_label') ?></div>
        	<div class="span9"><?php echo __('address_label_value') ?></div>
    	</div>
    
        <div id="address-container" class="container attachable detachable address">
        <?php foreach ($record->own('address', true) as $_n => $_record): ?>
            <?php echo $this->partial(sprintf('model/%s/form/own/%s', $record->getMeta('type'), 'address'), array('n' => $_n, 'address' => $_record)) ?>
        <?php endforeach ?>    
    	    <a
    			href="<?php echo $this->url(sprintf('/%s/attach/own/%s', $record->getMeta('type'), 'address')) ?>"
    			class="attach"
    			data-target="address-container">
    				<span><?php echo __('scaffold_attach') ?></span>
    		</a>
		</div>
    </fieldset>
    <fieldset
        id="person-card"
        class="tab">
        <legend class="verbose"><?php echo __('address_legend_card') ?></legend>

        <?php $_attributes = R::dispense('card')->attributes() ?>
        <table>
            <thead>
                <tr>
                    <th class="scaffold-action">&nbsp;</th>
                    <?php foreach ($_attributes as $_i => $_attribute): ?>
                        <?php
                            $_class = 'card fn-'.$_attribute['attribute'].' order';
                            if (isset($_attribute['class'])) $_class .= ' '.$_attribute['class'];
                        ?>
                    <th class="<?php echo $_class ?>">
                        <a href="#"><?php echo __('card_label_'.$_attribute['attribute']) ?></a>
                    </th>
                    <?php endforeach ?>
                </tr>
            </thead>
            <tbody>
                <?php $_row = 0 ?>
                <?php foreach (R::find('card', ' client_id = :pid OR invreceiver_id = :pid OR applicant_id = :pid OR foreign_id = :pid ORDER BY sortnumber DESC ', array(':pid' => $record->getId())) as $_n => $_record): ?>
                    <?php $_row++ ?>
                <tr
                    id="<?php echo $_record->getMeta('type') ?>-<?php echo $_record->getId() ?>"
                    class="<?php echo $_record->getMeta('type') ?> table item <?php echo ($_record->invalid()) ? 'error' : '' ?> <?php echo ($_record->deleted()) ? 'deleted' : '' ?>">
                    <td class="action">
                        <a href="<?php echo $this->url(sprintf('/%s/edit/%d', $_record->getMeta('type'), $_record->getId())) ?>" title="<?php echo __('scaffold_action_title_edit') ?>" class="edit ir"><?php echo __('action_edit') ?></a>
                    <?php echo $this->beanrow($_record, $_attributes, $surpressHtmlspecialchars = true) ?>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>

    </fieldset>
    <fieldset
        id="person-invoice"
        class="tab">
        <legend class="verbose"><?php echo __('address_legend_invoice') ?></legend>

        <?php $_attributes = R::dispense('invoice')->attributes() ?>
        <table>
            <thead>
                <tr>
                    <th class="scaffold-action">&nbsp;</th>
                    <?php foreach ($_attributes as $_i => $_attribute): ?>
                        <?php
                            $_class = 'invoice fn-'.$_attribute['attribute'].' order';
                            if (isset($_attribute['class'])) $_class .= ' '.$_attribute['class'];
                        ?>
                    <th class="<?php echo $_class ?>">
                        <a href="#"><?php echo __('invoice_label_'.$_attribute['attribute']) ?></a>
                    </th>
                    <?php endforeach ?>
                </tr>
            </thead>
            <tbody>
                <?php $_row = 0 ?>
                <?php foreach (R::find('invoice', ' client_id = ? ORDER BY invoicedate DESC LIMIT 500', array($record->getId())) as $_n => $_record): ?>
                    <?php $_row++ ?>
                <tr
                    id="<?php echo $_record->getMeta('type') ?>-<?php echo $_record->getId() ?>"
                    class="<?php echo $_record->getMeta('type') ?> table item <?php echo ($_record->invalid()) ? 'error' : '' ?> <?php echo ($_record->deleted()) ? 'deleted' : '' ?>">
                    <td class="action">
                        <a href="<?php echo $this->url(sprintf('/%s/edit/%d', $_record->getMeta('type'), $_record->getId())) ?>" title="<?php echo __('scaffold_action_title_edit') ?>" class="edit ir"><?php echo __('action_edit') ?></a>
                    <?php echo $this->beanrow($_record, $_attributes, $surpressHtmlspecialchars = true) ?>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>

    </fieldset>
</div>
