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
