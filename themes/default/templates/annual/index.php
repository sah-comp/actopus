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
            <div class="row">
                <label for="annual-year">
                    <?php echo __('annual_label_due_year_month') ?>
                </label>
                <select
                    id="annual-year"
                    name="dialog[year]">
                    <?php foreach ($years as $_year_id => $_year): ?>
                    <option
                        value="<?php echo $_year ?>"
                        <?php echo ($year == $_year) ? self::SELECTED : '' ?>><?php echo $_year ?></option>
                    <?php endforeach ?>
                </select>
                <select
                    id="annual-month"
                    name="dialog[month]">
                    <?php foreach ($months as $_month_id => $_month): ?>
                    <option
                        value="<?php echo $_month_id ?>"
                        <?php echo ($month == $_month_id) ? self::SELECTED : '' ?>><?php echo $_month ?></option>
                    <?php endforeach ?>
                </select>
                <select
                    id="annual-user"
                    name="dialog[attorney]">
                    <?php foreach ($attorneys as $_attorney_id => $_attorney): ?>
                    <option
                        value="<?php echo $_attorney->getId() ?>"
                        <?php echo ($attorney == $_attorney->getId()) ? self::SELECTED : '' ?>><?php echo $_attorney->name ?></option>
                    <?php endforeach ?>
                </select>
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
