<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\ResourceTag;
use App\Traits\CreatesSlugs;
use Illuminate\Http\Request;
use Szykra\Notifications\Flash;

class TagController extends Controller
{
    use CreatesSlugs;
    
    /**
     * View the list of categories.
     * @return $this
     */
    public function index()
    {
        // Authorise
        $this->authorize('index', ResourceTag::class);
        
        // Get the list of categories
        $tags = ResourceTag::orderBy('name', 'ASC')
                           ->paginate(20);
        $this->checkPagination($tags);
        return view('resources.tags.index')->with('tags', $tags);
    }
    
    /**
     * Store a new category.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Authorise
        $this->requireAjax();
        $this->authorize('create', ResourceTag::class);
        
        // Create the slug
        $this->createSlug($request);
        
        // Validate
        $fields = ['name', 'slug'];
        $this->validate(
            $request,
            ResourceTag::getValidationRules($fields),
            ResourceTag::getValidationMessages($fields)
        );
        
        // Create
        ResourceTag::create(clean($request->only($fields)));
        Flash::success('Tag created');
        return $this->ajaxResponse('Tag created');
    }
    
    /**
     * Update an existing category.
     * @param                          $id
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        // Authorise
        $this->requireAjax();
        $tag = ResourceTag::findOrFail($id);
        $this->authorize('update', $tag);
        
        // Create the slug
        $this->createSlug($request);
        
        // Validate
        $fields        = ['name', 'slug'];
        $rules         = ResourceTag::getValidationRules($fields);
        $rules['slug'] .= ",{$id},id";
        $this->validate(
            $request,
            $rules,
            ResourceTag::getValidationMessages($fields)
        );
        
        // Update
        $tag->update(clean($request->only($fields)));
        Flash::success('Tag updated');
        return $this->ajaxResponse('Tag updated');
    }
    
    /**
     * Delete an existing category.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Authorise
        $this->requireAjax();
        $tag = ResourceTag::findOrFail($id);
        $this->authorize('delete', $tag);
        
        // Delete
        $tag->delete();
        Flash::success('Tag deleted');
        return $this->ajaxResponse('Tag deleted');
    }
    
    /**
     * Ensure that the request contains a slug field.
     * @param \Illuminate\Http\Request $request
     */
    private function createSlug(Request $request)
    {
        return $request->merge([
            'slug' => $this->slugify($request),
        ]);
    }
}
