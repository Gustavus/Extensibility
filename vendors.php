#!/usr/bin/env php
<?php
// This code was adapted from Symfony 2's vendors.php

if (!is_dir($vendorDir = dirname(__FILE__) . '/vendor')) {
  mkdir($vendorDir, 0777, true);
}

$dependencies = array(
  'Gustavus/Test' => 'http://github.com/Gustavus/Test.git',
);

foreach ($dependencies as $name => $url) {
  echo "> Installing/Updating $name\n";

  $installDir = $vendorDir.'/'.$name;
  if (!is_dir($installDir)) {
    system(sprintf('git clone %s %s', escapeshellarg($url), escapeshellarg($installDir)));
  }
}

// Set up an autoloader

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