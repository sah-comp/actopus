<!DOCTYPE html>
<html class="no-js">
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: sans-serif;
	        font-size: 10pt;
        }
        table {
            border-collapse: collapse;
        }
        caption {
            font-weight: bold;
            padding-bottom: 3mm;
        }
        td {
            vertical-align: top;
        }
        th {
            text-align: left;
            border-bottom: 0.1em solid gray;
        }
        th.number,
        td.number {
            text-align: right;
        }     
    </style>
</head>
<body>
    <!--mpdf
    <htmlpageheader name="vrheader" style="display: none;">
        <table width="100%">
            <tr>
                <td width="60%" style="text-align: left;"><?php echo __($record->getMeta('type').'_head_title') ?></td>
                <td width="40%" style="text-align: right;"></td>
            </tr>
        </table>
    </htmlpageheader>
    <htmlpagefooter name="vrfooter" style="display: none;">
        <div style="border-top: 0.1mm solid #000000; font-size: 9pt; text-align: center; padding-top: 3mm;">
            <?php echo __('page') ?> {PAGENO} <?php echo __('of') ?> {nbpg}
        </div>
    </htmlpagefooter>
    <sethtmlpageheader name="vrheader" value="on" show-this-page="1" />
    <sethtmlpagefooter name="vrfooter" value="on" />
    mpdf-->
    
    <table class="htmlpdf" width="100%">
        <thead>
            <tr>
                <?php foreach ($attributes as $_i => $_attribute): ?>
                <?php 
                    $_width = '5%';
                    $_class = ''; 
                    if (isset($_attribute['class'])) $_class = $_attribute['class'];
                    if (isset($_attribute['width'])) $_width = $_attribute['width'];
                ?>
                <th width="<?php echo $_width ?>" class="<?php echo $_class ?>">
                    <?php echo __($record->getMeta('type').'_label_'.$_attribute['attribute']) ?>
                </th>
                <?php endforeach ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($records as $_id => $_record): ?>
            <tr class="">
                <?php echo $this->beanrow($_record, $attributes, $surpressHtmlspecialchars = true) ?>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</body>
</html>
