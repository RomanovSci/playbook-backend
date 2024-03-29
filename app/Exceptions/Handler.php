<?php
declare(strict_types = 1);

namespace App\Exceptions;

use App\Helpers\DebugbarHelper;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class Handler
 * @package App\Exceptions
 */
class Handler extends ExceptionHandler
{
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
        //
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception): void
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request  $request
     * @param \Exception  $e
     * @return mixed
     */
    public function render($request, Exception $e)
    {
        $response = ['message' => $e->getMessage()];

        if (config('app.debug')) {
            $response['debug'] = DebugbarHelper::getBaseProfilingData();
            $response['trace'] = $e->getTrace();
        }

        $status = Response::HTTP_BAD_REQUEST;

        if ($this->isHttpException($e)) {
            $status = $e->getStatusCode();
        }

        return response()->json($response, $status);
    }
}
