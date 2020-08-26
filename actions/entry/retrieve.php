<?php

require_once "__php__";
require_once "objects/Entry.php";

if ($_SERVER["REQUEST_METHOD"] != "GET") {
    $response = new Response(ResponseCode::METHOD_NOT_ALLOWED, array("method" => $_SERVER["REQUEST_METHOD"]));
    $response->send(1, "Method not allowed, for example using POST on a GET-only file");
    exit();
}

if (isset($_GET)) {
    if (isset($_GET["id"]) && !empty($_GET["id"])) {
        if (is_numeric($_GET["id"])) {
            Database::connect();

            $entry = Entry::getById($_GET["id"]);
            $response = new Response(ResponseCode::OK, $entry->toArray());
            $response->send();
        }
    }
    else {
        echo "list";
    }
}
