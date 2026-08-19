<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Sentry\Laravel\Integration;
use Sentry\State\Scope;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
//        $this->reportable(function (Throwable $e) {
//            Integration::captureUnhandledException($e);
//        });
    }

    /**
     * Report or log an exception.
     *
     *
     * @throws Throwable
     */
    public function report(Throwable $e): void
    {
        // Check if the user is authenticated
//        if (auth()->check()) {
//            // If the user is authenticated fetch the user
//            $user = auth()->user();
//            // Config scope to set the user for sentry
//            Integration::configureScope(function (Scope $scope) use ($user): void {
//                $scope->setUser([
//                    'id' => $user->id,
//                    'email' => $user->email,
//                ]);
//            });
//        }
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     *
     * @return JsonResponse|Response|\Symfony\Component\HttpFoundation\Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e): JsonResponse|Response|\Symfony\Component\HttpFoundation\Response
    {
        if ($e instanceof AuthenticationException) {
            return $this->jsonError(__('Unauthorized Access'), Response::HTTP_UNAUTHORIZED);
        }

        if (($request->is('api/*'))|| ($request->is('admin/*'))) {

            if ($e instanceof AuthorizationException) {
                return $e->render($request);
            } elseif ($e instanceof ModelNotFoundException) {
                $errorMessage = __('Requested resource is not available, Please contact support');
                $errorCode = Response::HTTP_NOT_FOUND;
            } else {
                $errorMessage = __($e->getMessage());
                $errorCode = $e instanceof HttpException
                    ? $e->getStatusCode()
                    : Response::HTTP_BAD_REQUEST;
            }

            return $this->jsonError($errorMessage, $errorCode);
        }

        return parent::render($request, $e);
    }
}
