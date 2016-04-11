<?php

namespace App\Http\Controllers;

use App\Event;
use App\Http\Requests\GenericRequest;
use App\Http\Requests\ResourceRequest;
use App\Permission;
use App\Resource;
use App\ResourceCategory;
use App\ResourceTag;
use App\Http\Requests;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Szykra\Notifications\Flash;

class ResourceController extends Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->middleware('auth.permission:admin', [
			'only' => [
				'create',
				'store',
				'edit',
				'update',
				'destroy',
				'listTags',
				'storeTag',
				'updateTag',
				'destroyTag',
			],
		]);
	}

	/**
	 * Handle whether to show the index page or perform a search.
	 * @param \App\Http\Requests\GenericRequest $request
	 * @return mixed
	 */
	public function searchHandle(GenericRequest $request)
	{
		// Parse the search
		$search   = $this->parseSearchRequest($request);
		$query    = isset($search['query']) ? $search['query'] : null;
		$category = isset($search['category']) ? $search['category'] : null;
		$tags     = isset($search['tag']) ? $search['tag'] : [];

		// Nothing entered - just render the index page
		if($query == null && $category == null && empty($tags)) {
			return $this->searchIndex();
		} // Process the search
		else {
			return $this->searchPerform($query, $category, $tags);
		}
	}

	/**
	 * Process the submission of the search form.
	 * @param \App\Http\Requests\GenericRequest $request
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function searchProcess(GenericRequest $request)
	{
		// Check that a query has been performed
		if(!$request->get('query') && !$request->get('category') && !$request->get('tag')) {
			return redirect(route('resources.search'));
		} else {
			// Redirect
			return redirect(route('resources.search', $this->parseSearchRequest($request)));
		}
	}

	/**
	 * Display the default search form.
	 * @return mixed
	 */
	public function searchIndex()
	{
		return View::make('resources.index')
		           ->with('query', null);
	}

	/**
	 * Actually perform the search and present the results.
	 * @param       $query
	 * @param       $category
	 * @param array $tags
	 * @return mixed
	 */
	public function searchPerform($query, $category, array $tags)
	{
		// Decode the search term
		$query = urldecode($query);

		// Search using the query and category
		$resources = Resource::select('resources.*');
		$resources = $query ? $resources->search($query) : $resources->orderBy('title');
		$resources = $category ? $resources->inCategory($category) : $resources;
		$resources = $tags ? $resources->withTags($tags) : $resources;

		// Access and paginate
		$resources = $resources->accessible()
		                       ->paginate(20);
		$this->checkPagination($resources);

		// Render the view
		return View::make('resources.search')
		           ->with([
			           'resources' => $resources,
			           'search'    => (object) [
				           'query'    => $query,
				           'category' => $category,
				           'tags'     => $tags,
			           ],
			           'category'  => ResourceCategory::whereSlug($category)->first(),
		           ]);
	}

	/**
	 * View the resources as a tabulated list.
	 * @param \App\Http\Requests\GenericRequest $request
	 * @return mixed
	 */
	public function index(GenericRequest $request)
	{
		if(!$this->user->isAdmin()) {
			return redirect()->route('resources.search');
		}

		// Get the resources
		$resources = Resource::select('resources.*')
		                     ->orderBy('title', 'ASC');

		// Allow filtering by category or access
		$filter = $this->filter($request);
		if($filter) {
			if(preg_match('/^category:(.*)$/', $filter, $matches)) {
				$resources = $resources->inCategory($matches[1]);
			} else if(preg_match('/^access:(.*)$/', $filter, $matches)) {
				$resources = $matches[1] == 'null' ? $resources->whereNull('resources.access_id') : $resources->where('resources.access_id', $matches[1]);
			}
			$resources = $resources->get();
		} else {
			$resources = $resources->paginate(20);
			$this->checkPagination($resources);
		}

		// Render
		return View::make('resources.list')
		           ->with([
			           'resources' => $resources,
			           'filter'    => $filter,
		           ]);
	}

	/**
	 * View the form to create a resource.
	 * @param \App\Http\Requests\GenericRequest $request
	 * @return string
	 */
	public function create(GenericRequest $request)
	{
		return View::make('resources.create')
		           ->with([
			           'resource' => new Resource([
				           'type' => $request->old('type') ?: Resource::TYPE_FILE,
			           ]),
			           'mode'     => 'create',
			           'url'      => route('resources.store'),
		           ]);
	}

	/**
	 * Process the creation form and store the new request.
	 * @param \App\Http\Requests\ResourceRequest $request
	 */
	public function store(ResourceRequest $request)
	{
		// Create the resource
		$resource = Resource::create([
			'title'       => $request->stripped('title'),
			'description' => $request->has('description') ? $request->stripped('description') : null,
			'category_id' => $request->has('category_id') ? $request->get('category_id') : null,
			'event_id'    => $request->has('event_id') ? $request->get('event_id') : null,
			'author_id'   => $this->user->id,
			'type'        => $request->get('type'),
			'href'        => null,
			'access_id'   => $request->has('access_id') ? $request->get('access_id') : null,
		]);

		// Set the tags
		$resource->tags()->sync($request->has('tags') ? $request->get('tags') : []);

		// Upload the file
		if($resource->isFile()) {
			$request->file('file')->move(Resource::getParentDirectory(), $resource->getFileName());
		} // Set the GDoc ID
		else if($resource->isGDoc()) {
			$resource->update([
				'href' => $request->stripped('drive_id'),
			]);
		}

		Flash::success('Resource created');

		return redirect(route('resources.view', ['id' => $resource->id]));
	}

	/**
	 * View a resource.
	 * @param $id
	 * @return string
	 */
	public function view($id)
	{
		$resource = $this->getResourceWithAccess($id);

		return View::make('resources.view')
		           ->with([
			           'resource' => $resource,
		           ]);
	}

	/**
	 * View the contents of a resource.
	 * @param $id
	 * @return mixed
	 */
	public function stream($id)
	{
		$resource = $this->getResourceWithAccess($id);

		if($resource->isFile()) {
			$path = $resource->getFilePath();

			return Response::make(file_get_contents($path), 200, $resource->getHeaders());
		}
	}

	/**
	 * Download a resource.
	 * @param $id
	 */
	public function download($id)
	{
		$resource = $this->getResourceWithAccess($id);

		if($resource->isFile()) {
			$path = $resource->getFilePath();

			return Response::download($path, $resource->title . '.' . $resource->getFileExtension(), $resource->getHeaders());
		} else if($resource->isGDoc()) {
			return $resource->getFilePath();
		}
	}

	/**
	 * View the form to edi a resource.
	 * @param $id
	 * @return mixed
	 */
	public function edit($id)
	{
		$resource = Resource::findOrFail($id);

		return View::make('resources.edit')
		           ->with([
			           'resource' => $resource,
			           'mode'     => 'edit',
			           'url'      => route('resources.update', ['id' => $id]),
		           ]);
	}

	/**
	 * Process the form and update the resource.
	 * @param                                    $id
	 * @param \App\Http\Requests\ResourceRequest $request
	 * @return mixed
	 */
	public function update($id, ResourceRequest $request)
	{
		// Update the resource
		$resource = Resource::findOrFail($id);
		$resource->update([
			'title'       => $request->stripped('title'),
			'description' => $request->has('description') ? $request->stripped('description') : null,
			'category_id' => $request->has('category_id') ? $request->get('category_id') : null,
			'event_id'    => $request->has('event_id') ? $request->get('event_id') : null,
			'access_id'   => $request->has('access_id') ? $request->get('access_id') : null,
			'href'        => $resource->isGDoc() ? $request->get('drive_id') : null,
		]);

		// Set the tags
		$resource->tags()->sync($request->has('tags') ? $request->get('tags') : []);

		// Upload the new file if provided
		if($resource->isFile() && $request->hasFile('file')) {
			File::delete($resource->getFilePath());
			$request->file('file')->move(Resource::getParentDirectory(), $resource->getFileName());
		}

		Flash::success('Updated');

		return redirect(route('resources.view', ['id' => $resource->id]));
	}

	/**
	 * Delete a resource.
	 * @param                                   $id
	 * @param \App\Http\Requests\GenericRequest $request
	 * @return mixed
	 */
	public function destroy($id, GenericRequest $request)
	{
		$request->requireAjax();

		$resource = Resource::find($id);
		if(!$resource) {
			return $this->ajaxError('Couldn\'t find that resource', 404);
		}
		$resource->delete();
		if($resource->isFile()) {
			File::delete($resource->getFilePath());
		}
		Flash::success('Resource deleted');

		return Response::json(true);
	}

	/**
	 * Provide a method that parses a search
	 * query for a category and any tags.
	 * @param $request
	 * @return array
	 */
	private function parseSearchRequest(GenericRequest $request)
	{
		// Get the query, category and tags from the request
		$query    = $request->get('query') ?: null;
		$category = $request->get('category') ?: null;
		$tags     = $request->get('tag') ?: [];

		// Initialise the parsed array
		$params = ['query' => $query];
		if($category) {
			$params['category'] = $category;
		}
		if($tags) {
			foreach($tags as $tag) {
				@$params['tag'][] = $tag;
			}
		}

		// Look for a category in the query
		preg_match('/category:([a-z0-9-]+)/i', $query, $matches);
		if(count($matches) > 0) {
			$params['category'] = $matches[1];
			$query              = trim(str_replace($matches[0], '', $query));
		}

		// Look for any tags in the query
		preg_match_all('/tag:([a-z0-9-]+)/i', $query, $matches);
		if(count($matches[0]) > 0) {
			foreach($matches[1] as $i => $tag) {
				@$params['tag'][] = $tag;
				$query = trim(str_replace($matches[0][$i], '', $query));
			}
		}

		// Set the query
		$params['query'] = $query;

		return $params;
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	private function getResourceWithAccess($id)
	{
		// Get the resource
		$resource = Resource::findOrFail($id);

		// Check the access priviledges
		if(!$resource->canAccess($this->user)) {
			App::abort(403);

			return $resource;
		}

		// Check the source is accessible
		if($resource->isFile() && !file_exists($resource->getFilePath())) {
			App::abort(404);
		}

		return $resource;
	}
}