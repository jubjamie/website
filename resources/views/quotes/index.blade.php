@extends('app')
@section('page-section', 'quotes')
@section('title', 'Quotesboard')

@section('scripts')
    $modal.on('show.bs.modal', function(event) {
        var btn = $(event.relatedTarget);
        $modal.find('#addQuoteModal').data('formAction', btn.data('formAction'));
    });
@endsection

@section('add_quote_button')
    <button class="btn btn-success"
            data-toggle="modal"
            data-target="#modal"
            data-modal-template="quote_add"
            data-modal-class="modal-sm"
            data-modal-title="Add a Quote"
            data-form-action="{{ route('quotes.add') }}"
            id="newQuoteButton">
        <span class="fa fa-plus"></span>
        <span>Add a new quote</span>
    </button>
@endsection

@section('content')
    <h1 class="page-header">Quotes Board</h1>
    @if(count($quotes) > 0)
        @yield('add_quote_button')
        {!! Form::open(['route' => 'quotes.delete']) !!}
            @foreach($quotes as $i => $quote)
                <div class="quote">
                    <div class="quote-number">#{{ ($quotes->currentPage() - 1) * $quotes->perPage() + $i + 1 }}</div>
                    <div class="quote-details">
                        <div class="quote-content">
                            {!! str_replace(PHP_EOL, '</p><p>', \GrahamCampbell\Markdown\Facades\Markdown::convertToHtml($quote->quote)) !!}
                        </div>
                        @if(!is_null($quote->added_by))
                            <div class="quote-date">
                                Said by {{ $quote->culprit }} {{ $quote->date->diffForHumans() }}
                            </div>
                        @endif
                        <div class="quote-actions">
                            @if($activeUser->isAdmin())
                            {{--<span class="fa fa-thumbs-up" title="Like this quote"></span>--}}
                            {{--<span class="fa fa-thumbs-down" title="Dislike this quote"></span>--}}
                            {{--<span class="fa fa-flag" title="Mark this as inappropriate"></span>--}}
                                <button class="btn btn-link" name="deleteQuote" onclick="return confirm('Are you sure you want to delete this quote?')" title="Delete this quote" value="{{ $quote->id }}">
                                    <span class="fa fa-trash"></span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        {!! Form::close() !!}
        @yield('add_quote_button')
        @include('partials.app.pagination', ['paginator' => $quotes])
    @else
        <div class="text-center">
            <h3>We don't seem to have any good quotes</h3>
            <h4>You guys need to start embarrassing yourselves</h4>
            @yield('add_quote_button')
        </div>
    @endif
@endsection


@section('modal')
    <div data-type="modal-template" data-id="quote_add">
        @include('quotes.form')
    </div>
@endsection