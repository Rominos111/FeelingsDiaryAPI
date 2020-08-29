<?php

$_ENV["ALLOWED_METHODS"] = "GET";
require_once "__php__";

require_once "objects/Entry.php";

JWTResponse::checkRequestToken($_GET["token"]);

if (empty($_GET["id"])) {
    Response::missingArguments("id")->send();
}

if (!is_numeric($_GET["id"])) {
    Response::wrongDataType("id", $_GET["id"], 0)->send();
}

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
