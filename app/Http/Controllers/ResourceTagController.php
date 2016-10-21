<?php
    
    namespace App\Http\Controllers;
    
    use App\Http\Requests\GenericRequest;
    use App\ResourceTag;
    use Illuminate\Http\Request;
    use App\Http\Requests;
    use App\Http\Controllers\Controller;
    use Illuminate\Support\Facades\Response;
    use Illuminate\Support\Facades\View;
    use Szykra\Notifications\Flash;
    
    class ResourceTagController extends Controller
    {
        public function __construct()
        {
            parent::__construct();
            $this->middleware('auth.permission:admin');
        }
        
        /**
         * View all the existing tags (tabulated).
         * @return mixed
         */
        public function index()
        {
            $tags = ResourceTag::orderBy('slug', 'ASC')->paginate(20);
            
            return View::make('resources.tags.list')
                       ->with('tags', $tags);
        }
        
        /**
         * Create a new resource tag.
         * @param \App\Http\Requests\GenericRequest $request
         * @return mixed
         */
        public function store(GenericRequest $request)
        {
            // Require ajax request
            $this->requireAjax($request);
            
            // Validate
            $request->merge(['slug' => $this->createSlug($request)]);
            $this->validate($request, ResourceTag::getValidationRules('name', 'slug'), ResourceTag::getValidationMessages('name', 'slug'));
            
            // Create the category
            ResourceTag::create($request->stripped('name', 'slug'));
            Flash::success('Tag created');
            
            return Response::json(true);
        }
        
        /**
         * Update a tag's details.
         * @param                                   $id
         * @param \App\Http\Requests\GenericRequest $request
         * @return mixed
         */
        public function update($id, GenericRequest $request)
        {
            // Require ajax request
            $this->requireAjax($request);
            
            // Get the tag
            $tag = ResourceTag::find($id);
            if(!$tag) {
                return $this->ajaxError('Couldn\'t find that tag', 404);
            }
            
            // Validate
            $rules = ResourceTag::getValidationRules('name', 'slug');
            $rules['slug'] .= ",{$id},id";
            $request->merge(['slug' => $this->createSlug($request)]);
            $this->validate($request, $rules, ResourceTag::getValidationMessages('name', 'slug'));
            
            // Update
            $tag->update($request->stripped('name', 'slug'));
            Flash::success('Tag updated');
            
            return Response::json(true);
        }
        
        /**
         * Delete a tag.
         * @param                                   $id
         * @param \App\Http\Requests\GenericRequest $request
         * @return mixed
         */
        public function destroy($id, GenericRequest $request)
        {
            // Require ajax
            $this->requireAjax($request);
            
            // Get the tag
            $tag = ResourceTag::find($id);
            if(!$tag) {
                return $this->ajaxError("Couldn't find that tag", 404);
            }
            
            // Delete
            $tag->delete();
            Flash::success('Tag deleted');
            
            return Response::json(true);
        }
    }
