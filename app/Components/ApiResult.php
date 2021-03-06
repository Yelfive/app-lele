<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-04-20
 */

namespace App\Components;

use App\Exceptions\BadResultException;
use App\Exceptions\MethodNotFoundException;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

/**
 * Class ApiResult
 * @package App\Http\Components
 * @method $this code(int $code = 200)
 * @method $this message(string $message)
 * @method $this data(array $data)
 * @method $this list(array $data)
 * @method $this extend(array $data)
 * @method $this overrideExtend(array $data) Override the extend field
 *
 * @property int $code
 * @property string $message
 * @property array $data
 * @property array $list
 * @property array $extend
 */
class ApiResult implements Jsonable
{

    /**
     * @var array
     */
    public $errors;

    /**
     * @var array
     * - `rules`    based on Laravel [[Illuminate\Validation\Validator]]
     *
     * About rules:
     * @see \Illuminate\Validation\Validator
     * @link https://laravel.com/docs/5.4/validation#available-validation-rules
     *
     */
    protected $rules = [
        'code' => 'required|integer|min:100',
        'message' => 'required|string',
        'data' => 'array',
        'list' => 'array',
        'extend' => 'array',
    ];

    // todo: not working
    protected $messages = [
        'message' => [
            'required' => '`message` is required for output.'
        ]
    ];

    protected $defaultValues = [
        'code' => 200,
    ];

    protected $result = [];

    public function __construct()
    {
        $this->loadDefaultValues();
    }

    protected function loadDefaultValues()
    {
        foreach ($this->defaultValues as $k => $defaultValue) {
            $this->result[$k] = $defaultValue;
        }
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     * @return string
     * @throws BadResultException
     */
    public function toJson($options = 0)
    {
        if ($this->validate()) {
            $data = $this->toArray();
            return json_encode($data, JSON_UNESCAPED_UNICODE);
        } else {
            throw new BadResultException($this->errorsToString());
        }
    }

    public function errorsToString()
    {
        $errorString = '';
        foreach ($this->errors as $errors) {
            foreach ($errors as $error) {
                $errorString .= $error;
            }
        }
        return $errorString;
    }

    public function toArray()
    {
        $data = $this->result;
        if (isset($data['extend'])) {
            $extend = $data['extend'];
            unset($data['extend']);
            $data = array_merge($data, $extend);
        }
        return $data;
    }

    /**
     * Validate the whole result
     * @return bool
     */
    public function validate(): bool
    {
        $validator = Validator::make($this->result, $this->rules, $this->messages);
        if ($validator->passes()) {
            return true;
        } else {
            $this->errors = $validator->errors()->toArray();
            return false;
        }
    }

    public function __call($name, $arguments)
    {
        if (isset($this->rules[$name]) || $name === 'overrideExtend') {
            $value = $arguments[0] ?? null;
            if ($name === 'extend') {
                $value = array_merge($this->result['extend'] ?? [], $value);
            }
            if ($name === 'overrideExtend') $name = 'extend';
            $this->setAttribute($name, $value);
            return $this;
        }
        throw new MethodNotFoundException('Call to undefined method ' . __CLASS__ . '::' . $name . '()');
    }

    public function __get($name)
    {
        return $this->result[$name] ?? null;
    }

    /**
     * Set
     * @param string $name
     * @param mixed $value
     * @throws BadResultException
     */
    protected function setAttribute(string $name, $value)
    {
        $rule = $this->rules[$name];
        if ($value === null) {
            if (isset($this->defaultValues[$name])) {
                $value = $this->defaultValues[$name];
            } else {
                throw new BadResultException("Missing value for '$name', it must not be `null`");
            }
        }

        $validator = Validator::make([$name => $value], [$name => $rule]);
        if ($validator->fails()) {
            throw new BadResultException($validator->errors());
        } else {
            $this->result[$name] = $value;
        }
    }

    public function isEmpty(): bool
    {
        return !$this->result;
    }

    /**
     * Return a message after form validation failed
     * @param array|MessageBag $errors
     * @param null|string $message
     * @return ApiResult
     */
    public static function validationFailed($errors, $message = null): ApiResult
    {
        return static::instance()
            ->code(HttpStatusCode::CLIENT_VALIDATION_ERROR)
            ->message($message ?? __('base.Invalid params.'))
            ->extend(['errors' => $errors]);
    }

    /**
     * @return static
     */
    public static function instance()
    {
        return new static;
    }
}