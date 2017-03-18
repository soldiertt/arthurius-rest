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
    $this->logger->info("Slim-Skeleton 'post /mail '".json_encode($mail));

    $mailResponse = Mailer::sendMail($mail);

    if ($mailResponse->success) {
        return $response->withStatus(200)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($mailResponse));
    } else {
        return $response->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($mailResponse));
    }
});

