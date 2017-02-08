<?php
    
    namespace App\Http\Controllers;
    
    use Illuminate\Contracts\Auth\Factory;
    use Illuminate\Foundation\Bus\DispatchesJobs;
    use Illuminate\Http\Request;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Routing\Controller as BaseController;
    use Illuminate\Foundation\Validation\ValidatesRequests;
    use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
    use Illuminate\Support\Facades\Input;
    use Illuminate\Support\Facades\Route;
    
    class Controller extends BaseController
    {
        use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
        
        /**
         * Redirect to page 1 if the paginator is empty.
         * @param \Illuminate\Pagination\LengthAwarePaginator $paginator
         * @return \Illuminate\Http\RedirectResponse
         */
        protected function checkPagination(LengthAwarePaginator $paginator)
        {
            if($paginator->count() == 0 && !is_null(Input::get('page')) && (int) Input::get('page') != 1) {
                return redirect()->route(Route::current()->getName(), Input::except('page') + ['page' => 1]);
            }
        }
        
        /**
         * Create a response for an AJAX request.
         * @param       $text
         * @param int   $status
         * @param array $data
         * @param array $headers
         * @return \Illuminate\Http\JsonResponse
         */
        protected function ajaxResponse($text, $status = 200, array $data = [], array $headers = [])
        {
            $data = array_merge($data, [($status == 200 ? 'response' : 'error') => $text]);
            
            return response()->json($data, $status, $headers);
        }
        
        /**
         * Create an AJAX error response.
         * @param     $errorText
         * @param int $status
         * @return \Illuminate\Http\JsonResponse
         */
        protected function ajaxError($errorCode, $status = 422, $errorText = null)
        {
            $data = [
                'error_code' => $errorCode,
                '__error'    => true,
            ];
            $text = $errorText ?: trans('errors.' . $errorCode);
            
            return $this->ajaxResponse($text, $status, $data);
        }
    }
