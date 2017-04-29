<?php

namespace App\Exceptions;

namespace App\Exceptions;

use App\Services\Responder\ResponderServiceInterface;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Log;
use Auth;
use Request;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param Exception $e
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @throws ModelValidationException
     */
    public function render($request, Exception $e) {

        $msg = (Auth::check() ? "user_id:".Auth::user()->id : "Anonymous").
            " ".(Request::ip() ? Request::ip() : "CLI"). " ".
            str_replace("\n", ", ", $e->getMessage()).' in '.$e->getFile().' at line '.$e->getLine().
            " ".Request::method()." ".Request::fullUrl().", ".json_encode(Request::all());

        Log::critical($msg);

        /** @var ResponderServiceInterface $corsService */
        $corsService = app()->make(ResponderServiceInterface::class);

        switch(1) {
            case $e instanceof TokenBlacklistedException:
                /** @var ModelNotFoundException $e */
                $e = new MultipleExceptions(trans('app.common.errors.logged_out'), 401);
                return $corsService->errorResponse($e, 401);
                break;

            case $e instanceof ModelNotFoundException:
                /** @var ModelNotFoundException $e */
                $e->__construct(trans('app.common.errors.element_not_found'), 404, null);
                return $corsService->errorResponse($e);

            case $e instanceof NotFoundHttpException:
                $e->__construct(trans('app.common.errors.element_not_found'), null, 404);
                return $corsService->errorResponse($e);

            case $e instanceof AccessDeniedHttpException:
                $n = new MultipleExceptions(trans('app.common.errors.insufficient_privileges'), 403);
                return $corsService->errorResponse($n, 403);

            case $e instanceof MethodNotAllowedHttpException:
                /** @var MethodNotAllowedHttpException $e */
                $e->__construct([], 'Method not allowed', $e, 405);
                return $corsService->errorResponse($e);

            case $e instanceof TokenExpiredException:
            case $e instanceof TokenInvalidException:
                /** @var TokenInvalidException $e */
                return $corsService->errorResponse($e, 401);

            case $e instanceof ValidationException:
                /** @var ValidationException $e */
                throw new ModelValidationException($e->validator->getMessageBag(), 422);
                break;

            case $e instanceof ModelValidationException:
                return $corsService->errorResponse($e, 422);

            case $e instanceof MultipleExceptions:
                return $corsService->errorResponse($e, 422);

            case $e instanceof AuthenticationException:
                /** @var TokenInvalidException $e */
                return $corsService->errorResponse($e, 400);
            
        }

        if(config('app.debug') == true) {
            return parent::render($request, $e);
        } else {
            return $corsService->errorResponse($e);
        }
    }
}
