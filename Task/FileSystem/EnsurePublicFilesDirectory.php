<?php

namespace Thunder\Robo\Task\FileSystem;

use Thunder\Robo\Utility\PathResolver;

/**
 * Robo task: Ensure public files directory.
 */
class EnsurePublicFilesDirectory extends EnsureDirectorySkippedIfAcquia {

  /**
   * {@inheritdoc}
   */
  protected function getPath() {
    return PathResolver::publicFilesDirectory();
  }

}
