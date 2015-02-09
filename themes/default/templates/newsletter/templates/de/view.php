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
                    href="<?php echo $this->url('/newsletter/archive/') ?>"
                    style="color: #000091; font-size: 14px;">
                    <?php echo __('newsletter_linktext_archive') ?></a>
            </td>
        </tr>
        <tr>
            <td
                style="vertical-align: middle; color: #aaafaf; font-size: 18px; font-weight: bold; font-style: italic;">
                <?php echo __('month_label_'.$record->issue()->m) ?> <?php echo $record->issue()->y ?>
            </td>
            <td style="text-align: right; vertical-align: top;">
                <img
                    src="<?php echo $this->basehref().'/../../uploads/fraport-logo.jpg' ?>"
                    width="190px"
                    height="100px"
                    alt="Fraport Logo" /><br /><br />
            </td>
        </tr>
        <tr>
            <td
                style="color: #000091; font-size: 30px; font-weight: bold; font-style: italic;">
                <?php echo __('fraport_h1_themendienst') ?>
            </td>
            <td
                style="text-align: right;">
                <a href="<?php echo $this->basehref().'/../../uploads/fraport-newsletter-'.$record->getId().'.zip' ?>">
                    <img src="<?php echo $this->basehref().'/../../uploads/fraport-button-download-all.jpg' ?>" alt="Alle Artikel downloaden" style="border: 0;" />
                </a>
            </td>
        </tr>
        <tr>
            <td
                colspan="2"
                style="font-size: 14px; color: #000091; font-style: italic; font-weight: bold;">
                <?php echo $this->textile($record->teaser) ?>
            </td>
        </tr>
        <tr>
            <td
                colspan="2">
                <br /><br /><br /><br /><br />
            </td>
        </tr>
        <?php if ( ! empty($articles)): ?>
            <?php foreach ($articles as $_id => $_article): ?>
        
        <tr>
            <td
                style="color: #000091; font-weight: bold; font-size: 18px; font-style: italic;">
                <?php echo htmlspecialchars($_article->name) ?>
            </td>
            <td
                style="text-align: right; vertical-align: bottom;">
                <a href="<?php echo $this->basehref().'/../../uploads/fraport-article-'.$_article->getId().'.zip' ?>">
                    <img src="<?php echo $this->basehref().'/../../uploads/fraport-button-download.jpg' ?>" alt="Texte und Bilder downloaden" style="border: 0;" />
                </a>
            </td>
        </tr>
        <tr>
            <td
                style="vertical-align: top;"
                colspan="2">
                <table
                    style="width: 100%">
                    <tr>
                        <td
                            style="vertical-align: top; width: 170px;">
                            <img
                                src="<?php echo $this->basehref().'/../../uploads/fraport-article-'.$_article->getId().'-tn.jpg' ?>"
                                width="160px"
                                height="160px"
                                style="margin-right: 10px;"
                                alt="Article Image" />
                        </td>
                        <td
                            style="vertical-align: top;">
                            <p
                                style="color: #000091; font-weight: bold;">
                                <?php echo htmlspecialchars($_article->teaser) ?>
                            </p>
                            <br />
                            <?php echo htmlspecialchars($_article->content) ?>&nbsp;<a
                                href="<?php echo $this->url(sprintf('/newsletter/article/%d/', $_article->getId())) ?>"
                                style="color: #000091;">
                                <?php echo __('newsletter_linktext_read_article') ?>
                            </a>
                            <br />
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td
                colspan="2"><br /><br /><br /><br /></td>
        </tr>
        
            <?php endforeach; ?>
        <?php endif ?>
        
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