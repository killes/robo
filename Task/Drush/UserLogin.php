<?php

namespace Thunder\Robo\Task\Drush;
use Robo\Common\TaskIO;

/**
 * Robo task: Display one-time login URL.
 */
class UserLogin extends DrushTask {

  use TaskIO;

  /**
   * User
   *
   * @var int|string
   */
  protected $user;

  /**
   * Constructor.
   *
   * @param int|string $user
   *   An optional uid, user name, or email address for the user to log in
   *   (defaults to user ID '1').
   */
  public function __construct($user = 1) {
    $this->user = $user;
  }

  /**
   * {@inheritdoc}
   */
  public function run() {
    $this->printTaskInfo('Login URL');

    return $this->exec()
      ->arg('user-login')
      ->arg($this->user)
      ->option('no-browser')
      ->run();
  }

}
