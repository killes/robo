<?php

namespace Thunder\Robo\Task\Drush;

use Robo\Result;
use Thunder\Robo\Utility\Environment;

/**
 * Robo task: Update localizations.
 */
class LocaleUpdate extends DrushTask {

  /**
   * {@inheritdoc}
   */
  public function run() {
    if (!$this->skip()) {
      return $this->exec()
        ->arg('locale-update')
        ->run();
    }

    return Result::success($this);
  }

  /**
   * Task should be skipped?
   *
   * @return bool
   *   Whether the task should be skipped or not?
   */
  protected function skip() {
    return FALSE;
  }

}
