<?php
// An ad hoc autoloader for Travis CI

class ClassAutoloader
{
  public function __construct()
  {
    spl_autoload_register(array($this, 'loader'));
  }

  public function loader($className)
  {
    $className = explode('\\', $className);

    $vendor    = array_shift($className);
    $project   = array_shift($className);
    $className = implode('/', $className);

    if (file_exists($path = __DIR__ . "/$className")) {
      // Part of project
      require_once $path;
    } else if (file_exists($path = __DIR__ . "/vendor/$vendor/$project/$className")) {
      // Part of a dependency
      require_once $path;
    }
  }
}

$autoloader = new ClassAutoloader();