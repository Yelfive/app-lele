<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-04-20
 */

namespace App\Exceptions;

class BadResultException extends \Exception
{

    public function __construct($message, $code = 521, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}