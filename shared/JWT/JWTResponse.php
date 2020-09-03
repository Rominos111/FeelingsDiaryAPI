<?php

require_once "__php__";

abstract class JWTResponse {
    public static function checkToken(?string $token, ?int $type = null) : void {
        $type = is_null($type) ? JWT::getTokenType($token) : $type;

        if (empty($token)) {
            Response::missingArguments("token")->send();
        }

        if (!is_string($token)) {
            Response::wrongDataType("token", $token, "")->send();
        }

        if (!JWT::isTokenSyntaxValid($token)) {
            Response::wrongDataSyntax("token")->send();
        }

        if (!JWT::isTokenSignatureValid($token, $type)) {
            Response::builder()
                ->setMessage("Invalid token signature")
                ->setHttpCode(ResponseCode::UNAUTHORIZED)
                ->send();
        }

        if (JWT::isTokenExpired($token)) {
            Response::builder()
                ->setMessage("Expired token")
                ->setHttpCode(ResponseCode::UNAUTHORIZED)
                ->send();
        }

        if (!JWT::isTokenUserAgentValid($token)) {
            Response::builder()
                ->setMessage("Invalid token User-Agent")
                ->setHttpCode(ResponseCode::UNAUTHORIZED)
                ->send();
        }

        if (!JWT::isTokenIPValid($token)) {
            Response::builder()
                ->setMessage("Invalid token IP")
                ->setHttpCode(ResponseCode::UNAUTHORIZED)
                ->send();
        }

        if (!JWT::isTokenWhitelisted($token)) {
            Response::builder()
                ->setMessage("Blacklisted token")
                ->setHttpCode(ResponseCode::UNAUTHORIZED)
                ->send();
        }
    }

    public static function checkRefreshToken(?string $token) : void {
        self::checkToken($token, JWT::TOKEN_REFRESH);
    }

    public static function checkRequestToken(?string $token) : void {
        self::checkToken($token, JWT::TOKEN_REQUEST);
    }
}
