<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function ($exceptions) {

        $exceptions->render(function (Throwable $e, Request $request) {

            if ($request->expectsJson()) {
                $statusCode = match (true) {
                    $e instanceof ValidationException => 422,
                    $e instanceof AuthenticationException => 401,
                    $e instanceof NotFoundHttpException => 404,
                    method_exists($e, 'getStatusCode') => $e->getStatusCode(),
                    default => 500,
                };

                $response = [
                    'success' => false,
                    'message' => 'Ocorreu um erro interno no servidor.',
                    'data' => null,
                ];

                if ($e instanceof ValidationException) {
                    $response['message'] = 'Os dados fornecidos são inválidos.';
                    $response['errors'] = method_exists($e, 'errors') ? $e->errors() : [];
                } else if ($statusCode !== 500 || config('app.debug')) {
                    $response['message'] = $e->getMessage();
                }

                return response()->json($response, $statusCode);
            }

        });
    })
    ->create();
