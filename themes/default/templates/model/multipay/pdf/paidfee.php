<!DOCTYPE html>
<html lang="de" class="no-js">
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: sans-serif;
	        font-size: 10pt;
        }
        h1 {
            font-size: 12pt;
        }
        table {
            border-collapse: collapse;
        }
        th {
            text-align: left;
        }
        td {
            vertical-align: top;
            border: 0.1mm solid #000000;
        }
        th.number,
        td.number {
            text-align: right;
        }
        .emphasize {
            font-weight: bold;
        }
        
    </style>
</head>
<body>
    <table width="100%">
        <caption>
            <?php echo htmlspecialchars( $record->name ) ?>
        </caption>
        <thead>
            <tr>
                <th><?php echo __( 'card_label_name' ) ?></th>
                <th><?php echo __( 'card_label_applicationnumber' ) ?></th>
                <th><?php echo __( 'card_label_applicant' ) ?></th>
                <th><?php echo __( 'multipayfee_label_typeoffee' ) ?></th>
                <th><?php echo __( 'cardfeestep_label_year' ) ?></th>                
                <th class="number"><?php echo __( 'multipayfee_label_amount' ) ?></th>
                <th><?php echo __( 'card_label_codeword' ) ?></th>
            </tr>
        </thead>
        <tbody>
    <?php
    $_total_amount = 0;
    $_total_records = 0;
    foreach ($record->ownMultipayfee as $_id => $_fee):
            $_total_amount += $_fee->amount;
            $_total_records++;
            if ( ! $_fee->card->applicant_id ) {
                $_person = R::load( 'person', $_fee->card->client_id );
            } else {
                $_person = R::load( 'person', $_fee->card->applicant_id );
            }
    ?>
            <tr>
                <td><?php echo htmlspecialchars( $_fee->card->name ) ?></td>
                <td><?php echo htmlspecialchars( $_fee->applicationnumber ) ?></td>
                <td><?php echo htmlspecialchars( $_person->name ) ?></td>
                <td><?php echo htmlspecialchars( $_fee->paymentcode ) ?></td>
                <td><?php echo htmlspecialchars( (int)( $_fee->paymentcode - 30 ) ) ?></td>
                <td class="number"><?php echo htmlspecialchars( $this->decimal( $_fee->amount, 2 ) ) ?></td>
                <td><?php echo htmlspecialchars( $_fee->card->codeword ) ?></td>
            </tr>
    <?php endforeach ?>
            <tr>
                <td colspan="5" class="number emphasize"><?php echo __( 'multipay_label_total_amount', $_total_records ) ?></td>
                <td class="number emphasize"><?php echo htmlspecialchars( $this->decimal( $_total_amount, 2 ) ) ?></td>
                <td>&nbsp;</td>
            </tr>
        </tbody>
    </table>
</body>
</html>