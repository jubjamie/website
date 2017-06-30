<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\ResourceCategory;
use App\Traits\CreatesSlugs;
use Illuminate\Http\Request;
use Szykra\Notifications\Flash;

class CategoryController extends Controller
{
    use CreatesSlugs;
    
    /**
     * View the list of categories.
     * @return $this
     */
    public function index()
    {
        // Authorise
        $this->authorize('index', ResourceCategory::class);
        
        // Get the list of categories
        $categories = ResourceCategory::orderBy('name', 'ASC')
                                      ->paginate(20);
        $this->checkPagination($categories);
        return view('resources.categories.index')->with('categories', $categories);
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
        $this->authorize('create', ResourceCategory::class);
        
        // Create the slug
        $this->createSlug($request);
        
        // Validate
        $fields = ['name', 'slug', 'flag'];
        $this->validate(
            $request,
            ResourceCategory::getValidationRules($fields),
            ResourceCategory::getValidationMessages($fields)
        );
        
        // Create
        ResourceCategory::create(clean($request->only($fields)));
        Flash::success('Category created');
        return $this->ajaxResponse('Category created');
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
        $category = ResourceCategory::findOrFail($id);
        $this->authorize('update', $category);
        
        // Create the slug
        $this->createSlug($request);
        
        // Validate
        $fields        = ['name', 'slug', 'flag'];
        $rules         = ResourceCategory::getValidationRules($fields);
        $rules['slug'] .= ",{$id},id";
        $this->validate(
            $request,
            $rules,
            ResourceCategory::getValidationMessages($fields)
        );
        
        // Update
        $category->update(clean($request->only($fields)));
        Flash::success('Category updated');
        return $this->ajaxResponse('Category updated');
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
        $category = ResourceCategory::findOrFail($id);
        $this->authorize('delete', $category);
        
        // Delete
        $category->delete();
        Flash::success('Category deleted');
        return $this->ajaxResponse('Category deleted');
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
