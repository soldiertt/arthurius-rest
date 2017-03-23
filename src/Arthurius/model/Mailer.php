<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 09-10-16
 * Time: 21:40
 */
namespace Arthurius\model;

use PHPMailer;
use Arthurius\secrets\Secrets;

class Mailer {

    protected $mailer;

    public function __construct($mail) {
        $this->mailer = new PHPMailer;
        $this->mailer->isSMTP();
        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $this->mailer->SMTPDebug = 0;
        //Ask for HTML-friendly debug output
        $this->mailer->Debugoutput = 'html';
        $this->mailer->Host = 'smtp.gmail.com';
        $this->mailer->Port = 587;
        $this->mailer->SMTPSecure = 'tls';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = Secrets::$MAIL_USER;
        $this->mailer->Password = Secrets::$MAIL_PASS;
        $this->mailer->setFrom('noreply@arthurius.be', 'Arthurius');
        $this->mailer->addReplyTo('arthurius.lame@gmail.com', 'Arthurius');
        $this->mailer->addAddress($mail['recipientEmail'], $mail['recipientName']);
        $this->mailer->Subject = self::computeMailSubject($mail);
        $this->mailer->msgHTML(self::computeMailBody($mail), dirname(__FILE__));
        //Replace the plain text body with one created manually
        //$this->mailer->AltBody = 'This is a plain-text message body';
        //Attach an image file
        //$this->mailer->addAttachment('images/phpmailer_mini.png');
        $this->mailer->CharSet = 'UTF-8';
    }

     public static function sendMail($mail) {
         $instance = new self($mail);
         return $instance->send();
     }

     public function send() {
         $mailResponse = new MailResponse();

         if (!$this->mailer->send()) {
             $mailResponse->message = $this->mailer->ErrorInfo;
         } else {
             $mailResponse->success = true;
         }

         return $mailResponse;
     }

     private static function computeMailBody($mail) {
         $body = null;
         switch ($mail['template']) {
             case "ACCOUNT_DELETION":
                $body = file_get_contents(__DIR__ . '/../mailtemplates/account_deletion_request.html');
                $body = str_replace('%userEmail%', $mail['parameters']['userEmail'], $body);
                $body = str_replace('%userId%', $mail['parameters']['userId'], $body);
                 break;
             case "ACCOUNT_DELETION_CANCEL":
                 $body = file_get_contents(__DIR__ . '/../mailtemplates/account_deletion_request_cancelled.html');
                 $body = str_replace('%userEmail%', $mail['parameters']['userEmail'], $body);
                 $body = str_replace('%userId%', $mail['parameters']['userId'], $body);
                 break;
         }
         return $body;
     }

    private static function computeMailSubject($mail) {
        $subject = null;
        switch ($mail['template']) {
            case "ACCOUNT_DELETION":
                $subject = "[Arthurius] Demande de suppression de compte";
                break;
            case "ACCOUNT_DELETION_CANCEL":
                $subject = "[Arthurius] Demande de suppression de compte annulée";
                break;
        }
        return $subject;
    }
 }