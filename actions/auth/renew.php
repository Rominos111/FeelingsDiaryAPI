<?php

$_ENV["EXPECTED"] = array(
    "methods" => "POST",
    "args" => array(
        "token" => ""
    )
);

require_once "__php__";

JWTResponse::checkRefreshToken($_POST["token"]);

$uid = JWT::getUserID($_POST["token"]);

$newToken = JWT::getToken($uid, JWT::TOKEN_REQUEST);

Response::builder()
    ->setPayload(array(
        "requestToken" => $newToken
    ))
    ->send();
