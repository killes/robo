<?php

namespace Thunder\Robo\Task\DatabaseDump;

trait loadTasks {

  /**
   * Export project database dump.
   *
   * @param string $filepath
   *   The file path of the database dump.
   *
   * @return Export
   */
  protected function taskDatabaseDumpExport($filepath) {
    return $this->task(Export::class, $filepath);
  }

  /**
   * Import project database dump.
   * 
   * @param string $filepath
   *   The file path of the database dump.
   *
   * @return Import
   */
  protected function taskDatabaseDumpImport($filepath) {
    return $this->task(Import::class, $filepath);
  }

}
