<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-04-25
 */

namespace App\Models;

use App\Events\ModelSaving;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

/**
 * - Changes timestamp behavior when saving
 */
class Model extends \fk\utility\Database\Eloquent\Model
{

    const CREATED_BY = 'created_by';

    protected $events = [
        'saving' => ModelSaving::class
    ];

    /**
     * @var null|MessageBag
     */
    public $errors = null;

    public function __construct(array $attributes = [])
    {
        $this->fillable(array_keys($this->_getRules()));

        parent::__construct($attributes);
    }

    private $_rules;

    private final function _getRules()
    {
        if (is_array($this->_rules)) {
            return $this->_rules;
        }
        $this->_rules = $this->rules();

        if (!is_array($this->_rules)) $this->_rules = [];

        return $this->_rules;
    }

    public function rules()
    {
        return [];
    }

    public function validate(array $attributes = null)
    {
        $validator = Validator::make(is_array($attributes) ? $attributes : $this->attributes, $this->_rules);
        if ($validator->passes()) {
            return true;
        } else {
            $this->errors = $validator->errors();
            return false;
        }
    }

    public function getAttributes(array $accept = null, array $except = null)
    {
        $attributes = $this->attributes;
        if ($accept && is_array($accept)) {
            $attributes = array_intersect_key($attributes, array_flip($accept));
        }

        if ($except && is_array($except)) {
            $attributes = array_diff_key($attributes, array_flip($except));
        }

        return $attributes;
    }

    /**
     * Make it public
     * @inheritdoc
     */
    public function increment($column, $amount = 1, array $extra = [])
    {
        return parent::increment($column, $amount, $extra);
    }

    /**
     * Make it public
     * @inheritdoc
     */
    public function decrement($column, $amount = 1, array $extra = [])
    {
        return parent::decrement($column, $amount, $extra);
    }

    public function hasErrors()
    {
        return isset($this->errors);
    }

}