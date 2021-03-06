<?php

namespace Thunder\Robo\Task\DatabaseDump;

use Thunder\Robo\Utility\Drush;

/**
 * Robo task: Export database dump.
 */
class Export extends Dump {

  /**
   * {@inheritdoc}
   */
  public function run() {
    return Drush::exec()
      ->arg('sql-dump')
      ->arg('>')
      ->arg(escapeshellarg($this->filepath))
      ->option('ordered-dump')
      ->option('extra=--skip-comments')
      ->option('structure-tables-list=' . escapeshellarg(implode(',', $this->getStructureOnlyTableList())))
      ->run();
  }

  /**
   * Return structure-only database table names.
   *
   * This is used to make the exported file as small as possible. All returned
   * database table names indicate to only export their structure but not data
   * rows.
   *
   * @return array
   *   An array of database table names.
   */
  protected function getStructureOnlyTableList() {
    $tables = [
      'cache_*',
      'cachetags',
      'watchdog',
      'sessions',
    ];

    return $tables;
  }

}
