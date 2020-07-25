<?php

session_start();

# Affichage des message de debug
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

switch ($_SERVER["REQUEST_METHOD"]) {
    case "GET":
        include_once "operations/retrieve.php";
        break;

    case "POST":
        include_once "operations/create.php";
        break;

    case "PUT":
    case "PATCH":
        include_once "operations/update.php";
        break;

    case "DELETE":
        include_once "operations/delete.php";
        break;

    default:
        break;
}

echo "employés";