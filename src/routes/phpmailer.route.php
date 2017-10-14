<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 11-02-17
 * Time: 22:48
 */

use Arthurius\model\Mailer;

$app->post('/mail', function ($request, $response, $args) {

    $mail = $request->getParsedBody();
    $userId = $request->getHeader("X-Arth-User-Identifier")[0];
    $this->logger->info("Slim-Skeleton 'post /mail '".json_encode($mail));

    $env = getenv('ENVIRONMENT') ?: 'development';

    $domain = $_SERVER['HTTP_HOST'];
    $prefix = $_SERVER['HTTPS'] ? 'https://' : 'http://';

    $getUrl = $prefix.$domain;
    if ($env !== "development") {
        $getUrl .= "/api";
    }
    $getUrl .= "/user/".urlencode($userId);

    $curl = new Curl\Curl();
    $curl->setHeader('Content-Type', 'application/json');
    $curl->get($getUrl);

    if ($curl->error) {

        return $response->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode(array('code' => $curl->error_code, 'message' => $curl->response)));
    } else {

        $mailResponse = Mailer::sendMail($userId, json_decode($curl->response), $mail);

        if ($mailResponse->success) {
            return $response->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($mailResponse));
        } else {
            return $response->withStatus(500)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($mailResponse));
        }

    }
});

