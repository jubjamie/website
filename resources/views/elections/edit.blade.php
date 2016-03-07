@section('page-id', 'edit')
@section('title', 'Edit Election')

@include('elections._form', [
    'route' => route('elections.update', ['id' => $election->id])
])