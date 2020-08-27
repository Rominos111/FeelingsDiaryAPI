<?php

$_ENV["ALLOWED_METHODS"] = "POST";
require_once "__php__";

require_once "objects/User.php";
require_once "shared/JWT.php";

if (isset($_POST)) {
    if (!empty($_POST["password"]) && is_string($_POST["password"])) {
        if (!empty($_POST["username"]) && is_string($_POST["username"])) {
            $user = User::getByUsername($_POST["username"]);

            if (!is_null($user)) {
                $canConnect = User::canConnect($_POST["username"], $_POST["password"]);

                if ($canConnect) {
                    $uid = User::getByUsername($_POST["username"])->getId();

                    $rft = JWT::getToken($uid, JWT::TOKEN_REFRESH);
                    $rqt = JWT::getToken($uid, JWT::TOKEN_REQUEST);

                    Response::builder()
                        ->setHttpCode(ResponseCode::OK)
                        ->setContent(array(
                            "refreshToken" => $rft,
                            "requestToken" => $rqt
                        ))
                        ->send();
                }
                else {
                    Response::builder()
                        ->setHttpCode(ResponseCode::UNAUTHORIZED)
                        ->setMessage("Wrong password")
                        ->send();
                }
            }
            else {
                Response::builder()
                    ->setHttpCode(ResponseCode::NOT_FOUND)
                    ->setMessage("User not found")
                    ->send();
            }
        }
        else {
            Response::missingArguments("username")->send();
        }
    }
    else {
        Response::missingArguments("password")->send();
    }
}
