<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\App;

abstract class Request extends FormRequest
{
	/**
	 * Provide the ability for all requests to return values with the html
	 * tags stripped.
	 * @return array
	 */
	public function stripped()
	{
		if(func_num_args() > 0) {
			$inputs = $this->only(func_get_args());
		} else {
			$inputs = $this->all();
		}
		$inputs_stripped = [];

		foreach($inputs as $n => $v) {
			$inputs_stripped[$n] = is_array($v) ? array_map("strip_tags", $v) : strip_tags($v);
		}

		return count($inputs_stripped) == 1 ? array_shift($inputs_stripped) : $inputs_stripped;
	}

	/**
	 * Require that the request is made by AJAX,
	 */
	public function requireAjax()
	{
		if(!$this->ajax()) {
			App::abort(Response::HTTP_NOT_FOUND);
		}
	}

	/**
	 * Convert date and time sub-entries into a single entry.
	 * @param        $baseName
	 * @param string $format
	 */
	public function createDateTimeEntry($baseName, $format = 'Y-m-d H:i')
	{
		$date = Carbon::create($this->get($baseName . '_year') ?: 1,
			$this->get($baseName . '_month') ?: 1,
			$this->get($baseName . '_day') ?: 1,
			$this->get($baseName . '_hour') ?: 0,
			$this->get($baseName . '_minute') ?: 0,
			$this->get($baseName . '_second') ?: 0,
			env("SERVER_TIMEZONE", "UTC"));

		$this->merge([
			$baseName => $date->format($format),
		]);
	}

	/**
	 * Convert date sub entries into a single entry.
	 * @param        $baseName
	 * @param string $format
	 */
	public function createDateEntry($baseName, $format = 'Y-m-d')
	{
		$this->createDateTimeEntry($baseName, $format);
	}

	/**
	 * Convert time sub entries into a single entry.
	 * @param        $baseName
	 * @param string $format
	 */
	public function createTimeEntry($baseName, $format = 'H:i')
	{
		$this->createDateTimeEntry($baseName, $format);
	}
}
