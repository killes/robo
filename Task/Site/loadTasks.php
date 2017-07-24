<?php

namespace Thunder\Robo\Task\Site;

trait loadTasks {

  /**
   * Initialize site.
   *
   * @param string $environment
   *   An environment string.
   *
   * @return Initialize
   */
  protected function taskSiteInitialize($environment) {
    return $this->task(Initialize::class, $environment);
  }

  /**
   * Install site.
   *
   * @param string $environment
   *   An environment string.
   *
   * @return Install
   */
  protected function taskSiteInstall($environment) {
    return $this->task(Install::class, $environment);
  }

  /**
   * Enable/disable maintenance mode.
   *
   * @param bool $status
   *   Whether to enable or disable maintenance mode.
   *
   * @return MaintenanceMode
   */
  protected function taskSiteMaintenanceMode($status) {
    return $this->task(MaintenanceMode::class, $status);
  }

  /**
   * Set up file system.
   *
   * @param string $environment
   *   An environment string.
   *
   * @return SetupFileSystem
   */
  protected function taskSiteSetupFileSystem($environment) {
    return $this->task(SetupFileSystem::class, $environment);
  }

  /**
   * Update site.
   *
   * @param string $environment
   *   An environment string.
   *
   * @return Update
   */
  protected function taskSiteUpdate($environment) {
    return $this->task(Update::class, $environment);
  }

}
