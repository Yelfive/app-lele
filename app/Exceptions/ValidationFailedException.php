<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-05-28
 */

namespace App\Exceptions;

use Exception;
use Illuminate\Support\MessageBag;

class ValidationFailedException extends Exception
{

    public function __construct($message = "", $code = 0, Exception $previous = null)
    {
        if ($message instanceof MessageBag) $message = $message->toJson();
        parent::__construct($message, $code, $previous);
    }

}