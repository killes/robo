<?php

namespace Thunder\Robo\Task\Site;

use Robo\Common\BuilderAwareTrait;
use Robo\Contract\BuilderAwareInterface;
use Thunder\Robo\Task\FileSystem\EnsurePrivateFilesDirectory;
use Thunder\Robo\Task\FileSystem\EnsurePublicFilesDirectory;
use Thunder\Robo\Task\FileSystem\EnsureTemporaryFilesDirectory;
use Thunder\Robo\Task\FileSystem\EnsureTranslationFilesDirectory;
use Robo\Task\BaseTask;

/**
 * Robo task base: Set up file system.
 */
class SetupFileSystem extends BaseTask implements BuilderAwareInterface {

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

    $collection->addTaskList([
      // Ensure private files directory.
      'Setup.ensurePrivateFilesDirectory' => new EnsurePrivateFilesDirectory($this->environment),
      // Ensure public files directory.
      'Setup.ensurePublicFilesDirectory' => new EnsurePublicFilesDirectory($this->environment),
      // Ensure temporary files directory.
      'Setup.ensureTemporaryFilesDirectory' => new EnsureTemporaryFilesDirectory($this->environment),
      // Ensure translation files directory.
      'Setup.ensureTranslationFilesDirectory' => new EnsureTranslationFilesDirectory($this->environment),
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
