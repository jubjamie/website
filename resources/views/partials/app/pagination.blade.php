@if(is_a($paginator, 'Illuminate\Pagination\LengthAwarePaginator'))
<nav class="pagination">{!! with(new App\Pagination\Presenter($paginator->appends(Input::except('page'))))->render() !!}</nav>
@endif