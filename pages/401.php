<?php

require_once "__php__";

require_once "shared/response/Response.php";

Response::builder()
    ->setHttpCode(ResponseCode::UNAUTHORIZED)
    ->setMessage("Unauthorized access")
    ->setCustomCode(-1)
    ->send();
