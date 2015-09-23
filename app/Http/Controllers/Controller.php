<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response as ResponseFacade;
use Illuminate\Support\Facades\Route;

abstract class Controller extends BaseController
{
	use DispatchesJobs, ValidatesRequests;

	/**
	 * Store the current user object for all controllers.
	 * @var User
	 */
	protected $user;

	/**
	 * Store the user.
	 */
	public function __construct()
	{
		$this->user = Auth::user() ?: new User();
	}

	/**
	 * Redirect to page 1 if the paginator is empty.
	 * @param \Illuminate\Pagination\LengthAwarePaginator $paginator
	 * @return \Illuminate\Support\Facades\Redirect
	 */
	protected function checkPagination(LengthAwarePaginator $paginator)
	{
		if($paginator->count() == 0 && !is_null(Input::get('page')) && (int) Input::get('page') != 1) {
			App::abort(Response::HTTP_TEMPORARY_REDIRECT, '', ['Location' => route(Route::current()->getName(), ['page' => 1])]);
		}
	}

	/**
	 * Require that the request is send by AJAX.
	 * @param \Illuminate\Http\Request $request
	 */
	protected function requireAjax(Request $request)
	{
		if(!$request->ajax()) {
			App::abort(Response::HTTP_NOT_FOUND);
		}
	}

	/**
	 * Prepare a response for sending an error message over ajax.
	 * @param     $errorText
	 * @param int $code
	 * @return mixed
	 */
	protected function ajaxError($errorText, $code = 422)
	{
		return ResponseFacade::json(['error' => (string) $errorText], (int) $code);
	}

	/**
	 * Get the filter value from the request.
	 * @param \Illuminate\Http\Request $request
	 * @return null
	 */
	protected function filter(Request $request)
	{
		return $request->route()->parameter('modifier') === 'filter'
			?
			trim($request->route()->parameter('term'))
			:
			null;
	}

	/**
	 * Get the search value from the request.
	 * @param \Illuminate\Http\Request $request
	 * @return null
	 */
	protected function search(Request $request)
	{
		return $request->route()->parameter('modifier') === 'search'
			?
			trim($request->route()->parameter('term'))
			:
			null;
	}
}
