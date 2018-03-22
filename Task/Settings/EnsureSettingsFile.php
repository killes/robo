<?php

namespace Thunder\Robo\Task\Settings;

use Robo\Common\BuilderAwareTrait;
use Robo\Common\IO;
use Robo\Contract\BuilderAwareInterface;
use Thunder\Robo\Utility\Environment;
use Thunder\Robo\Utility\PathResolver;
use Robo\Result;
use Robo\Task\BaseTask;

/**
 * Robo task base: Ensure settings file for environment.
 */
class EnsureSettingsFile extends BaseTask implements BuilderAwareInterface {

  use IO;
  use BuilderAwareTrait;

  /**
   * Database: Host.
   *
   * @var string
   */
  protected $db_host;

  /**
   * Database: Name.
   *
   * @var string
   */
  protected $db_name;

  /**
   * Database: Port.
   *
   * @var string
   */
  protected $db_port;

  /**
   * Database: User.
   *
   * @var string
   */
  protected $db_user;

  /**
   * Database: Password.
   *
   * @var string
   */
  protected $db_pass;

  /**
   * Environment.
   *
   * @var string
   */
  protected $environment;

  /**
   * File path.
   *
   * @var string
   */
  protected $file;

  /**
   * Constructor.
   *
   * @param string $environment
   *   An environment string.
   */
  public function __construct($environment) {
    $this->environment = $environment;
    $this->file = PathResolver::siteDirectory() . '/settings.' . $environment . '.php';
  }

  /**
   * {@inheritdoc}
   */
  public function run() {
    if (!$this->skip()) {
      $this->printTaskError('Settings file not available: ' . $this->file);
      $this->printTaskError('Please create one.');

      return Result::error($this, 'Settings file not available: ' . $this->file);
    }

    return Result::success($this);
  }

  /**
   * Assemble settings file source code lines.
   *
   * @return array
   *   An array of source code lines.
   */
  protected function lines() {
    $lines = array_merge(
      ['<?php'],
      $this->linesFileComment(),
      $this->linesOverrides()
    );

    if(!$this->skipDatabaseConnection()) {
      $lines = array_merge(
        $lines,
        $this->linesDatabaseConnection()
      );
    }

    return $lines;
  }

  /**
   * Assemble settings file source code lines for database connection.
   *
   * @return array
   *   An array of source code lines.
   */
  protected function linesDatabaseConnection() {
    $lines = [
      '',
      "\$databases['default']['default'] = array(",
      "  'database' => '" . $this->db_name . "',",
      "  'username' => '" . $this->db_user . "',",
      "  'password' => '" . $this->db_pass . "',",
      "  'host' => '" . $this->db_host . "',",
      "  'port' => '" . $this->db_port . "',",
      "  'driver' => 'mysql',",
      "  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',",
      ");",
    ];

    return $lines;
  }

  /**
   * Assemble settings file source code lines for file comment.
   *
   * @return array
   *   An array of source code lines.
   */
  protected function linesFileComment() {
    $lines = [
      '',
      '/**',
      ' * @file',
      ' * Settings for \'' . $this->environment . '\' environment.',
      ' *',
      ' * This file was generated automatically during Robo execution, but you may',
      ' * alter it to suite your needs.',
      ' */',
    ];

    return $lines;
  }

  /**
   * Assemble settings file source code lines for settings/config overrides.
   *
   * @return array
   *   An array of source code lines.
   */
  protected function linesOverrides() {
    $lines = [];

    // Add specific settings for local environment.
    if ($this->environment === Environment::LOCAL || Environment::isDevdesktop()) {
      $lines = array_merge($lines, [
        '',
        '// Enable development services.',
        '$settings[\'container_yamls\'][] = DRUPAL_ROOT . \'/sites/development.services.yml\';',
        '',
        '// Disable render cache.',
        '$settings[\'cache\'][\'bins\'][\'render\'] = \'cache.backend.null\';',
        '',
        '// Disable CSS/JS aggregation.',
        '$config[\'system.performance\'][\'css\'][\'preprocess\'] = FALSE;',
        '$config[\'system.performance\'][\'js\'][\'preprocess\'] = FALSE;',
        '',
        '// Set error level to verbose.',
        '$config[\'system.logging\'][\'error_level\'] = \'verbose\';',
        '',
        '// Set temporary folder.',
        '$config[\'system.file\'][\'path\'][\'temporary\'] = \'../tmp\';',
        '',
        '// Set private folder.',
        '$settings[\'file_private_path\'] = \'../private\';',
      ]);
    }
    $lines = array_merge($lines, [
      '',
      '// Set hash_salt.',
      '$settings[\'hash_salt\'] =  \'' . bin2hex(openssl_random_pseudo_bytes(10)) . '\';',
    ]);

    return $lines;
  }

  /**
   * Task should be skipped?
   *
   * @return bool
   *   Whether the task should be skipped or not?
   */
  protected function skip() {
    return file_exists($this->file);
  }

  /**
   * Creation of database connection details should be skipped?
   *
   * @return bool
   *  Whether the creation of database connection details should be skipped
   */
  protected function skipDatabaseConnection() {
    return Environment::isDevdesktop();
  }

}
