<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-07-01
 */

namespace fk\messenger;

class Messenger
{
    public $with;

    public function with(string $with)
    {
        $this->with = $with;
        return $this;
    }

    public function send()
    {
    }
}