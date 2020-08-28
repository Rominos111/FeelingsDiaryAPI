<?php

$_ENV["ALLOWED_METHODS"] = "GET";
require_once "__php__";

require_once "objects/Entry.php";
require_once "shared/JWT.php";

if (isset($_GET)) {
    if (!empty($_GET["token"]) && is_string($_GET["token"])) {
        $invalid = JWT::isInvalidToken($_GET["token"]);
        if (!$invalid) {
            if (!empty($_GET["id"]) && is_numeric($_GET["id"])) {
                $entry = Entry::getById($_GET["id"]);
                if (!is_null($entry)) {
                    if ($entry->getUserID() === JWT::getUserID($_GET["token"])) {
                        Response::builder()
                            ->setPayload($entry->toArray())
                            ->send();
                    }
                    else {
                        Response::builder()
                            ->setHttpCode(ResponseCode::UNAUTHORIZED)
                            ->setMessage("Entry not found")
                            ->setCustomCode(11)
                            ->send();
                    }
                }
                else {
                    Response::builder()
                        ->setHttpCode(ResponseCode::NOT_FOUND)
                        ->setMessage("Entry not found")
                        ->send();
                }
            }
            else {
                Response::missingArguments("id")->send();
            }
        }
        else {
            Response::builder()
                ->setHttpCode(ResponseCode::UNAUTHORIZED)
                ->setMessage("Invalid token")
                ->setCustomCode($invalid)
                ->send();
        }
    }
    else {
        Response::missingArguments("token")->send();
    }
}
