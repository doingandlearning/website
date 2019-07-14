@extends('_layouts.master') @section('body')
  <div class="bg-image-gradient-transparent flex-1">
    <div class="mx-auto py-8 px-6 sm:px-8 lg:px-12 xl:px-24 lg:max-w-2lg xl:max-w-3xl">
      <div class="text-center md:text-left my-4 sm:my-6 md:my-8 flex flex-col flex-col-reverse md:flex-row">
        <div class="md:w-1/2">
          <h1 class="text-grey-darkest font-normal">Web developer</h1>
          <p
              class="font-light mt-8 text-lg text-left text-grey-darkest leading-loose max-w-md md:max-w-full mx-auto md:mx-0 lg:pr-24 xl:pr-32"
          >
            Hi! I'm Kevin.
          </p>
          <p
              class="font-light mt-8 text-lg text-left text-grey-darkest leading-loose max-w-md md:max-w-full mx-auto md:mx-0 lg:pr-24 xl:pr-32"
          >
            I like to make things with code.
          </p>
          <p
              class="font-light mt-8 text-lg text-left text-grey-darkest leading-loose max-w-md md:max-w-full mx-auto md:mx-0 lg:pr-24 xl:pr-32"
          >  Check out the <a href="/blog">blog</a> and
            <a href="/contact">get in touch</a> if you'd like. Alternatively, hit me
            up on <a href="http://www.twitter.com/dolearning">twitter</a>.
          </p>
        </div>

        <div
            class="md:w-1/2 max-w-xs md:max-w-full mx-auto md:mx-0 mb-12 md:mb-0 md:pl-12"
        >
          <img
              class="block w-full rounded shadow-lg-dark"
              src="/assets/img/kevin.jpg"
              alt="Kevin Cunningham"
          />
        </div>
      </div>
    </div>
  <div class="container mx-auto flex">

  @foreach ($posts->slice(0,2) as $post)
    @include('_components.post_card')
  @endforeach
  </div>
@endsection
