<?php
/**
 * Annual index page template.
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

    <form
        id="dialog"
        class="panel annual"
        action="<?php echo $this->url('/annual/index/') ?>"
        method="post"
        accept-charset="utf-8">
        <fieldset>
            <legend class="verbose"><?php echo __('annual_legend') ?></legend>
            <label for="annual-year">
                <?php echo __('annual_label_due_year_month') ?>&nbsp;
            </label>
            <div class="row">
                <div class="xspan2">
                <select
                    id="annual-year"
                    name="dialog[year]">
                    <option
                        value=""><?php echo __('select_a_option') ?></option>
                    <?php foreach ($years as $_year_id => $_year): ?>
                    <option
                        value="<?php echo $_year ?>"
                        <?php echo ($year == $_year) ? self::SELECTED : '' ?>><?php echo $_year ?></option>
                    <?php endforeach ?>
                </select>
                </div>
                <div class="xspan2">
                <select
                    id="annual-month"
                    name="dialog[month]">
                    <option
                        value=""><?php echo __('select_a_option') ?></option>
                    <?php foreach ($months as $_month_id => $_month): ?>
                    <option
                        value="<?php echo $_month_id ?>"
                        <?php echo ($month == $_month_id) ? self::SELECTED : '' ?>><?php echo $_month ?></option>
                    <?php endforeach ?>
                </select>
                </div>
                <div class="xspan3">
                <select
                    id="annual-user"
                    name="dialog[attorney]">
                    <option
                        value=""><?php echo __('select_a_option') ?></option>
                    <?php foreach ($attorneys as $_attorney_id => $_attorney): ?>
                    <option
                        value="<?php echo $_attorney->getId() ?>"
                        <?php echo ($attorney == $_attorney->getId()) ? self::SELECTED : '' ?>><?php echo $_attorney->name ?></option>
                    <?php endforeach ?>
                </select>
                </div>
                <div class="xspan3">
                <select
                    id="annual-team"
                    name="dialog[team]">
                    <option
                        value=""><?php echo __('select_a_option') ?></option>
                    <?php foreach (R::find('team', ' ORDER BY sequence') as $_team_id => $_team): ?>
                    <option
                        value="<?php echo $_team->name ?>"
                        <?php echo ($team == $_team->name) ? self::SELECTED : '' ?>><?php echo $_team->name ?></option>
                    <?php endforeach ?>
                </select>
                </div>
                <div class="xspan2">                
                <select
                    id="annual-status"
                    name="dialog[status]">
                    <option
                        value=""><?php echo __('select_a_option') ?></option>
                    <?php foreach ($stati as $_status => $_status_label): ?>
                    <option
                        value="<?php echo $_status ?>"
                        <?php echo ($status == $_status) ? self::SELECTED : '' ?>><?php echo $_status_label ?></option>
                    <?php endforeach ?>
                </select>
                </div>
            </div>
        </fieldset>
        
        <div class="toolbar">
            <input type="submit" name="submit" value="<?php echo __('annual_submit') ?>" />
        </div>
    </form>

    <section class="list">
        <header>
            <h1 class="visuallyhidden"><?php echo __('annual_h1_index') ?></h1>
        </header>
        
        <?php echo $this->partial('annual/table') ?>
        
    </section>
</div>

<?php echo $this->partial('shared/master/footer') ?>
<?php echo $this->partial('shared/html5/foot') ?>
