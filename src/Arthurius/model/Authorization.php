<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 02-11-17
 * Time: 14:51
 */

namespace Arthurius\model;


class Authorization
{
    public static function checkIsAdmin($request) {
        $headerUserId = $request->getHeader('X-Arth-UserId')[0];
        if ($headerUserId != null) {
            return $headerUserId == 'google-oauth2|116562219924228798186' ||
                $headerUserId == 'auth0|5920c0dc549d1f23d38b08d3' ||
                $headerUserId == 'auth0|58bc2dc8a0ba8d24312f907d';
        }
        return false;
    }

    public static function forbidden($response) {
        $error = array('message' => 'Access denied');
        return $response->withStatus(401)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($error));
    }
}