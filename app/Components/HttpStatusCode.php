<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-04
 */

namespace App\Components;

use fk\http\StatusCode;

/**
 * Add some additional status codes
 */
class HttpStatusCode extends StatusCode
{

    const ALWAYS_EXPECTS_OK = true;

    // Client error
    const CLIENT_INVALID_LOGIN = 421;
    const CLIENT_VALIDATION_ERROR = 422;

    // Server error
    const SERVER_SAVE_FAILED = 522;
    const SERVER_THIRD_PARTY_ERROR = 523;
}