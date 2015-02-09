<?php
/**
 * Sandbox partial for navigation.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<ul>
    <ul>
        <li><a href="<?php echo $this->url('/sandbox/index/token/') ?>">Basic</a>
            <ul>
                <li><a href="#form">Form</a></li>
                <li><a href="#url">URL Parameter</a></li>
                <li><a href="#session">Session</a></li>
                <li><a href="#records">Records</a></li>
                <li><a href="#pagination">Pagination</a></li>
            </ul>
        </li>
        <li><a href="<?php echo $this->url('/iamnotthere/') ?>">Unfound page</a></li>
        <li><a href="<?php echo $this->url('/sandbox/partial/') ?>">Page Assembly with Partials</a></li>
        <li><a href="<?php echo $this->url('/sandbox/menu/') ?>">Hierarchical menus</a></li>
        <li><a href="<?php echo $this->url('/sandbox/webdav/') ?>">WebDAV</a></li>
</ul>