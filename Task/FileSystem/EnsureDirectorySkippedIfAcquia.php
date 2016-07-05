<?php

namespace Thunder\Robo\Task\FileSystem;

use Thunder\Robo\Utility\Environment;

/**
 * Robo task base: Ensure directory (skipped if in Acquia environment).
 */
abstract class EnsureDirectorySkippedIfAcquia extends EnsureDirectory {

  /**
   * {@inheritdoc}
   */
  protected function skip() {
    return parent::skip() || Environment::isAcquia($this->environment);
  }

}
