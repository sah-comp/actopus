<?php
/**
 * Housekeeping partial template.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php if ( ! isset($user) || ! is_a($user, 'RedBean_OODBBean')) return ?>
<section
    id="housekeeping"
    class="interval"
    data-container="housekeeping"
    data-delay="60000"
    data-href="<?php echo $this->url('/home/housekeeping') ?>">
</section>
<!-- End of housekeeping -->
