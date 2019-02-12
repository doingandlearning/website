@extends('_layouts.page')

@section('pageContent')
    <h1 class="text-4xl md:text-5xl lg:text-6xl font-normal">{{ $page->tag }}</h1>
    <main class="mt-6 sm:mt-12 text-lg antialiased leading-normal" role="main">
        @each('_partials.post', $page->getPostsByTag($posts), 'post')
    </main>
@endsection
