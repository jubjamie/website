<div class="form-group search-input">
    <div class="input-group">
        {!! Form::text('query', isset($search) ? $search->query : null, ['class' => 'form-control', 'placeholder' => 'What do you want to find?']) !!}
        <span class="input-group-addon">
            <button class="btn btn-default" name="form-action" value="do-search">
                <span class="fa fa-search"></span>
            </button>
        </span>
    </div>
</div>
<div class="tools">
    @if(isset($search))
        <div class="pull-left search-tools">
            <div class="dropdown" id="categoryDropdown">
                <a class="link grey dropdown-toggle" data-toggle="dropdown">
                    Category: {{ $category ? $category->name : 'None' }} <span class="fa fa-caret-down"></span>
                </a>
                <ul class="dropdown-menu">
                    @if($search->category)
                        <li>
                            <a href="{{ route('resources.search', Input::except('page', 'category')) }}">
                                <span class="fa fa-remove"></span>
                                Remove
                            </a>
                        </li>
                        <li role="separator" class="divider"></li>
                    @endif
                    @foreach($all_categories as $c)
                        <li>
                            <a href="{{ route('resources.search', Input::except('page', 'category') + ['category' => $c->slug]) }}">
                                <span class="fa{{ $category && $c->id == $category->id ? ' fa-check' : '' }}"></span>
                                {{ $c->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <span class="hidden-xs dropdown-separator"> | </span>
            <div class="dropdown" id="tagDropdown">
                <a class="link grey dropdown-toggle" data-toggle="dropdown">
                    Tags: {{ count($search->tags) }} <span class="fa fa-caret-down"></span>
                </a>
                <ul class="dropdown-menu">
                    @if($search->tags)
                        <li>
                            <a href="{{ route('resources.search', Input::except('page', 'tag')) }}">
                                <span class="fa fa-remove"></span>
                                Remove all
                            </a>
                        </li>
                        <li role="separator" class="divider"></li>
                    @endif
                    @foreach($all_tags as $t)
                        <li>
                            <a href="{{ in_array($t->slug, $search->tags) ? route('resources.search', Input::except('page', 'tag') + ['tag' => array_filter($search->tags, function($s) use ($t) { return $s != $t->slug; })]) : route('resources.search', Input::except('page', 'tag') + ['tag' => array_merge($search->tags, [$t->slug])]) }}">
                                <span class="fa{{ in_array($t->slug, $search->tags) ? ' fa-check' : '' }}"></span>
                                {{ $t->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    @if($activeUser->isAdmin())
        <div class="pull-right admin-tools">
            <div class="dropdown">
                <a class="link dropdown-toggle" data-toggle="dropdown">Settings</a>
                <ul class="dropdown-menu dropdown-menu-right">
                    <li><a href="{{ route('resources.category.list') }}"><span class="fa fa-bookmark"></span> Manage categories</a></li>
                    <li><a href="{{ route('resources.tag.list') }}"><span class="fa fa-tags"></span> Manage tags</a></li>
                    <li><a href="{{ route('resources.index') }}"><span class="fa fa-list-alt"></span> View all resources</a></li>
                    <li><a href="{{ route('resources.create') }}"><span class="fa fa-cloud-upload"></span> Add resource</a></li>
                </ul>
            </div>
        </div>
    @endif
</div>