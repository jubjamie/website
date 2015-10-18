<?php

namespace App\Http\Requests;

use Carbon\Carbon;

class QuoteRequest extends Request
{
	/**
	 * Determine if the user is authorized to make this request.
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 * @return array
	 */
	public function rules()
	{
		return [
			'culprit' => 'required',
			'date'    => 'required|date_format:Y-m-d H:i|before:' . Carbon::now()->tz(env('SERVER_TIMEZONE', 'UTC'))->addMinutes(1)->format("Y-m-d H:i"),
			'quote'   => 'required',
		];
	}

	/**
	 * Define the custom messages.
	 * @return array
	 */
	public function messages()
	{
		return [
			'culprit.required' => 'Please enter the culprit',
			'date.required'    => 'Please specify when it was said',
			'date.date_format' => 'Please use the format \'YYYY-mm-dd hh:mm\'',
			'date.before'      => 'Try not to predict the future!',
			'quote.required'   => 'Please enter what was said',
		];
	}
}
