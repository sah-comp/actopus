<?php
/**
 * Scaffold import page template.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php echo $this->partial('shared/html5/head') ?>
<?php echo $this->partial('shared/master/header') ?>

<div id="main" class="main">
    <section>
        <header>
            <h1 class="visuallyhidden"><?php echo __('scaffold_h1_import') ?></h1>
        </header>
        
        <form
            id="import-<?php echo $record->model ?>"
            class="panel <?php echo $record->model ?> import"
            method="post"
            action=""
            accept-charset="utf-8"
            enctype="multipart/form-data">
            <div>
                <input type="hidden" name="dialog[type]" value="<?php echo $record->getMeta('type') ?>" />
                <input type="hidden" name="dialog[id]" value="<?php echo $record->getId() ?>" />
                <input
                    type="hidden"
                    name="dialog[delimiter]"
                    value="<?php echo htmlspecialchars($record->delimiter) ?>" />                    
                <input
                    type="hidden"
                    name="dialog[enclosure]"
                    value="<?php echo htmlspecialchars($record->enclosure) ?>" />
            </div>
            <fieldset>
                <legend class="verbose"><?php echo __('optin_legend_import') ?></legend>
                <div>
                    <input type="hidden" name="dialog[model]" value="<?php echo htmlspecialchars($record->model) ?>" />
                </div>
                <div class="row">
                    <label
                        for="import-file"
                        class="<?php echo ($record->hasError('file')) ? 'error' : ''; ?>">
                        <?php echo __('import_label_file') ?>
                    </label>
                    <div class="upload">
                        <input
                            type="text"
                            class="uploaded <?php echo htmlspecialchars($record->extension) ?>"
                            name="void"
                            value="<?php echo htmlspecialchars($record->file) ?>"
                            readonly="readonly" />
                        <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo APP_MAX_FILE_SIZE ?>" />
                        <input
                            id="import-file"
                            type="file"
                            name="file"
                            accept="text/comma-separated-values"
                            value="<?php echo htmlspecialchars($record->file) ?>" />
                            <p class="info"><?php echo __('import_hint_file') ?></p>
                    </div>
                </div>
                <div class="row">
                    <label
                        for="import-encoding"
                        class="<?php echo ($record->hasError('encoding')) ? 'error' : ''; ?>">
                        <?php echo __('import_label_encoding') ?>
                    </label>
                    <select
                        id="import-encoding"
                        name="dialog[encoding]">
                        <?php foreach ($record->encodings() as $_encoding): ?>
                        <option
                            value="<?php echo $_encoding ?>"
                            <?php echo ($record->encoding == $_encoding) ? self::SELECTED : '' ?>><?php echo $_encoding ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </fieldset>
            <div class="toolbar">
                <input
                    type="submit"
                    name="submit"
                    accesskey="s"
                    value="<?php echo __('scaffold_submit_import') ?>" />

            </div>
            <?php if (isset($csv['state']) && $csv['state']): ?>
            <fieldset>
                <legend class="verbose"><?php echo __('optin_legend_import_attributes') ?></legend>
                <table class="mapper csv">
                    <caption><?php echo __('import_map_caption', array($csv['max_records'])) ?></caption>
                    <thead>
                        <tr>
                            <th><?php echo __('import_label_source') ?></th>
                            <th><?php echo __('import_label_value') ?></th>
                            <th><?php echo __('import_label_target') ?></th>
                            <th><?php echo __('import_label_default') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $_counter = 0 ?>
                        <?php foreach ($csv['records'][$csv['current_record']] as $_source => $_value): ?>
                        <?php $_counter++ ?>
                        <?php $_map = $record->map($_source) ?>
                        <tr>
                            <td>
                                <input type="hidden" name="dialog[ownMap][<?php echo $_counter ?>][type]" value="map" />
                                <input type="hidden" name="dialog[ownMap][<?php echo $_counter ?>][id]" value="<?php echo $_map->getId() ?>" />
                                <input
                                    type="hidden"
                                    name="dialog[ownMap][<?php echo $_counter ?>][source]"
                                    value="<?php echo htmlspecialchars($_source) ?>" />
                                <?php echo htmlspecialchars($_source) ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($_value) ?>
                            </td>
                            <td>
                                <select
                                    name="dialog[ownMap][<?php echo $_counter ?>][target]">
                                    <option value="__none__"><?php echo __('import_mapper_dismiss') ?></option>
                                    <?php foreach ($record->model()->attributes() as $_n=>$_attribute): ?>
                                    <option
                                        value="<?php echo $_attribute['orderclause'] ?>"
                                        <?php echo ($_map->target == $_attribute['attribute']) ? self::SELECTED : '' ?>><?php echo __($record->model.'_label_'.$_attribute['attribute']) ?></option>
                                    <?php endforeach ?>
                                </select>
                            </td>
                            <td>
                                <input
                                    type="text"
                                    name="dialog[ownMap][<?php echo $_counter ?>][default]"
                                    value="<?php echo $_map->default ?>" />
                            </td>
                        </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </fieldset>
            <?php echo $this->partial(sprintf('model/%s/import/details', $record->model)) ?>
            <div class="toolbar">
                <input
                    type="submit"
                    name="submit"
                    value="<?php echo __('import_submit_prev') ?>" />
                <input
                    type="submit"
                    name="submit"
                    value="<?php echo __('import_submit_next') ?>" />
                <input
                    type="submit"
                    name="submit"
                    value="<?php echo __('import_submit_execute') ?>" />
            </div>
            <?php endif // endif of: are there already csv records? ?>
        </form>
        
    </section>
    <?php echo $this->partial('shared/scaffold/info') ?>
</div>

<?php echo $this->partial('shared/master/footer') ?>
<?php echo $this->partial('shared/html5/foot') ?>
