        @foreach ($tags as $tag)
          <a class="mr-2 text-xs rounded-full py-1 px-3 bg-grey-darkest text-grey-lighter hover:text-grey-lightest"  href="#">#{{ $tag }}</a>
        @endforeach
