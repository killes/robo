<?php

namespace Thunder\Robo\Task\DatabaseDump;

use Robo\Robo;
use Thunder\Robo\Utility\Drush;

/**
 * Robo task: Import database dump.
 */
class Import extends Dump {

  /**
   * {@inheritdoc}
   */
  public function run() {
    /** @var Drush $drush */
    $drush = Robo::getContainer()->get('drush');
    return $drush->exec()
      ->arg('sql-cli')
      ->rawArg('<')
      ->arg($this->filepath)
      ->run();
  }

}
