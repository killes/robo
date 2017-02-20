<?php

namespace Thunder\Robo\Task\Site;

use Thunder\Robo\Task\Drush\ApplyDatabaseUpdates;
use Thunder\Robo\Task\Drush\ApplyEntitySchemaUpdates;
use Thunder\Robo\Task\Drush\CacheRebuild;
use Thunder\Robo\Task\Drush\ConfigImport;
use Thunder\Robo\Task\Drush\LocaleUpdate;
use Robo\Collection\Collection;
use Robo\Task\BaseTask;
use Thunder\Robo\Utility\Environment;

/**
 * Robo task base: Update site.
 */
class Update extends BaseTask {

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
    $collection = new Collection();

    // Set up filesystem.
    $collection->add((new SetupFileSystem($this->environment))->collection());

    $collection->add([
      // Apply database updates.
      'Update.applyDatabaseUpdates' => new ApplyDatabaseUpdates(),
      // Apply entity schema updates.
      'Update.applyEntitySchemaUpdates' => new ApplyEntitySchemaUpdates(),
      // Import configuration.
      'Update.drushConfigImport' => new ConfigImport(),
      // Update translations.
      'Install.localeUpdate' => new LocaleUpdate(),
      // Clear all caches.
      'Update.cacheRebuild' => new CacheRebuild(),
    ]);

    return $collection;
  }

  /**
   * {@inheritdoc}
   */
  public function run() {
    return $this->collection()->run();
  }

}
