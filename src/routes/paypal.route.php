<?php
// Routes

$app->post('/checkout', function ($request, $response, $args) {
    $parsedCart = $request->getParsedBody();

    $this->logger->info("/checkout /".json_encode($parsedCart));

    $totalAmount = $parsedCart['totalAmount'];
    $subtotalAmount = $parsedCart['subtotalAmount'];
    $shipping = $parsedCart['shipping'];
    $promotion = $parsedCart['promoAmount'];

    $this->logger->info("total amount ".strval($totalAmount));

    $token = getPaypalAccessToken();
    if (!$token) {
        return $response->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode(array('message' => 'Erreur lors de la récupération du token' )));
    };

    $body = array(
        'intent' => 'sale',
        'redirect_urls' => array(
            'return_url' => 'http://arthurius.local.dev/',
            'cancel_url' => 'http://arthurius.local.dev/'
        ),
        'payer' => array(
            'payment_method' => 'paypal'
        ),
        'transactions' => array(
            0 => array(
                'amount' => array(
                    'total' => strval($totalAmount),
                    'currency' => 'EUR',
                    'details' => array (
                        'subtotal' => strval($subtotalAmount),
                        'shipping' => strval($shipping),
                        'shipping_discount' => strval($promotion)
                    )
                ),
                'item_list' => array (
                    'items' => array()
                )
            )
        )
    );

    foreach($parsedCart['orders'] as $order) {
        $quantity = $order['count'];
        $name = $order['article']['name'];
        $price = $order['article']['price'];
        array_push($body['transactions'][0]['item_list']['items'], array(
            'quantity' => strval($quantity),
            'name' => $name,
            'price' => strval($price),
            'currency' => 'EUR'
        ));
    }

    $curl = new Curl\Curl();
    $curl->setHeader('Content-Type', 'application/json');
    $curl->setHeader('Authorization', 'Bearer '.$token);
    $curl->post('https://api.sandbox.paypal.com/v1/payments/payment', json_encode($body) );

    if ($curl->error) {
        $this->logger->error("/checkout /".$curl->response);
        return $response->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode(array('code' => $curl->error_code)));
    } else {
        $outPayment = json_decode($curl->response);
        $paymentId = $outPayment->id;
        return $response->withStatus(200)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode(array (
                    'paymentID' => $paymentId
                ))
            );
    }

});


$app->post('/execute-payment', function ($request, $response, $args) {
    $data = $request->getParsedBody();
    $paymentID = $data['paymentID'];
    $payerID = $data['payerID'];

    $this->logger->info("/execute-payment /".$paymentID.'/'.$payerID);

    $token = getPaypalAccessToken();
    if (!$token) {
        return $response->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode(array('message' => 'Erreur lors de la récupération du token' )));
    };

    $body = array('payer_id' => $payerID );

    $curl = new Curl\Curl();
    $curl->setHeader('Content-Type', 'application/json');
    $curl->setHeader('Authorization', 'Bearer '.$token);

    $postUrl = 'https://api.sandbox.paypal.com/v1/payments/payment/'.$paymentID.'/execute/';

    $this->logger->info("POST URL ".$postUrl);

    $curl->post($postUrl, json_encode($body));

    if ($curl->error) {
        return $response->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode(array('code' => $curl->error_code )));
    } else {
        return $response->withStatus(200)
            ->withHeader('Content-Type', 'application/json')
            ->write($curl->response);
    }

});

$app->get('/payment-detail', function ($request, $response, $args) {
    $paymentID = $request->getQueryParam('paymentID');
    $token = getPaypalAccessToken();
    if (!$token) {
        return $response->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode(array('message' => 'Erreur lors de la récupération du token' )));
    };

    $curl = new Curl\Curl();
    $curl->setHeader('Content-Type', 'application/json');
    $curl->setHeader('Authorization', 'Bearer '.$token);

    $getUrl = 'https://api.sandbox.paypal.com/v1/payments/payment/'.$paymentID;

    $this->logger->info("/payment-detail /".$getUrl);

    $curl->get($getUrl);

    if ($curl->error) {
        return $response->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode(array('message' => $curl->error_code )));
    } else {
        return $response->withStatus(200)
            ->withHeader('Content-Type', 'application/json')
            ->write($curl->response);
    }

});

function getPaypalAccessToken() {
    $curl = new Curl\Curl();
    $curl->setBasicAuthentication('AWM97ZD5w8pGO3EeGBOPmJqCRAltflEBAVHO9W7Hp1nXa48_f1_vwnrfVfMqZyoGcw6Jf3qZvA2d_1j2', 'ELh-J0fF5lpiQ4S3y3H3FMg3VKJKSTIirxrBZeSykMZFQ9bAC7_3MfqtxeZtZDhAGGKNiNWw9JEbuPPt');
    $curl->setHeader('Accept', 'application/json');
    $curl->setHeader('Accept-Language', 'en_US');
    $curl->post('https://api.sandbox.paypal.com/v1/oauth2/token',  array(
            'grant_type' => 'client_credentials'
        )
    );

    if ($curl->error) {
        return false;
    } else {
        $out = json_decode($curl->response);
        $token = $out->access_token;
        return $token;
    }
}