<?php
/**
 * Update help page template for cli.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>

Cinnebar Update
===============

After you have updated the source code using the current revisioning system you
are using right now, it might be neccessary to update or change stuff in your
database, too. This is when you issue the update command with a certain revision
number. That number may be given to you by the support team or the developer.

Usage: php -f index.php -- -c update [args...]
 
  -h            Display this screen
  --rev         Number of the revision to update to
                The revision number should be documented by the developer teams

Example:

php -f index.php -- -c update --rev 1

This would scan all cards for migrated original card numbers and transforms them
into a card object that is stored under the alias original in a card.
