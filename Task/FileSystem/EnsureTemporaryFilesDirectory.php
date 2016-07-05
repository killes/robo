<?php

namespace Thunder\Robo\Task\FileSystem;

use Thunder\Robo\Utility\PathResolver;

/**
 * Robo task: Ensure temporary files directory.
 */
class EnsureTemporaryFilesDirectory extends EnsureDirectorySkippedIfAcquia {

  /**
   * {@inheritdoc}
   */
  protected function getPath() {
    return PathResolver::temporaryFilesDirectory();
  }

  /**
   * {@inheritdoc}
   */
  protected function skip() {
    return parent::skip() || $this->getPath() === sys_get_temp_dir();
  }

}
