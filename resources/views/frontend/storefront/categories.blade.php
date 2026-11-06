@extends('layouts.frontend')

@section('title', 'Shop by Category | ' . (optional($appSetting)->site_name ?: 'Maanyas'))

@section('styles')
<link rel="stylesheet" href="{{ asset('frontend/css/storefront-categories.css') }}">
@endsection

@section('content')
<main class="store-category-page">
    <div class="store-category-container">
        <div class="store-category-heading">
            <p>Find your style</p>
            <h1>Shop by Category</h1>
        </div>

        @if ($categories->isNotEmpty())
            <div class="store-category-grid">
                @foreach ($categories as $category)
                    <article class="store-category-card">
                        <a href="{{ url('/category/' . $category->slug) }}">
                            <strong>{{ $category->name }}</strong>
                            <span>Explore collection <span aria-hidden="true">→</span></span>
                        </a>
                        @if ($category->childrenRecursive->isNotEmpty())
                            <div class="store-category-children">
                                @foreach ($category->childrenRecursive as $child)
                                    <a href="{{ url('/category/' . $child->slug) }}">{{ $child->name }}</a>
                                @endforeach
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
        @else
            <p>No categories are available right now.</p>
        @endif
    </div>
</main>
@endsection
