<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-04-24
 */

namespace App\Components;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\MessageBag;

class FormRequest extends \Illuminate\Foundation\Http\FormRequest
{

    /**
     * Translate format
     * ```
     *  [
     *      attribute => [
     *          error1,
     *          error2
     *      ]
     *  ]
     * ```
     * Into
     * ```
     *  error1. error2
     * ```
     * @param array $errors
     * @return $this|JsonResponse
     */
    public function response(array $errors)
    {
        if ($this->expectsJson()) {
            $result = ApiResult::validationFailed($errors);

            return new JsonResponse($result);
        }

        return $this->redirector
            ->to($this->getRedirectUrl())
            ->withInput($this->except($this->dontFlash))
            ->withErrors($errors, $this->errorBag);
    }

    /**
     * Simplify errors into string
     * @param array|MessageBag $errors
     * @return string
     */
    public function stringifyErrors($errors): string
    {
        if ($errors instanceof MessageBag) {
            $content = $errors->all();
        } else {
            $content = [];
            foreach ($errors as $attributeErrors) {
                if (is_string($attributeErrors)) {
                    $content[] = $attributeErrors;
                } else {
                    foreach ($attributeErrors as $error) {
                        $content[] = $error;
                    }
                }
            }

        }
        return implode('', $content);
    }

    private $_validationData;

    public function validationData($attribute = null)
    {
        if (is_array($this->_validationData)) {
            return $attribute === null ? $this->_validationData : $this->_validationData[$attribute];
        }
        $this->_validationData = parent::validationData();

        if (method_exists($this, 'rules')) {
            $this->_validationData = array_intersect_key($this->_validationData, $this->rules());
        }
        return $this->_validationData;
    }

}