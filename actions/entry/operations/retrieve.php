<?php

Database::connect();

if (empty($_GET)) {
    echo "list";
}
else {
    echo "get";
}
