<?php

require_once "__php__";

require_once "shared/response/Response.php";

Response::builder()
    ->setHttpCode(ResponseCode::FORBIDDEN)
    ->setMessage("Resource forbidden")
    ->setCustomCode(-1)
    ->send();
