<?php

namespace App\Exceptions;

use App\Traits\ApiResponserTraits;
use Throwable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    use ApiResponserTraits;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param \Exception $exception
     * @return void
     * @throws Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception $exception
     * @return \Illuminate\Http\Response
     * @throws Throwable
     */
    /*public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
    }*/

    public function render($request, \Throwable $exception)
    {
        //dd($exception);
        if (!request()->is('*api*') || !$request->ajax()) {
            if ($exception instanceof ModelNotFoundException) {
                $modelName = class_basename($exception->getModel());
                return $this->respondNotFound("The {$modelName} could not found.");
            }
            return parent::render($request, $exception);
        }
        if ($exception instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($exception, $request);
        } elseif ($exception instanceof ModelNotFoundException) {
            // dd($exception);
            $modelName = strtolower(class_basename($exception->getModel()));
            return $this->respondNotFound("The {$modelName} could not be find by the provided id.");
        } elseif ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        } elseif ($exception instanceof AuthorizationException) {
            return $this->respondForbidden($exception->getMessage());
        } elseif ($exception instanceof NotFoundHttpException) {
            return $this->respondNotFound('The requested resource could not be found.');
        } elseif ($exception instanceof MethodNotAllowedHttpException) {
            return $this->respondMethodNotAllowed('The resource does not support the current http method.');
        } elseif ($exception instanceof HttpException) {
            return $this->respondCustomError($exception->getMessage(), $exception->getStatusCode(), $exception);
        } elseif ($exception instanceof QueryException) {
            $sqlErrorCode = $exception->errorInfo[1];
            if ($sqlErrorCode == 1451) {
                return $this->respondResourceConflict('The requested resource cannot be deleted until its child references exists.');
            }
        }
        if (config('app.debug')) {
            return parent::render($request, $exception);
        }
        if ($exception instanceof TokenMismatchException) {
            redirect()->back()->withInput($request->input());
        }
        return $this->respondCustomError('Unknown Exception,Please Try again later.', 500, $exception);
    }


    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $e->errors();

        if ($this->isFrontend($request)) {
            if ($request->ajax()) {
                return $this->respondBadRequest($this->getValidationErrorMsg($errors));
            } else {
                return redirect()->back()
                    ->withInput($request->input())
                    ->withErrors($errors);
            }
        }

        return $this->respondBadRequest($this->getValidationErrorMsg($errors));
    }

    private function getValidationErrorMsg($errors)
    {
        $errorsMsg = '';
        foreach ($errors as $key => $value) {
            if (empty($errorsMsg)) {
                $errorsMsg = $errors[$key][0];
            }
        }
        return $errorsMsg;
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($this->isFrontend($request)) {
            return redirect()->guest('login');
        }
        $message = $exception->getMessage();
        return $this->respondUnauthorized($message);
    }

    private function isFrontend($request): bool
    {
        return $request->acceptsHtml() && collect($request->route()->middleware())->contains('web');
    }
}
