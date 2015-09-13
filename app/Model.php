<?php

namespace App;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

abstract class Model extends EloquentModel
{
	protected static $ValidationRules = [
		
	];

	protected static $ValidationMessages = [

	];


	/**
	 * A custom paginate method for when using the distinct() method. This fixes the incorrect 'total' reported by the default paginate.
	 * @param        $query
	 * @param int    $perPage
	 * @param array  $columns
	 * @param string $pageName
	 * @return \Illuminate\Pagination\LengthAwarePaginator
	 */
	public function scopeDistinctPaginate($query, $perPage = 15, $columns = ['*'], $pageName = 'page')
	{
		// Get the results
		$results = $query->forPage(Input::get($pageName, 1), $perPage)
		                 ->get($columns);

		// Now do a count on an unlimited version of the previous query
		$query->limit(static::count())->offset(0);
		$count = DB::select("SELECT COUNT(*) FROM (" . $query->toSql() . ") src;", $query->getBindings())[0]->{"COUNT(*)"};

		// Create the paginator
		return new LengthAwarePaginator($results, $count, $perPage, Paginator::resolveCurrentPage(), ['path' => Paginator::resolveCurrentPath()]);
	}

	/**
	 * Get the rules to validate a model.
	 * @return array
	 */
	public static function getValidationRules()
	{
		$fields = func_get_args();
		$rules = [];

		foreach($fields as $field) {
			if(isset(static::$ValidationRules[$field])) {
				$rules[$field] = static::$ValidationRules[$field];
			}
		}

		return $rules;
	}

	/**
	 * Get the validation messages.
	 * @return array
	 */
	public static function getValidationMessages()
	{
		$fields = func_get_args();
		$messages = [];

		foreach(static::$ValidationMessages as $rule => $msg) {
			if(in_array(substr($rule, 0, stripos($rule, '.')), $fields)) {
				$messages[$rule] = $msg;
			}
		}

		return $messages;
	}
}
