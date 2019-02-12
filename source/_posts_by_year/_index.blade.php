@extends('_layouts.page')

@section('pageContent')
    <h1 class="text-4xl md:text-5xl lg:text-6xl font-normal">Archives</h1>
    <p class="text-base md:text-lg lg:text-xl uppercase text-grey">for {{ $page->year }}</p>
    <main class="mt-6 sm:mt-12 text-lg antialiased leading-normal" role="main">
        @each('_partials.post', $page->getPostsForYear($posts), 'post')
    </main>
@endsection
