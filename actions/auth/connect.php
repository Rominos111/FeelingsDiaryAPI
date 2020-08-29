<?php

$_ENV["ALLOWED_METHODS"] = "POST";
require_once "__php__";

require_once "objects/User.php";

// Vérification validité password
if (empty($_POST["password"])) {
    Response::missingArguments("password")->send();
}

// Vérification validité password
if (!is_string($_POST["password"])) {
    Response::wrongDataType("password", $_POST["password"], "")->send();
}

// Vérification validité username
if (empty($_POST["username"])) {
    Response::missingArguments("username")->send();
}

// Vérification validité username
if (!is_string($_POST["username"])) {
    Repsonse::wrongDataType("username", $_POST["username"], "")->send();
}

$user = User::getByUsername($_POST["username"]);

// Vérification existence user
if (is_null($user)) {
    Response::builder()
        ->setHttpCode(ResponseCode::NOT_FOUND)
        ->setMessage("User not found")
        ->send();
}

$canConnect = User::canConnect($_POST["username"], $_POST["password"]);

// Vérification validité password
if (!$canConnect) {
    Response::builder()
        ->setHttpCode(ResponseCode::UNAUTHORIZED)
        ->setMessage("Wrong password")
        ->send();
}

$uid = User::getByUsername($_POST["username"])->getId();

$rft = JWT::getToken($uid, JWT::TOKEN_REFRESH);
$rqt = JWT::getToken($uid, JWT::TOKEN_REQUEST);

Response::builder()
    ->setHttpCode(ResponseCode::OK)
    ->setPayload(array(
        "refreshToken" => $rft,
        "requestToken" => $rqt
    ))
    ->send();
