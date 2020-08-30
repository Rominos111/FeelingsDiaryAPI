<?php

$_ENV["EXPECTED"] = array(
    "methods" => "POST",
    "args" => array(
        "content" => "",
        "token" => ""
    )
);

require_once "__php__";

require_once "objects/Entry.php";

JWTResponse::checkRequestToken($_POST["token"]);

$id = null;

try {
    $id = Entry::create($_POST["content"], JWT::getUserID($_POST["token"]));
}
catch (UserNotFoundException $e) {
    Response::builder()
        ->setMessage("You don't exist")
        ->setHttpCode(ResponseCode::UNPROCESSABLE_ENTITY)
        ->send();
}

Response::builder()
    ->setHttpCode(ResponseCode::CREATED)
    ->setMessage("Entry created")
    ->setPayload(array(
        "id" => $id
    ))->send();
