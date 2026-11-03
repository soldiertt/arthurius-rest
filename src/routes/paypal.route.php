<?php
use Arthurius\model\EnvUtil;

$app->post('/create-order', function ($request, $response, $args) {
    $userId = $request->getHeader('X-Arth-UserId')[0];
    if ($userId == null) {
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json')
            ->write(json_encode(array('message' => 'Authentication required')));
    }

    $cart = $request->getParsedBody();
    $this->logger->info("/create-order /".json_encode($cart));

    $items = array();
    $itemTotal = 0.0;
    if (isset($cart['orders'])) {
        foreach ($cart['orders'] as $order) {
            $quantity = (int) $order['count'];
            $price = round((float) $order['article']['price'], 2);
            $items[] = array(
                'name' => substr($order['article']['name'], 0, 127),
                'quantity' => strval($quantity),
                'unit_amount' => array('currency_code' => 'EUR', 'value' => number_format($price, 2, '.', ''))
            );
            $itemTotal += $price * $quantity;
            if (!empty($order['engraving'])) {
                $items[] = array(
                    'name' => 'Gravure',
                    'quantity' => '1',
                    'unit_amount' => array('currency_code' => 'EUR', 'value' => '15.00')
                );
                $itemTotal += 15.0;
            }
        }
    }
    $itemTotal = round($itemTotal, 2);
    $shipping = round((float) $cart['shipping'], 2);
    $discount = round((float) $cart['promoAmount'], 2);
    $total = round($itemTotal + $shipping - $discount, 2);

    $token = getPaypalAccessToken();
    if (!$token) {
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json')
            ->write(json_encode(array('message' => 'Erreur lors de la recuperation du token')));
    }

    $body = array(
        'intent' => 'CAPTURE',
        'purchase_units' => array(
            array(
                'amount' => array(
                    'currency_code' => 'EUR',
                    'value' => number_format($total, 2, '.', ''),
                    'breakdown' => array(
                        'item_total' => array('currency_code' => 'EUR', 'value' => number_format($itemTotal, 2, '.', '')),
                        'shipping'   => array('currency_code' => 'EUR', 'value' => number_format($shipping, 2, '.', '')),
                        'discount'   => array('currency_code' => 'EUR', 'value' => number_format($discount, 2, '.', ''))
                    )
                ),
                'items' => $items
            )
        )
    );

    $curl = new Curl\Curl();
    $curl->setHeader('Content-Type', 'application/json');
    $curl->setHeader('Authorization', 'Bearer '.$token);
    $curl->post(EnvUtil::getPaypalUrl('orders'), json_encode($body));

    if ($curl->error) {
        $this->logger->error("/create-order /".$curl->response);
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json')
            ->write(json_encode(array('message' => 'PayPal order creation failed', 'details' => json_decode($curl->response))));
    }
    $out = json_decode($curl->response);
    return $response->withStatus(200)->withHeader('Content-Type', 'application/json')
        ->write(json_encode(array('id' => $out->id)));
});

$app->post('/capture-order', function ($request, $response, $args) {
    $data = $request->getParsedBody();
    $orderID = isset($data['orderID']) ? $data['orderID'] : null;
    $userId = isset($data['userId']) ? $data['userId'] : null;
    $headerUserId = $request->getHeader('X-Arth-UserId')[0];
    if ($headerUserId == null || $orderID == null || ($userId != null && $headerUserId !== $userId)) {
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json')
            ->write(json_encode(array('message' => 'Authentication required')));
    }

    $token = getPaypalAccessToken();
    if (!$token) {
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json')
            ->write(json_encode(array('message' => 'Erreur lors de la recuperation du token')));
    }

    $captureUrl = EnvUtil::getPaypalUrl('orders').'/'.rawurlencode($orderID).'/capture';
    $this->logger->info("POST URL ".$captureUrl);

    $curl = new Curl\Curl();
    $curl->setHeader('Content-Type', 'application/json');
    $curl->setHeader('Authorization', 'Bearer '.$token);
    $curl->post($captureUrl, '{}');

    if ($curl->error) {
        $this->logger->error("/capture-order /".$curl->response);
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json')
            ->write(json_encode(array('message' => 'PayPal capture failed', 'details' => json_decode($curl->response))));
    }

    $out = json_decode($curl->response);
    $unit = $out->purchase_units[0];
    $breakdown = isset($unit->amount->breakdown) ? $unit->amount->breakdown : null;

    $items = array();
    if (isset($unit->items)) {
        foreach ($unit->items as $item) {
            $items[] = array(
                'name' => $item->name,
                'price' => $item->unit_amount->value,
                'quantity' => $item->quantity
            );
        }
    }

    $normalized = array(
        'items' => $items,
        'amount' => array(
            'total' => $unit->amount->value,
            'currency' => $unit->amount->currency_code,
            'details' => array(
                'subtotal' => ($breakdown && isset($breakdown->item_total)) ? $breakdown->item_total->value : $unit->amount->value,
                'shipping' => ($breakdown && isset($breakdown->shipping)) ? $breakdown->shipping->value : '0.00',
                'shipping_discount' => ($breakdown && isset($breakdown->discount)) ? $breakdown->discount->value : '0.00'
            )
        )
    );

    return $response->withStatus(200)->withHeader('Content-Type', 'application/json')
        ->write(json_encode($normalized));
});

function getPaypalAccessToken() {
    if (!empty($_SESSION['paypal_token']) && isset($_SESSION['paypal_token_expires'])
        && $_SESSION['paypal_token_expires'] > time()) {
        return $_SESSION['paypal_token'];
    }

    $curl = new Curl\Curl();
    $curl->setBasicAuthentication(EnvUtil::getPaypalUser(), EnvUtil::getPaypalPassword());
    $curl->setHeader('Accept', 'application/json');
    $curl->setHeader('Accept-Language', 'en_US');
    $curl->post(EnvUtil::getPaypalUrl('oauth'), array('grant_type' => 'client_credentials'));

    if ($curl->error) {
        return false;
    }
    $out = json_decode($curl->response);
    $_SESSION['paypal_token'] = $out->access_token;
    $_SESSION['paypal_token_expires'] = time() + ((int) $out->expires_in - 60);
    return $out->access_token;
}
