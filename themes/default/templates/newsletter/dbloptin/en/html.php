<h1>Willkommen!</h1>

<p>Sie oder ein Dritter hat Ihre E-Mail Adresse <?php echo htmlspecialchars($record->email) ?>in den Newsletter-Verteiler eingetragen. Bitte klicken Sie den Aktivierungslink in dieser E-Mail an oder übertragen Sie ihn in die Adresszeile Ihres Browsers, um die eingetragene E-Mail Adresse für den Empfang der Newsletter zu aktivieren.</p>

<p>Link: <a href="<?php echo $this->url(sprintf('/newsletter/activate/%s', $record->hash)) ?>" title="Klicken Sie diesen Link zur Aktivierung"><?php echo $this->Url(sprintf('/newsletter/activate/%s', $record->hash)) ?></a></p>

<p>Falls Sie Ihre E-Mail Adresse nicht für den Empfang aktivieren möchten, tun Sie nichts.</p>

<p>Vielen Dank für die Beachtung aller Hinweise,</p>

<p>Ihr Newsletter-Team.</p>
