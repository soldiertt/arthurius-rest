<?php

namespace Arthurius\model;

use Arthurius\secrets\Secrets;

class EnvUtil {

    public static function getPaypalUrl($type) {
        $env = getenv('ENVIRONMENT') ?: 'development';

        if ($type === 'payment') {
            $paymentUrl = 'https://api.sandbox.paypal.com/v1/payments/payment';
            if ($env === 'production') {
                $paymentUrl = 'https://api.paypal.com/v1/payments/payment';
            }

            return $paymentUrl;
        } else if ($type === 'oauth') {
            $paymentUrl = 'https://api.sandbox.paypal.com/v1/oauth2/token';
            if ($env === 'production') {
                $paymentUrl = 'https://api.paypal.com/v1/oauth2/token';
            }

            return $paymentUrl;
        }

        return null;
    }

    public static function getPaypalUser() {
        $env = getenv('ENVIRONMENT') ?: 'development';

        $user = Secrets::$SANDBOX_PAYPAL_USER;
        if ($env === 'production') {
            $user = Secrets::$PROD_PAYPAL_USER;
        }

        return $user;
    }

    public static function getPaypalPassword() {
        $env = getenv('ENVIRONMENT') ?: 'development';

        $pass = Secrets::$SANDBOX_PAYPAL_PASS;
        if ($env === 'production') {
            $pass = Secrets::$PROD_PAYPAL_PASS;
        }

        return $pass;
    }
}