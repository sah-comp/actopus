<?php
/**
 * Scaffold language chooser partial template.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php if ($record->isI18n() && isset($languages) && is_array($languages) && count($languages) > 1): ?>
<div class="languagechooser">
    <select
        name="i18n"
        data-href="<?php echo $this->url('/setting/language/') ?>"
        title="<?php echo __('scaffold_languagechooser_title') ?>">
    <?php foreach ($languages as $_language_id => $_language): ?>
        <option
            value="<?php echo $_language->iso ?>"
            <?php echo ($this->user()->language() == $_language->iso) ? self::SELECTED : '' ?>>
            <?php echo __('language_'.$_language->iso) ?>
        </option>
    <?php endforeach ?>
    </select>
</div>
<?php endif ?>