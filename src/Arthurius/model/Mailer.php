<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 09-10-16
 * Time: 21:40
 */
namespace Arthurius\model;

use Arthurius\secrets\Secrets;

class Mailer {

    public function __construct($mail) { }

     public static function sendMail($mail) {

         $replyto = Secrets::$MAIL_FROM;
         $body = self::computeMailBody($mail);

         $headers ='From: '.$replyto."\n";
         $headers .='Reply-To: '.$replyto."\n";
         $headers .='Content-Type: text/html; charset=utf-8'."\n";
         $headers .='Content-Transfer-Encoding: 8bit';
         $success = mail(Secrets::$MAIL_TO, self::computeMailSubject($mail), $body, $headers);

         return new MailResponse($success);

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