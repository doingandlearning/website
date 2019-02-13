    <div class="flex-1 max-w-xs rounded overflow-hidden shadow-lg my-2 px-4 py-2 m-2">
      <img class="w-full" src="{{$post->cover_image}}" alt="Cover image">
      <div class="px-6 py-4">
        <div class="font-bold text-xl mb-2">        <a
              href="{{ $post->getUrl() }}"
              title="Read more - {{ $post->title }}"
              class="text-black font-extrabold"
          >{{ $post->title }}</a></div>
        <p class="text-grey-darker font-medium my-2">
          {{ $post->getDate()->format('F j, Y') }}
        </p>

        <p class="text-grey-darker text-base">
          {!! $post->getExcerpt(200) !!}
        </p>
      </div>
      <div class="px-6 py-4 pin-b">
        @include('_partials.tags', ['tags' => $post->tags])
      </div>
    </div>
