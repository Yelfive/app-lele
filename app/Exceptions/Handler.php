<?php

namespace App\Exceptions;

use App\Components\ApiResult;
use App\Components\HttpStatusCode;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Auth\AuthenticationException $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            $result = ApiResult::instance()
                ->code(HttpStatusCode::CLIENT_UNAUTHORIZED)
                ->message(__('error.Unauthorized'));

            return response()->json($result, HttpStatusCode::getStatusCode(HttpStatusCode::CLIENT_UNAUTHORIZED));
        }

        return redirect()->guest(route('login.page'));
    }

    /**
     * @inheritdoc
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        if ($e->response) {
            return $e->response;
        }

        $errors = $e->validator->errors()->getMessages();

        if ($request->expectsJson()) {
            return response()->json(
                ApiResult::instance()
                    ->code(HttpStatusCode::CLIENT_VALIDATION_ERROR)
                    ->extend(['errors' => $errors])
                    ->message(__('error.Validation failed'))
                , HttpStatusCode::getStatusCode(HttpStatusCode::CLIENT_VALIDATION_ERROR)
            );
        }

        return redirect()->back()->withInput(
            $request->input()
        )->withErrors($errors);
    }

    protected function prepareResponse($request, Exception $e)
    {
        if ($request->expectsJson()) {
            $result = new ApiResult();
            /** @var \Illuminate\Routing\ResponseFactory $response */
            $response = response();
            if ($e instanceof PostTooLargeException) {
                $result->code(HttpStatusCode::CLIENT_REQUEST_ENTITY_TOO_LARGE)
                    ->message($e->getMessage() ?: '图片或文件不能超过' . ini_get('post_max_size')); // TODO: i18n
            } else {
                $result->code(HttpStatusCode::SERVER_INTERNAL_ERROR)
                    ->message($e->getMessage() ?: '服务器内部错误'); // TODO: i18n
            }
            return $response->json($result, HttpStatusCode::getStatusCode($result->code));
        } else {
            return parent::prepareResponse($request, $e);
        }
    }
}
