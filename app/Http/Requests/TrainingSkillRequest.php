<?php

namespace App\Http\Requests;

use App\TrainingSkill;

class TrainingSkillRequest extends Request
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
		return TrainingSkill::getValidationRules('name', 'category_id', 'description', 'requirements_level1', 'requirements_level2', 'requirements_level3');
	}

	/**
	 * Get the validation messages.
	 * @return array
	 */
	public function messages()
	{
		return TrainingSkill::getValidationMessages('name', 'category_id', 'description', 'requirements_level1', 'requirements_level2', 'requirements_level3');
	}
}
