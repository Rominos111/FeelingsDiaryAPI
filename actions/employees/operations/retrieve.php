<?php

Database::connect();

if (empty($_GET)) {
    $test = array("abc", "def");

    $res = array("content" => $test);

    echo json_encode($res);
}
else {
    echo "get";
}

