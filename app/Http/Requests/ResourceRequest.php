<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Resource;

class ResourceRequest extends Request
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
			'title'       => 'required',
			'type'        => 'required|in:' . implode(',', array_keys(Resource::TYPES)),
			'file'        => ($this->route()->getName() == 'resources.store' ? ('required_if:type,' . Resource::TYPE_FILE . '|') : '') . 'mimes:pdf|max:5000',
			'drive_id'    => 'required_if:type,' . Resource::TYPE_GDOC,
			'category_id' => 'exists:resource_categories,id',
			'tags'        => 'array',
			'tags.*'      => 'exists:resource_tags,id',
			'event_id'    => 'exists:events,id',
			'access_id'   => 'exists:permissions,id',
		];
	}

	/**
	 * Get the validation messages that apply to the request.
	 * @return array
	 */
	public function messages()
	{
		return [
			'title.required'       => 'Enter the resource\'s name',
			'type.required'        => 'Select the resource type',
			'type.in'              => 'Select a valid resource type',
			'file.required_if'     => 'Select a file to upload',
			'file.mimes'           => 'Only PDFs are currently supported',
			'file.max'             => 'Maximum file size is 5MB',
			'drive_id.required_if' => 'Enter the document ID',
			'category_id.exists'   => 'Select a valid category',
			'tags.array'           => 'Select some tags',
			'tags.*.exists'        => 'Select a valid tag',
			'event_id.exists'      => 'Select a valid event',
			'access_id.exists'     => 'Select a valid access type',
		];
	}
}
