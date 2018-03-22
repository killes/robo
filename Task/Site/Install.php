<?php

namespace Thunder\Robo\Task\Site;

use Robo\Common\BuilderAwareTrait;
use Robo\Contract\BuilderAwareInterface;
use Thunder\Robo\Task\Drush\SiteInstall;
use Thunder\Robo\Utility\PathResolver;
use Robo\Task\BaseTask;

/**
 * Robo task base: Install site.
 */
class Install extends BaseTask implements BuilderAwareInterface {

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
    $dump = PathResolver::databaseDump();

    // No database dump file present -> perform initial installation, export
    // configuration and create database dump file.
    if (!file_exists($dump)) {
      $collection->addTaskList([
        // Install Drupal site.
        'Install.siteInstall' => new SiteInstall(),
      ]);

      // Set up file system.
      $collection->addTask($this->collectionBuilder()->taskSiteSetupFileSystem($this->environment));

      $collection->addTaskList([
        // Ensure 'config' and 'locale' module.
        'Install.enableExtensions' => $this->collectionBuilder()->taskDrushEnableExtension(['config', 'locale']),
        // Update translations.
        'Install.localeUpdate' => $this->collectionBuilder()->taskDrushLocaleUpdate(),
        // Rebuild caches.
        'Install.cacheRebuild' => $this->collectionBuilder()->taskDrushCacheRebuild(),
        // Export configuration.
        'Install.configExport' => $this->collectionBuilder()->taskDrushConfigExport(),
        // Export database dump file.
        'Install.databaseDumpExport' => $this->collectionBuilder()->taskDatabaseDumpExport($dump),
      ]);
    }

    // Database dump file already exists -> import it and update database with
    // latest exported configuration (if any).
    else {
      $collection->addTaskList([
        // Drop all tables.
        'Install.sqlDrop' => $this->collectionBuilder()->taskDrushSqlDrop(),
        // Import database dump.
        'Install.databaseDumpImport' => $this->collectionBuilder()->taskDatabaseDumpImport($dump)
      ]);

      // Perform site update tasks
      $collection->addTask($this->collectionBuilder()->taskSiteUpdate($this->environment));
    }

    return $collection->original();
  }

  /**
   * {@inheritdoc}
   */
  public function run() {
    return $this->collection()->run();
  }

}
