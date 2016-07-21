<?php

namespace Thunder\Robo\Task\Environment;

use Robo\Task\BaseTask;
use Thunder\Robo\Utility\Environment;
use Thunder\Robo\Utility\PathResolver;

class Initialize extends BaseTask {

  /**
   * Environment.
   *
   * @var string
   */
  protected $environment;

  public function __construct($environment) {
    $this->environment = $environment;
  }

  /**
   * @return \Robo\Result
   */
  public function run() {
    Environment::set($this->environment);

    if(Environment::isDevdesktop()) $this->ensureDevdesktopPath();
  }

  public function ensureDevdesktopPath() {
    $path = PathResolver::getDevdesktopPath();
    while(!file_exists($path)) {
      $path = $this->ask('Path to DevDesktop');
      if($path == "") {
        $this->say('WARNING: Will use default binarys!');
        $path = NULL;
        break;
      }
    }
    PathResolver::setDevdesktopPath($path);
  }
}