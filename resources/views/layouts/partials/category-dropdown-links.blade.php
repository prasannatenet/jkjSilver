@foreach ($categories as $category)
    <a href="{{ url('/category/' . $category->slug) }}">{{ $category->name }}</a>
    @include('layouts.partials.category-dropdown-links', ['categories' => $category->childrenRecursive])
@endforeach
