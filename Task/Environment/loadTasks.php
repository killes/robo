<?php

namespace Thunder\Robo\Task\Environment;

trait loadTasks {

  /**
   * Initialize environment
   *
   * @param string $environment
   *    An environemnt string
   * @return Initialize
   */
  protected function taskEnvironmentInitialize($environment) {
    return new Initialize($environment);
  }
}