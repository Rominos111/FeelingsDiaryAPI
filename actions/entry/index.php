<?php

require_once "__php__";

switch ($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        require_once "retrieve.php";
        break;

    case "POST":
        require_once "create.php";
        break;

    case "PATCH":
        require_once "update.php";
        break;

    case "DELETE":
        require_once "delete.php";
        break;

    default:
        break;
}
