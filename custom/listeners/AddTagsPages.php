<?php
namespace App\Listeners;

use TightenCo\Jigsaw\Jigsaw;
use App\PseudoCollectionGenerator;
use Illuminate\Support\Collection;

class AddTagsPages extends PseudoCollectionGenerator
{
  /**
   * Helpers that should be registered.
   *
   * @return array
   */
  protected static function helpers()
  {
    return [
      'getPostsByTag' => function ($page, $posts) {
        return $posts->filter(function ($post) use ($page) {
          return in_array($page->tag, $post->tags ?? []);
        });
      },
    ];
  }
  /**
   * Get new collections configurations.
   *
   * @return \Illuminate\Support\Collection
   */
  protected function getCollectionsConfigurations()
  {
    return collect([
      'posts_by_tag' => [
        'extends' => '_posts_by_tag._index',
        'path' => 'blog/tags/{tag}',
        'items' => $this->getTagItems(),
      ],
    ]);
  }
  /**
   * Map tags to page metadata.
   *
   * @return \Illuminate\Support\Collection
   */
  protected function getTagItems()
  {
    return $this->getTags()->map(function ($tag) {
      return [
        'title' => strtoupper($tag),
        'tag' => $tag,
      ];
    });
  }
  /**
   * Get all tags used in the posts.
   *
   * @return \Illuminate\Support\Collection
   */
  protected function getTags()
  {
    return $this->jigsaw->getCollection('posts')
      ->flatMap->tags
      ->filter()
      ->unique()
      ->values()
      ->toBase();
  }
}
