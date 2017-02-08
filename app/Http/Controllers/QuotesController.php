<?php
    
    namespace App\Http\Controllers;
    
    use App\Http\Requests\QuoteRequest;
    use App\Quote;
    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Szykra\Notifications\Flash;
    
    class QuotesController extends Controller
    {
        /**
         * QuotesController constructor.
         */
        public function __construct()
        {
            $this->middleware('auth');
        }
        
        /**
         * Display a listing of the resource.
         * @return \Illuminate\Http\Response
         */
        public function index()
        {
            $this->authorize('view', Quote::class);
            $quotes = Quote::orderBy('created_at', 'DESC')->paginate(15);
            $this->checkPagination($quotes);
            
            return view('quotes.index')->with('quotes', $quotes);
        }
        
        /**
         * Store a newly created resource in storage.
         * @param \App\Http\Requests\QuoteRequest $request
         * @return \Illuminate\Http\Response
         */
        public function store(QuoteRequest $request)
        {
            // Create quote
            Quote::create([
                'culprit'  => clean($request->get('culprit')),
                'quote'    => clean($request->get('quote')),
                'date'     => Carbon::createFromFormat('Y-m-d H:i:s', $request->get('date'))->addMinutes($request->header('TZ-OFFSET')),
                'added_by' => $request->user()->id,
            ]);
            Flash::success('Quote created');
            
            return $this->ajaxResponse('Quote created');
        }
        
        /**
         * Remove the specified resource from storage.
         * @param  \App\Quote $quote
         * @return \Illuminate\Http\Response
         */
        public function destroy(Quote $quote)
        {
            $this->authorize('delete', $quote);
            $quote->delete();
            Flash::success('Quote deleted');
            
            return redirect()->back();
        }
    }
