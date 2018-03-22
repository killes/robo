<?php

namespace Thunder\Robo\Task\Site;

use Robo\Common\BuilderAwareTrait;
use Robo\Contract\BuilderAwareInterface;
use Robo\Task\BaseTask;

/**
 * Robo task base: Update site.
 */
class Update extends BaseTask implements BuilderAwareInterface {

  use BuilderAwareTrait;

  /**
   * Environment.
   *
   * @var string
   */
  protected $environment;

  /**
   * Constructor.
   *
   * @param string $environment
   *   An environment string.
   */
  public function __construct($environment) {
    $this->environment = $environment;
  }

  /**
   * Return task collection for this task.
   *
   * @return \Robo\Collection\Collection
   *   The task collection.
   */
  public function collection() {
    $collection = $this->collectionBuilder();

    // Set up filesystem.
    $collection->addTask($this->collectionBuilder()->taskSiteSetupFileSystem($this->environment));

    $collection->addTaskList([
      // Apply database updates.
      'Update.applyDatabaseUpdates' => $this->collectionBuilder()->taskDrushApplyDatabaseUpdates(),
      // Apply entity schema updates.
      'Update.applyEntitySchemaUpdates' => $this->collectionBuilder()->taskDrushEntitySchemaUpdates(),
      // Import configuration.
      'Update.drushConfigImport' => $this->collectionBuilder()->taskDrushConfigImport(),
      // Update translations.
      'Install.localeUpdate' => $this->collectionBuilder()->taskDrushLocaleUpdate(),
      // Clear all caches.
      'Update.cacheRebuild' => $this->collectionBuilder()->taskDrushCacheRebuild(),
    ]);

    return $collection->original();
  }

  /**
   * {@inheritdoc}
   */
  public function run() {
    return $this->collection()->run();
  }

}
