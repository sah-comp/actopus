Willkommen!

Sie oder ein Dritter hat Ihre E-Mail Adresse <?php echo $record->email ?>in den Newsletter-Verteiler eingetragen. Bitte klicken Sie den Aktivierungslink in dieser E-Mail an oder übertragen Sie ihn in die Adresszeile Ihres Browsers, um die eingetragene E-Mail Adresse für den Empfang der Newsletter zu aktivieren.

Link: <?php echo $this->url(sprintf('/newsletter/activate/%s', $record->hash)) ?>

Falls Sie Ihre E-Mail Adresse nicht für den Empfang aktivieren möchten, tun Sie nichts.

Vielen Dank für die Beachtung aller Hinweise,

Ihr Newsletter-Team.
