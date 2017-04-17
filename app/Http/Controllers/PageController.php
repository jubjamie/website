<?php
    
    namespace App\Http\Controllers;
    
    use App\Http\Requests\PageRequest;
    use App\Page;
    use Illuminate\Support\Facades\Auth;
    use Szykra\Notifications\Flash;
    
    class PageController extends Controller
    {
        /**
         * PageController constructor.
         */
        public function __construct()
        {
            $this->middleware('auth')
                 ->except('show');
        }
        
        /**
         * Display a listing of the resource.
         * @return \Illuminate\Http\Response
         */
        public function index()
        {
            $this->authorize('index', Page::class);
            
            $pages = Page::orderBy('title')
                         ->paginate(15);
            $this->checkPagination($pages);
            
            return view('pages.index')->with('pages', $pages);
        }
        
        /**
         * Show the form for creating a new resource.
         * @return \Illuminate\Http\Response
         */
        public function create()
        {
            $this->authorize('create', Page::class);
            
            $page = new Page([
                'user_id'   => Auth::user()->id,
                'published' => 1,
            ]);
            
            return view('pages.create')->with('page', $page);
        }
        
        /**
         * Store a newly created resource in storage.
         * @param \App\Http\Requests\PageRequest $request
         * @return \Illuminate\Http\Response
         */
        public function store(PageRequest $request)
        {
            $page = Page::create(clean($request->only('title', 'slug', 'content', 'published', 'user_id')));
            Flash::success('Page created');
            
            return redirect()->route('page.show', ['slug' => $page->slug]);
        }
        
        /**
         * Display the specified resource.
         * @param $slug
         * @return \Illuminate\Http\Response
         */
        public function show($slug)
        {
            $page = Page::findBySlugOrFail($slug);
            
            // Check if the page is published. This is a bit of a
            // hack as authorisation doesn't support guest users.
            if(!$page->published) {
                if(Auth::check() && Auth::user()->isAdmin()) {
                    Flash::warning('Page not published', 'This page will not be viewable by non-admins until it is published.');
                } elseif(!Auth::check()) {
                    return redirect()->guest('login');
                } else {
                    app()->abort(404);
                }
            }
            
            return view('pages.view')->with('page', $page);
        }
        
        /**
         * Show the form for editing the specified resource.
         * @param $slug
         * @return \Illuminate\Http\Response
         */
        public function edit($slug)
        {
            $page = Page::findBySlugOrFail($slug);
            $this->authorize('update', $page);
            
            return view('pages.edit')->with('page', $page);
        }
        
        /*
         * Update the specified resource in storage.
         * @param  \Illuminate\Http\Request $request
         * @param                           $slug
         * @return \Illuminate\Http\Response
         */
        public function update(PageRequest $request, $slug)
        {
            $page = Page::findBySlugOrFail($slug);
            
            $page->update(clean($request->only('title', 'slug', 'content', 'published', 'user_id')));
            Flash::success('Page updated');
            
            return redirect()->route('page.index');
        }
        
        /**
         * Remove the specified resource from storage.
         * @param $slug
         * @return \Illuminate\Http\Response
         */
        public function destroy($slug)
        {
            $page = Page::findBySlugOrFail($slug);
            $this->authorize('delete', $page);
            
            $page->delete();
            Flash::success('Page deleted');
            
            return redirect()->route('page.index');
        }
    }