<div class="search-tools pull-{{ $filterAlign or 'right' }}">
    @if(isset($filterOptions))
        <div class="dropdown filter">
            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" title="Filter">
                <span class="fa fa-filter"></span>
            </button>
            <ul class="dropdown-menu">
                <li{{ empty($filterValue) ? ' class=current': '' }}>{!! link_to($filterBaseUrl, '- no filter -', ['class' =>
                'em']) !!}</li>
                @foreach($filterOptions as $filter => $text)
                    <li{{ $filter == $filterValue ? ' class=current' : '' }}>{!! link_to($filterBaseUrl.'?'.http_build_query(array_merge($filterBaseQuery,
                    ['filter' =>
                    $filter])),$text)
                    !!}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(in_array('searchValue', array_keys(get_defined_vars())))
        <div class="dropdown search">
            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" title="Search">
                <span class="fa fa-search"></span>
            </button>
            <ul class="dropdown-menu">
                <li>
                    <input class="form-control" type="text" value="{{ $searchValue or '' }}">
                    @if(isset($searchValue) && $searchValue)
                        <a class="clear-search"
                           href="{{ $filterBaseUrl . (!empty($filterBaseQuery) ? ('?'.http_build_query($filterBaseQuery)) : '') }}"
                           title="Clear search">
                            <span class="fa fa-remove"></span>
                        </a>
                    @endif
                </li>
            </ul>
        </div>
    @endif
</div>