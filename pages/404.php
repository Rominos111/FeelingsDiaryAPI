<?php

require_once "__php__";

require_once "shared/response/Response.php";

Response::builder()
    ->setHttpCode(ResponseCode::NOT_FOUND)
    ->setMessage("Resource not found")
    ->setCustomCode(-1)
    ->send();
