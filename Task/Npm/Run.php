<?php

namespace Thunder\Robo\Task\Npm;

use Robo\Contract\CommandInterface;
use Robo\Task\Npm\Base;

/**
 * Robo task: Run an npm command.
 */
class Run extends Base implements CommandInterface {

  /**
   * {@inheritdoc}
   */
  protected $action = 'run';

  /**
   * {@inheritdoc}
   */
  public function run() {
    $this->printTaskInfo('Run Npm command: ' . $this->arguments);
    return $this->executeCommand($this->getCommand());
  }

}
