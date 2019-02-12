<?php
namespace App;

use Illuminate\Support\Arr;
use TightenCo\Jigsaw\Jigsaw;
use Illuminate\Support\Collection;
use Illuminate\Container\Container;
use TightenCo\Jigsaw\Loaders\DataLoader;
use TightenCo\Jigsaw\Loaders\CollectionRemoteItemLoader;

abstract class PseudoCollectionGenerator
{
  /**
   * @var \TightenCo\Jigsaw\Jigsaw
   */
  protected $jigsaw;
  /**
   * @var \Illuminate\Support\Collection
   */
  protected $collectionData;
  /**
   * @var \TightenCo\Jigsaw\Loaders\DataLoader
   */
  protected $dataLoader;
  /**
   * @var \TightenCo\Jigsaw\Loaders\CollectionRemoteItemLoader
   */
  protected $remoteItemLoader;
  /**
   * Construct new instance.
   *
   * @param  \TightenCo\Jigsaw\Loaders\DataLoader  $dataLoader
   * @param  \TightenCo\Jigsaw\Loaders\CollectionRemoteItemLoader  $remoteItemLoader
   */
  public function __construct(DataLoader $dataLoader, CollectionRemoteItemLoader $remoteItemLoader)
  {
    $this->dataLoader = $dataLoader;
    $this->remoteItemLoader = $remoteItemLoader;
  }
  /**
   * Register bindings for listener in the container and start listening for the event.
   *
   * @param  \Illuminate\Container\Container  $container
   * @return void
   */
  public static function register(Container $container)
  {
    static::registerHelpers($container);
    $container->bind(static::class, function ($c) {
      return new static($c[DataLoader::class], $c[CollectionRemoteItemLoader::class]);
    });
    $container->events->afterCollections(function ($jigsaw) use ($container) {
      $container->make(static::class)->handle($jigsaw);
    });
  }
  /**
   * Register helpers in config.
   *
   * @param  \Illuminate\Container\Container  $container
   * @return void
   */
  protected static function registerHelpers($container)
  {
    $helpers = static::helpers();
    if (empty($helpers)) {
      return;
    }
    $config = $container->config;
    foreach ($helpers as $helperName => $helper) {
      Arr::set($config, $helperName, $helper);
    }
    $container->instance('config', $config);
  }
  /**
   * Helpers that should be registered.
   *
   * @return array
   */
  protected static function helpers()
  {
    return [];
  }
  /**
   * Handle `afterCollections` hook to add new collections based on data from existing ones.
   *
   * @param  \TightenCo\Jigsaw\Jigsaw  $jigsaw
   * @return void
   */
  public function handle(Jigsaw $jigsaw)
  {
    $collectionData = $this->generateCollectionData($jigsaw);
    $jigsaw->getSiteData()->addCollectionData($collectionData);
  }
  /**
   * Generate new collection with included new data.
   *
   * @param  \TightenCo\Jigsaw\Jigsaw  $jigsaw
   * @return \Illuminate\Support\Collection
   */
  protected function generateCollectionData(Jigsaw $jigsaw)
  {
    return $this->setJigsawInstance($jigsaw)
      ->appendNewCollectionsToConfigurations()
      ->scheduleCleanup()
      ->loadCollectionData();
  }
  /**
   * Set working Jigsaw instance.
   *
   * @param  \TightenCo\Jigsaw\Jigsaw  $jigsaw
   * @return $this
   */
  protected function setJigsawInstance(Jigsaw $jigsaw)
  {
    $this->jigsaw = $jigsaw;
    return $this;
  }
  /**
   * Add new collections data to jigsaw site data.
   *
   * @return $this
   */
  protected function appendNewCollectionsToConfigurations()
  {
    $collections = $this->jigsaw->app->config->get('collections');
    $this->appendNewCollectionsTo($collections);
    return $this;
  }
  /**
   * Append new collections to given old collections.
   *
   * @param  \Illuminate\Support\Collection  $collections
   * @return void
   */
  protected function appendNewCollectionsTo($collections)
  {
    $this->getCollectionsConfigurations()
      ->each(function ($collectionSettings, $collectionName) use ($collections) {
        $collections->put($collectionName, $collectionSettings);
      });
  }
  /**
   * Get new collections configurations.
   *
   * @return \Illuminate\Support\Collection
   */
  abstract protected function getCollectionsConfigurations();
  /**
   * Generate collections data for remote collections.
   *
   * @return $this
   */
  protected function loadCollectionData()
  {
    $siteData = $this->loadSiteData();
    $this->writeTempSiteData($siteData);
    return $this->dataLoader->loadCollectionData($siteData, $this->jigsaw->getSourcePath());
  }
  /**
   * Load site data with added configurations.
   *
   * @return \TightenCo\Jigsaw\SiteData
   */
  protected function loadSiteData()
  {
    return $this->dataLoader->loadSiteData($this->jigsaw->app->config);
  }
  /**
   * Write temporary collection pages.
   *
   * @param  \TightenCo\Jigsaw\SiteData  $siteData
   * @return void
   */
  protected function writeTempSiteData($siteData)
  {
    $this->remoteItemLoader->write($siteData->collections, $this->jigsaw->getSourcePath());
  }
  /**
   * Cleanup temporary collection items.
   *
   * @return $this
   */
  protected function scheduleCleanup()
  {
    $this->jigsaw->app->events->afterBuild(function () {
      $this->remoteItemLoader->cleanup();
    });
    return $this;
  }
}
