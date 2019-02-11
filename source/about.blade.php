@extends('_layouts.master')

@push('meta')
    <meta property="og:title" content="About {{ $page->siteName }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ $page->getUrl() }}"/>
    <meta property="og:description" content="A little bit about {{ $page->siteName }}" />
@endpush

@section('body')
    <h1>About</h1>

    <img src="/assets/img/kevin.jpg"
        alt="Kevin"
        class="flex h-64 bg-contain mx-auto md:float-right my-6 md:ml-10">

    <p class="mb-6">This is what it will look like with text.</p>

    <p class="mb-6">loermsadasddas</p>

    <p class="mb-6"></p>
@endsection
