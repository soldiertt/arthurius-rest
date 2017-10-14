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

     public static function sendMail($userId, $userInfo, $mail) {

         $replyTo = Secrets::$MAIL_FROM;
         $lang = $mail['parameters']['language'] ?: 'fr';
         $subject = self::computeMailSubject($mail, $lang);
         $body = self::computeMailBody($userId, $userInfo, $mail, $lang);
         $env = getenv('ENVIRONMENT') ?: 'development';

         if ($env != 'development') {
             $headers ='From: '.$replyTo."\n";
             $headers .='Reply-To: '.$replyTo."\n";
             $headers .='Content-Type: text/html; charset=utf-8'."\n";
             $headers .='Content-Transfer-Encoding: 8bit';

             $success = mail(self::computeMailTo($mail, $userInfo), $subject, $body, $headers);
             return new MailResponse($success, "Mail sent.");
         } else {
             return new MailResponse(true, $body);
         }
     }

    private static function computeMailBody($userId, $userInfo, $mail, $lang) {

        $body = null;
        switch ($mail['template']) {
            case "ACCOUNT_DELETION":
                $body = file_get_contents(__DIR__ . '/../mailtemplates/account_deletion_request.html');
                $body = str_replace('%userEmail%', $mail['parameters']['userEmail'], $body);
                $body = str_replace('%userId%', $userId, $body);
                 break;
            case "ACCOUNT_DELETION_CANCEL":
                 $body = file_get_contents(__DIR__ . '/../mailtemplates/account_deletion_request_cancelled.html');
                 $body = str_replace('%userEmail%', $mail['parameters']['userEmail'], $body);
                 $body = str_replace('%userId%', $userId, $body);
                 break;
            case "ADMIN_PAYMENT_CONFIRMATION":
                $body = file_get_contents(__DIR__ . '/../mailtemplates/admin_payment_confirmation.html');
                $cart = json_decode($mail['parameters']['paypalOrder']['json']);
                $body = str_replace('%userId%', $userId, $body);
                self::fillMailTemplate($body, $userInfo, $cart, "fr");
                break;
            case "USER_PAYMENT_CONFIRMATION":
                switch ($lang) {
                    case "fr":
                        $body = file_get_contents(__DIR__ . '/../mailtemplates/user_payment_confirmation_fr.html');
                        break;
                    case "nl":
                        $body = file_get_contents(__DIR__ . '/../mailtemplates/user_payment_confirmation_nl.html');
                        break;
                    case "en":
                        $body = file_get_contents(__DIR__ . '/../mailtemplates/user_payment_confirmation_en.html');
                        break;
                }
                $cart = json_decode($mail['parameters']['paypalOrder']['json']);
                self::fillMailTemplate($body, $userInfo, $cart, $lang);
                break;
        }
        return $body;
    }

    private static function fillMailTemplate(&$body, $userInfo, $cart, $lang) {
        $address = self::buildAddress($userInfo);
        $body = str_replace('%userFullname%', $userInfo->user_metadata->name, $body);
        $body = str_replace('%userEmail%', $userInfo->user_metadata->email, $body);
        $body = str_replace('%userAddress%', $address, $body);

        $subtotal = $cart->amount->details->subtotal;
        $shipping = $cart->amount->details->shipping;
        $discount = $cart->amount->details->shipping_discount;
        $total = $cart->amount->total;

        $body = str_replace('%subtotal%', $subtotal, $body);
        $body = str_replace('%shipping%', $shipping, $body);
        if ($discount !== null && $discount !== "0.00") {
            $discountLabel = "";
            switch ($lang) {
                case "fr":
                    $discountLabel = "Réduction";
                    break;
                case "nl":
                    $discountLabel = "Reductie";
                    break;
                case "en":
                    $discountLabel = "Discount";
                    break;
            }
            $discountRowHtml = "<tr><td colspan='2' class='footer-label'>".$discountLabel."</td><td>".$discount." EUR</td></tr>";
            $body = str_replace('%discountRow%', $discountRowHtml, $body);
        } else {
            $body = str_replace('%discountRow%', "", $body);
        }
        $body = str_replace('%total%', $total, $body);
        $cartArticles = self::orderToHtmlRows($cart);
        $body = str_replace('%cartArticles%', $cartArticles, $body);
    }

    private static function buildAddress($userInfo) {
        $delivery = $userInfo->user_metadata->addresses->delivery;
        $html = $delivery->street." ".$delivery->houseNumber;
        if ($delivery->postbox != null) {
            $html .= " / ".$delivery->postbox;
        }
        $html .= "<br/>".$delivery->postcode." ".$delivery->city;
        $html .= "<br/>".$delivery->country;
        return $html;
    }

    private static function orderToHtmlRows($cart) {

        $html = "";
        foreach($cart->items as $item) {
            $name = $item->name;
            $price = $item->price;
            $currency = $item->currency;
            $quantity = $item->quantity;
            $html .= "<tr><td>".$name."</td><td>".$quantity."</td><td>".$price." ".$currency."</td></tr>";
        }
        return $html;
    }

    private static function computeMailSubject($mail, $lang) {
        $subject = null;
        switch ($mail['template']) {
            case "ACCOUNT_DELETION":
                $subject = "[Arthurius] Demande de suppression de compte";
                break;
            case "ACCOUNT_DELETION_CANCEL":
                $subject = "[Arthurius] Demande de suppression de compte annulée";
                break;
            case "ADMIN_PAYMENT_CONFIRMATION":
                $subject = "[Arthurius] Nouvelle commande";
                break;
            case "USER_PAYMENT_CONFIRMATION":
                switch ($lang) {
                    case "fr":
                        $subject = "Confirmation de votre commande Arthurius";
                        break;
                    case "nl":
                        $subject = "Bevestiging van uw bestelling bij Arthurius";
                        break;
                    case "en":
                        $subject = "Confirmation of your order at Arthurius";
                        break;
                }
                break;
        }
        return $subject;
    }

    private static function computeMailTo($mail, $userInfo) {
        $mailto = null;
        switch ($mail['template']) {
            case "ACCOUNT_DELETION":
            case "ACCOUNT_DELETION_CANCEL":
            case "ADMIN_PAYMENT_CONFIRMATION":
                $mailto = EnvUtil::getAdminMailTo();
                break;
            case "USER_PAYMENT_CONFIRMATION":
                $mailto = $userInfo->user_metadata->email;
                break;
        }
        return $mailto;
    }
 }