<?php

namespace Thunder\Robo\Utility;

use Robo\Robo;

/**
 * A helper class for Drupal sites.
 */
class Drupal {

  /**
   * Is installed?
   *
   * @return bool
   *   Whether Drupal is already installed or not.
   *
   * @throws \Exception
   */
  public static function isInstalled() {
    $status = static::parseStatus();

    return static::statusIsBootstrapped($status);
  }

  /**
   * Return private files directory path.
   *
   * @return string|null
   *   The path to the private files directory of Drupal (if any).
   */
  public static function privateFilesDirectory() {
    $status = static::parseStatus();
    $path = $status->get('private');

    if (!empty($path)) {
      return $path;
    }

    return NULL;
  }

  /**
   * Return public files directory path.
   *
   * @return string
   *   The path to the public files directory of Drupal.
   *
   * @throws \Exception
   */
  public static function publicFilesDirectory() {
    if (!static::isInstalled()) {
      return static::publicFilesDirectoryFallback();
    }

    $status = static::parseStatus();
    $path = $status->get('files');

    if (empty($path)) {
      throw new \Exception(__CLASS__ . ' - Unable to determine public files directory.');
    }

    return $path;
  }

  /**
   * Return fallback public files directory path.
   *
   * This returns the default path of the public files directory.
   *
   * @return string
   *   The fallback path to the public files directory of Drupal.
   */
  protected static function publicFilesDirectoryFallback() {
    return PathResolver::siteDirectory() . '/files';
  }

  /**
   * Return temporary files directory path.
   *
   * @return string
   *   The path to the temporary files directory of Drupal.
   *
   * @throws \Exception
   */
  public static function temporaryFilesDirectory() {
    if (!static::isInstalled()) {
      return static::temporaryFilesDirectoryFallback();
    }

    $status = static::parseStatus();
    $path = $status->get('temp');

    if (empty($path)) {
      throw new \Exception(__CLASS__ . ' - Unable to determine temporary files directory.');
    }

    return $path;
  }

  /**
   * Return fallback temporary files directory path.
   *
   * This returns the default path of the temporary files directory.
   *
   * @return string
   *   The fallback path to the temporary files directory of Drupal.
   *
   * @throws \Exception
   */
  protected static function temporaryFilesDirectoryFallback() {
    try {
      // Custom output capture to ensure no output at all.
      ob_start();

      /** @var \Thunder\Robo\Utility\Drush $drush */
      $drush = Robo::getContainer()->get('drush');
      $exec = $drush->exec()
        ->arg('php-eval')
        ->arg(escapeshellarg('return file_directory_os_temp();'))
        ->option('format=string')
        ->run();

      // End custom output capture.
      ob_end_clean();

      if ($exec->wasSuccessful()) {
        $path = $exec->getMessage();
      }
    }
    catch (\Exception $e) {}

    if (!$path) {
      $path = ini_get('upload_tmp_dir');
    }

    if (!$path) {
      $path = sys_get_temp_dir();
    }

    if (!$path) {
      throw new \Exception(__CLASS__ . ' - Unable to determine fallback temporary files directory path.');
    }

    return $path;
  }

  /**
   * Checks if a module is enabled.
   *
   * @param string $moduleName
   *   Module that should be checked.
   *
   * @return bool
   *   Whether the module is enabeld or not.
   *
   * @throws \Exception
   *   If it's not possible to parse the module status.
   */
  public static function moduleEnabled($moduleName) {

    // Load Drupal core status via Drush.
    /** @var \Thunder\Robo\Utility\Drush $drush */
    $drush = Robo::getContainer()->get('drush');
    $output = $drush->exec()
      ->arg('pm-list')
      ->option('format=json')
      ->silent(TRUE)
      ->run()
      ->getMessage();


    // Unable to parse Drupal module info JSON.
    if (!($status = @json_decode($output))) {
      print $output;

      throw new \Exception(__CLASS__ . ' - Unable to parse module information.');
    }
    if (array_key_exists($moduleName, $status)) {
      return $status->$moduleName->status == 'enabled';
    }
    else {
      return FALSE;
    }
  }

  /**
   * Parse Drupal core status.
   *
   * @return DrupalCoreStatus
   *   The parsed Drupal core status information fetched via 'drush core-status').
   *
   * @throws \Exception
   */
  protected static function parseStatus() {
    // Load from cache (if exists).
    if (($status = static::parseStatusCache())) {
      return $status;
    }

    // Load Drupal core status via Drush.
    /** @var \Thunder\Robo\Utility\Drush $drush */
    $drush = Robo::getContainer()->get('drush');
    $output = $drush->exec()
      ->arg('core-status')
      ->option('format=json')
      ->silent(TRUE)
      ->run()
      ->getMessage();

    // Unable to parse Drupal core status JSON.
    if (!($status = @json_decode($output))) {
      print $output;

      throw new \Exception(__CLASS__ . ' - Unable to parse Drupal status.');
    }

    // Cast status.
    $status = new DrupalCoreStatus((object) $status);

    // Save to cache (if bootstrapped).
    if (static::statusIsBootstrapped($status)) {
      static::parseStatusCache($status);
    }

    return $status;
  }

  /**
   * Cache Drupal core status in global variable.
   *
   * @param DrupalCoreStatus|null $root
   *   The Drupal core status information to cache.
   *
   * @return \stdClass|null
   *   The cached Drupal core status information (if any).
   */
  protected static function parseStatusCache(DrupalCoreStatus $status = NULL) {
    $cid = '__THUNDER_ROBO_DRUPAL_STATUS__';

    if (isset($status) && !empty($status)) {
      $GLOBALS[$cid] = $status;
    }

    return isset($GLOBALS[$cid]) ? $GLOBALS[$cid] : NULL;
  }

  /**
   * @param DrupalCoreStatus $status
   *   The Druapl core status information.
   *
   * @return bool
   *   Whether the status information indicate a successful bootstrap or not.
   */
  protected static function statusIsBootstrapped(DrupalCoreStatus $status) {
    $bootstrap = $status->get('bootstrap');

    return !empty($bootstrap);
  }

}
