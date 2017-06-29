<?php

namespace App\Http\Controllers;

use App\Components\ApiResponse;
use App\Exceptions\BadResultException;
use App\Components\ApiResult;
use Illuminate\Contracts\Validation\Validator as AbstractValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;

class ApiController extends Controller
{
    /**
     * @var ApiResult
     */
    public $result;

    /**
     * @var null|MessageBag
     */
    protected $errors;

    /**
     * @var Request
     */
    protected $request;

    public function __construct(ApiResult $result, Request $request)
    {
        if ($request instanceof \fk\utility\Http\Request) $request->setExpectsJson(true);
        $this->request = $request;
        $this->result = $result;
    }

    public function callAction($method, $parameters)
    {
        parent::callAction($method, $parameters);
        if ($this->result->validate()) {
            return new ApiResponse($this->result);
        } else {
            throw new BadResultException($this->result->errorsToString());
        }
    }

    /**
     * Add `return` if validation passes
     * @inheritdoc
     * @return array Array of inputs tested if validation passes
     */
    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        try {
            parent::validate($request, $rules, $messages, $customAttributes);
            return array_intersect_key($request->all(), $rules);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    protected function validateData(array $data, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validator = Validator::make(...func_get_args());

        if ($validator->passes()) {
            return true;
        } else {
            $this->errors = $validator->errors();
            $this->throwValidationException(App::make('request'), $validator);
            return false;
        }
    }

    /**
     * Replace the response generator
     * @inheritdoc
     */
    protected function throwValidationException(Request $request, $validator)
    {
        throw new ValidationException($validator, $this->prepareResponse($request, $validator));
    }

    protected function prepareResponse(Request $request, AbstractValidator $validator)
    {
        $errors = ApiResult::validationFailed($validator->errors());
        if ($request->expectsJson()) {
            return new JsonResponse($errors, $errors->code);
        }

        return redirect()->to($this->getRedirectUrl())
            ->withInput($request->input())
            ->withErrors($errors, $this->errorBag());
    }
}
