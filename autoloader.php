<?php
// An ad hoc autoloader for Travis CI

class ClassAutoloader
{
  public function __construct()
  {
    spl_autoload_register(array($this, 'loader'));
  }

  public function loader($fullClassName)
  {
    $className = explode('\\', $fullClassName);

    $vendor    = array_shift($className);
    $project   = array_shift($className);
    $className = implode('/', $className);

    $paths = array();
    if (file_exists($paths[] = __DIR__ . "/$className.php")) {
      // Part of project
      $path = end($paths);
      echo "> Autoloading $path\n";
      require_once $path;
    } else if (file_exists($paths[] = realpath(__DIR__ . "/vendor/$vendor/$project/$className.php"))) {
      // Part of a dependency
      $path = end($paths);
      echo "> Autoloading $path\n";
      require_once $path;
    } else {
      printf("> Failed to autoload $fullClassName. Tried %s\n", implode(', ', $paths));
    }
  }
}

$autoloader = new ClassAutoloader();