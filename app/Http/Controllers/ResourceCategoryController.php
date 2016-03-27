<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenericRequest;
use App\ResourceCategory;
use App\Http\Requests;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Szykra\Notifications\Flash;

class ResourceCategoryController extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->middleware('auth.permission:admin');
	}

	/**
	 * View all the existing categories (tabulated).
	 * @return mixed
	 */
	public function index()
	{
		$categories = ResourceCategory::orderBy('name', 'ASC')->paginate(20);

		return View::make('resources.categories.list')
		           ->with('categories', $categories);
	}

	/**
	 * Create a new resource category.
	 * @param \App\Http\Requests\GenericRequest $request
	 * @return mixed
	 */
	public function store(GenericRequest $request)
	{
		// Require ajax request
		$this->requireAjax($request);

		// Validate
		$request->merge(['slug' => $this->createSlug($request)]);
		$this->validate($request, ResourceCategory::getValidationRules('name', 'slug'), ResourceCategory::getValidationMessages('name', 'slug'));

		// Create the category
		ResourceCategory::create($request->stripped('name', 'slug', 'flag'));
		Flash::success('Category created');

		return Response::json(true);
	}

	/**
	 * Update a category's details.
	 * @param                                   $id
	 * @param \App\Http\Requests\GenericRequest $request
	 * @return mixed
	 */
	public function update($id, GenericRequest $request)
	{
		// Require ajax request
		$this->requireAjax($request);

		// Get the category
		$category = ResourceCategory::find($id);
		if(!$category) {
			return $this->ajaxError('Couldn\'t find that category', 404);
		}

		// Validate
		$rules = ResourceCategory::getValidationRules('name', 'slug');
		$rules['slug'] .= ",{$id},id";
		$request->merge(['slug' => $this->createSlug($request)]);
		$this->validate($request, $rules, ResourceCategory::getValidationMessages('name', 'slug'));

		// Update
		$category->update($request->stripped('name', 'slug', 'flag'));
		Flash::success('Category updated');

		return Response::json(true);
	}

	/**
	 * Delete a category.
	 * @param                                   $id
	 * @param \App\Http\Requests\GenericRequest $request
	 * @return mixed
	 */
	public function destroy($id, GenericRequest $request)
	{
		// Require ajax
		$this->requireAjax($request);

		// Get the category
		$category = ResourceCategory::find($id);
		if(!$category) {
			return $this->ajaxError("Couldn't find that category", 404);
		}

		// Delete
		$category->delete();
		Flash::success('Category deleted');

		return Response::json(true);
	}
}
