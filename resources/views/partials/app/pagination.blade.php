@if(is_a($paginator, 'Illuminate\Pagination\LengthAwarePaginator'))
<nav class="pagination">{!! with(new App\Pagination\Presenter($paginator))->render() !!}</nav>
@endif