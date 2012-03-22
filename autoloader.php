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

    if (file_exists($path = __DIR__ . "/$className")) {
      // Part of project
      echo "> Autoloading $path\n";
      require_once $path;
    } else if (file_exists($path = __DIR__ . "/$vendor/$project/$className")) {
      // Part of a dependency
      echo "> Autoloading $path\n";
      require_once $path;
    } else {
      echo "> Failed to autoload $fullClassName\n";
    }
  }
}

$autoloader = new ClassAutoloader();