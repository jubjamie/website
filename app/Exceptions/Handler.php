<?php
    
    namespace App\Exceptions;
    
    use Exception;
    use Illuminate\Auth\Access\AuthorizationException;
    use Illuminate\Auth\AuthenticationException;
    use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
    use Illuminate\Session\TokenMismatchException;
    use Illuminate\Support\Facades\Response;
    use Szykra\Notifications\Flash;
    
    class Handler extends ExceptionHandler
    {
        /**
         * A list of the exception types that should not be reported.
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
         * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
         * @param  \Exception $exception
         * @return void
         */
        public function report(Exception $exception)
        {
            parent::report($exception);
        }
        
        /**
         * Render an exception into an HTTP response.
         * @param  \Illuminate\Http\Request $request
         * @param  \Exception               $exception
         * @return \Illuminate\Http\Response
         */
        public function render($request, Exception $exception)
        {
            // Default behaviour
            $default = parent::render($request, $exception);
            
            if($exception instanceof TokenMismatchException) {
                return $request->expectsJson() ? response()->json([
                    'error_code' => 'token.mismatch',
                    'error'      => trans('errors.token.mismatch'),
                    '__error'    => true,
                ], 500) : $default;
            } else if($exception instanceof AuthorizationException) {
                if(!$request->user() && !$request->expectsJson()) {
                    return redirect()->guest('login');
                } else if($request->expectsJson()) {
                    return response()->json([
                        'error_code' => 'unauthorised',
                        'error'      => trans('errors.unauthorised'),
                        '__error'    => true,
                    ], $default->getStatusCode());
                }
            }
            
            return $default;
        }
        
        /**
         * Convert an authentication exception into an unauthenticated response.
         * @param  \Illuminate\Http\Request                 $request
         * @param  \Illuminate\Auth\AuthenticationException $exception
         * @return \Illuminate\Http\Response
         */
        protected function unauthenticated($request, AuthenticationException $exception)
        {
            if($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }
            
            return redirect()->guest('login');
        }
    }
