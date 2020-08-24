<?php

require "__php__";

switch ($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        require_once "operations/retrieve.php";
        break;

    case "POST":
        require_once "operations/create.php";
        break;

    case "PATCH":
        require_once "operations/update.php";
        break;

    case "DELETE":
        require_once "operations/delete.php";
        break;

    default:
        break;
}
