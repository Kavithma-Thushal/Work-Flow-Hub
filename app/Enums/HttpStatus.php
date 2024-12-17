<?php

namespace App\Enums;

enum HttpStatus
{
    const OK = 200;
    const CREATED = 201;
    const NO_CONTENT = 204;
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const UNPROCESSABLE_CONTENT = 422;
    const INTERNAL_SERVER_ERROR = 500;
}
