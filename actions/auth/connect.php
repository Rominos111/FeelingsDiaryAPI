<?php

require_once "__php__";

require_once "utils/random.php";
require_once "objects/User.php";
require_once "shared/JWT.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    (new Response(
        ResponseCode::METHOD_NOT_ALLOWED,
        array("method" => $_SERVER["REQUEST_METHOD"]),
        1,
        "Method not allowed, like using POST in a GET-only request"
    ))->send();
}

if (isset($_POST)) {
    if (!empty($_POST["password"]) && is_string($_POST["password"])) {
        if (!empty($_POST["username"]) && is_string($_POST["username"])) {
            Database::connect();

            $canConnect = User::canConnect($_POST["username"], $_POST["password"]);

            if ($canConnect) {
                $uid = User::getByUsername($_POST["username"])->getId();

                $rft = JWT::getToken($uid, JWT::TOKEN_REFRESH);
                $rqt = JWT::getToken($uid, JWT::TOKEN_REQUEST);

                (new Response(200, array(
                    "refresh_token" => $rft,
                    "request_token" => $rqt
                )))->send();
            }
        }
        else {
            (Response::missingArguments("username"))->send();
        }
    }
    else {
        (Response::missingArguments("password"))->send();
    }
}
