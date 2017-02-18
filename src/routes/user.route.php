<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 11-02-17
 * Time: 22:48
 */

$app->get('/user/{userId}', function ($request, $response, $args) {
    $userId = $request->getAttribute('userId');
    $getUrl = 'https://soldiertt.eu.auth0.com/api/v2/users/'.urlencode($userId);

    $this->logger->info("/user/" . $userId);

    $token = getAuth0AccessToken();

    if (!$token) {
        return $response->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode(array('message' => 'Erreur lors de la récupération du token' )));
    };

    $curl = new Curl\Curl();
    $curl->setHeader('Content-Type', 'application/json');
    $curl->setHeader('Authorization', 'Bearer '.$token);
    $curl->get($getUrl);

    if ($curl->error) {
        return $response->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode(array('code' => $curl->error_code, 'message' => $curl->response)));
    } else {

        return $response->withStatus(200)
            ->withHeader('Content-Type', 'application/json')
            ->write($curl->response);
    }
});

$app->patch('/user/{userId}', function ($request, $response, $args) {
    $metadata = $request->getParsedBody();
    $userId = $request->getAttribute('userId');
    $patchUrl = 'https://soldiertt.eu.auth0.com/api/v2/users/'.urlencode($userId);

    $this->logger->info("/user/" . $userId);

    $token = getAuth0AccessToken();

    if (!$token) {
        return $response->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode(array('message' => 'Erreur lors de la récupération du token' )));
    };

    $curl = new Curl\Curl();
    $curl->setHeader('Content-Type', 'application/json');
    $curl->setHeader('Authorization', 'Bearer '.$token);
    $curl->patch($patchUrl, json_encode($metadata), true);

    if ($curl->error) {
        return $response->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode(array('code' => $curl->error_code, 'message' => $curl->response)));
    } else {

        return $response->withStatus(200)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode(array(
                    'message' => 'success'
                ))
            );
    }
});

function getAuth0AccessToken() {
    $curl = new Curl\Curl();
    $curl->setHeader('Accept', 'application/json');
    $curl->post('https://soldiertt.eu.auth0.com/oauth/token',  array(
            'grant_type' => 'client_credentials',
            'client_id' => '0XK492h0etoNyXIbFYnVnAo43mrjywMk',
            'client_secret' => '4NCqiYwsW5djLuaK1ZdtMwgKiswK0iTyvv8OqP3pFvqBbNKBgZRaOZgEEBt7vOHj',
            'audience' => 'https://soldiertt.eu.auth0.com/api/v2/'
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