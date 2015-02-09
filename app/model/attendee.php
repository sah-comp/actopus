<?php
/**
 * Cinnebar.
 *
 * My lightweight no-framework framework written in PHP.
 *
 * @package Cinnebar
 * @author $Author$
 * @version $Id$
 */

/**
 * Manages attendee beans.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Attendee extends Cinnebar_Model
{   
    /**
     * Prepare and send the opt-in email for this optin bean.
     *
     * If the double opt-in activation email was not already sent to this optin bean, we use
     * PHPMailer to prepare and send the activation mail.
     *
     * @uses $config
     * @uses PHPMailer
     * @todo Get rid of global stuff and dont asume smtp configuration is set properly
     *
     * @param Cinnebar_Controller $controller
     * @return bool
     */
    public function sendMailWithActivationLink(Cinnebar_Controller $controller)
    {
        global $config;
        require_once BASEDIR.'/vendors/PHPMailer_5.2.4/class.phpmailer.php';
        
		$mail = new PHPMailer();
		$mail->CharSet = 'UTF-8';
		$mail->Subject = __('optin_activation_mail_subject');
		
		$mail->From = $config['listmanager']['email'];
		$mail->FromName = $config['listmanager']['name'];
  		$mail->AddReplyTo($config['listmanager']['email'], $config['listmanager']['name']);
  		
		// prepate PHPMailer to use a transporter
		if (isset($config['smtp']['active']) && $config['smtp']['active'])
		{
			$mail->IsSMTP();
			//$mail->SMTPDebug = 2;
			$mail->SMTPAuth = true;
			$mail->Host = $config['smtp']['host'];
			$mail->Port = $config['smtp']['port'];
			$mail->Username = $config['smtp']['user'];
			$mail->Password = $config['smtp']['pw'];
		}
		$body_html = $controller->makeView(sprintf('newsletter/dbloptin/%s/html', $controller->router()->language()));
		$body_text = $controller->makeView(sprintf('newsletter/dbloptin/%s/text', $controller->router()->language()));
		$body_html->record = $body_text->record = $this->bean;
		
		$mail->MsgHTML($body_html->render());
		$mail->AltBody = $body_text->render();
		$mail->AddAddress($this->bean->email);
		
        return $mail->Send();
    }
    
    /**
     * update.
     */
    public function update()
    {
        $this->bean->hash = md5($this->bean->email);
        $this->bean->user = R::findOne('user', ' email = ?', array($this->bean->email));
        parent::update();
    }

    /**
     * Setup validators and set auto info to true.
     */
    public function dispense()
    {
        $this->addValidator('email', 'isemail');
    }
}
