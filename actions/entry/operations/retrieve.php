<?php

Database::connect();

if (empty($_GET)) {
    // TODO: Lister ne peut se faire qu'avec un token
    echo "list";
}
else {
    if (!empty($_GET["id"])) {
        if (is_numeric($_GET["id"])) {
            $entry = Entry::getByID($_GET["id"]);
            $response = new Response($entry->toArray(), ResponseCode::OK);
            $response->send();
        }
    }
}
