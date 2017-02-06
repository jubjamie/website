<?php
    
    namespace App\Http\Controllers;
    
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
    }
