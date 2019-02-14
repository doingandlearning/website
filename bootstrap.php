<?php
use TightenCo\Jigsaw\Loaders\CollectionRemoteItemLoader;
use TightenCo\Jigsaw\Loaders\DataLoader;
// @var $container \Illuminate\Container\Container
// @var $events \TightenCo\Jigsaw\Events\EventBus

/*
 * You can run custom code at different stages of the build process by
 * listening to the 'beforeBuild', 'afterCollections', and 'afterBuild' events.
 *
 * For example:
 *
 * $events->beforeBuild(function (Jigsaw $jigsaw) {
 *     // Your code here
 * });
 */

// $container->bind(AddTagsPages::class, function ($c) {
//   return new AddTagsPages($c[DataLoader::class], $c[RemoteLoader::class]);
// });

// $events->afterCollections(function ($jigsaw) use ($container) {
//   $container->make(AddTagsPages::class)->handle($jigsaw);
// });
$container->bind(\TightenCo\Jigsaw\Handlers\CollectionItemHandler::class, function ($c) {
  return new \TightenCo\Jigsaw\Handlers\CollectionItemHandler($c['config'], [
      $c[\TightenCo\Jigsaw\Handlers\MarkdownHandler::class],
      $c[\TightenCo\Jigsaw\Handlers\BladeHandler::class],
      $c[\App\Listeners\AddTagsPages::class],
      $c[\App\Listeners\AddArchivePages::class],
  ]);
});
//$container->bind(\App\Listeners\DownloadImages::class, function ($c) {
//  return new \App\Listeners\DownloadImages($c[\TightenCo\Jigsaw\Parsers\FrontMatterParser::class]);
//});

$events->afterBuild(App\Listeners\GenerateSitemap::class);
$events->afterBuild(App\Listeners\GenerateIndex::class);
