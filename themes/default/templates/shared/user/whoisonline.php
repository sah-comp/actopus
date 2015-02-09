<?php
/**
 * Who is online partial template.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<section
    id="whoisonline"
    class="interval"
    data-container="whoisonline"
    data-delay="60000"
    data-href="<?php echo $this->url('/home/whoisonline') ?>">
    <?php echo $this->partial('shared/user/gravatars') ?>
</section>