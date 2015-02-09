<!DOCTYPE html>
<html lang="de">
<head>
	<meta charset="utf-8" />
    <style type="text/css">
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 12px;
        }
        a, a:link, a:hover, a:active, a:visited {
            color: #000091;
        }
        p {
            margin: 0;
            padding-bottom: 1em;
        }
        img { border: 0; }
	</style>
	<title><?php echo htmlspecialchars($record->name) ?></title>
</head>
<body
    style="background-color: #ffffff;">
    <div
        style="width: 700px; padding: 10px; margin: 0 auto; background-color: #fff;">
    <table
        style="width: 680px;">
        <tr>
            <td
                colspan="2">
                <a
                    href="<?php echo $this->url(sprintf('/newsletter/view/%d', $record->newsletter()->getId())) ?>"
                    style="color: #000091; font-size: 14px;">
                    <?php echo __('newsletter_linktext_back_to_overview') ?></a>
            </td>
        </tr>
        <tr>
            <td
                style="vertical-align: middle; color: #aaafaf; font-size: 18px; font-weight: bold; font-style: italic;">
                <?php echo __('month_label_'.$record->newsletter()->issue()->m) ?> <?php echo $record->newsletter()->issue()->y ?>
            </td>
            <td style="text-align: right;">
                <img
                    src="<?php echo $this->basehref().'/../../uploads/fraport-logo.jpg' ?>"
                    width="190px"
                    height="100px"
                    alt="Fraport Logo" /><br /><br />
            </td>
        </tr>
        <tr>
            <td
                colspan="2"
                style="color: #000091; font-size: 30px; font-weight: bold; font-style: italic;">
                <?php echo __('fraport_h1_themendienst') ?>
            </td>
        </tr>
        <tr>
            <td
                colspan="2">
                <br /><br />
            </td>
        </tr>
        <tr>
            <td
                style="color: #000091; font-weight: bold; font-size: 18px; font-style: italic;">
                <?php echo htmlspecialchars($record->name) ?>
            </td>
            <td
                style="text-align: right; vertical-align: bottom;">
                <a href="<?php echo $this->basehref().'/../../uploads/fraport-article-'.$record->getId().'.zip' ?>">
                    <img src="<?php echo $this->basehref().'/../../uploads/fraport-button-download.jpg' ?>" alt="Texte und Bilder downloaden" style="border: 0;" />
                </a>
            </td>
        </tr>
        <tr>            
            <td
                colspan="2"
                style="vertical-align: top;">
                <p
                    style="color: #000091; font-weight: bold;">
                    <?php echo htmlspecialchars($record->teaser) ?>
                </p>
                <br />
                <?php echo $this->textile($record->content) ?>
                <br />
            </td>
        </tr>

        <?php if ($_slices = $record->getownSlice()): ?>
            <?php foreach ($_slices as $_void => $_slice): ?>
                <tr>
                    <td colspan="2">
                    <?php echo $_slice->render($this); ?>
                    </td>
                </tr>
            <?php endforeach ?>
        <?php endif ?>

        <tr>
            <td
                colspan="2"><br /></td>
        </tr>
        <tr>
            <td
                colspan="2">
                <a
                    href="<?php echo $this->url(sprintf('/newsletter/view/%d', $record->newsletter()->getId())) ?>"
                    style="color: #000091; font-size: 14px;">
                    <?php echo __('newsletter_linktext_back_to_overview') ?></a>
            </td>
        </tr>
        <tr>
            <td
                colspan="2">
                <table
                    style="font-size: 10px; color: #aaafaf; width: 100%; border-top: 1px solid #aaafaf;">
                    <tr>
                        <td
                            colspan="3">
                            <br />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Fraport AG<br />
                            Frankfurt Airport Services Worldwide<br />
                            Unternehmenskommunikation<br />
                            60547 Frankfurt
                        </td>                        
                        <td>
                            Verantwortlich: Mike Peter Schweitzer, Pressesprecher<br />
                            Telefon +49 69 690-70555<br />
                            E-Mail <a style="color: #aaafaf;" href="mailto:m.schweitzer@fraport.de">m.schweitzer@fraport.de</a><br />
                            Website <a style="color: #aaafaf;" href="http://www.fraport.de">www.fraport.de</a>
                        </td>
                        <td>
                            Sitz der Gesellschaft:<br />
                            Frankfurt am Main<br />
                            Amtsgericht Frankfurt am Main: HRB 7042<br />
                            Umsatzsteuer-Identifikationsnr.: DE 114150623
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
    </table>
    </div>
</body>
</html>