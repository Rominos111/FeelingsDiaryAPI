<?php

$_ENV["EXPECTED"] = array(
    "methods" => "GET",
    "args" => array(
        "token" => ""
    )
);

require_once "__php__";

require_once "objects/Entry.php";

JWTResponse::checkRequestToken($_GET["token"]);

if (!is_null($_GET["id"]) && is_numeric($_GET["id"])) {
    $entry = Entry::getById($_GET["id"]);

    if (is_null($entry)) {
        Response::builder()
            ->setHttpCode(ResponseCode::NOT_FOUND)
            ->setMessage("Entry not found")
            ->send();
    }

    // Mieux qu'un FORBIDDEN ?
    if ($entry->getUserID() !== JWT::getUserID($_GET["token"])) {
        Response::builder()
            ->setHttpCode(ResponseCode::NOT_FOUND)
            ->setMessage("Entry not found")
            ->send();
    }

    Response::builder()
        ->setPayload($entry->toArray())
        ->send();
}
else {
    $uid = JWT::getUserID($_GET["token"]);
    $entries = Entry::listByUserId($uid);

    Response::builder()
        ->setPayload($entries)
        ->send();
}
