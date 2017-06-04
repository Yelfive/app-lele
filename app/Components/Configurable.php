<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-04-20
 */

namespace App\Components;

use App\Exceptions\InvalidPropertyException;

abstract class Configurable
{
    /**
     * @var string Config scope
     */
    public $scope;

    public function __construct()
    {
        if (!$this->scope || !is_string($this->scope)) {
            throw new \BadMethodCallException('Property `scope` must be set, and must be string');
        }
        $config = config($this->scope, []);
        foreach ($config as $property => $value) {
            $this->$property = $value;
        }
    }

    public function __set($name, $value)
    {
        throw new InvalidPropertyException("Cannot set value of undefined property `$name`");
    }
}