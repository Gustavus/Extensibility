<?php
/**
 * @package Extensibility
 */

namespace Gustavus\Extensibility;

/**
 * Base class for filters and actions
 *
 * @package Extensibility
 */
abstract class Base
{
  /**
   *
   * @var array
   */
  protected static $items = array();

  /**
   *
   * @var string
   */
  protected static $currentTag = null;

  /**
   *
   * @var boolean
   */
  private static $stop = false;

  /**
   * Adds a callback to the given tag
   *
   * @param string $tag
   * @param callback $function
   * @param integer $priority
   * @param integer $acceptedArguments
   * @return boolean
   */
  final static public function add($tag, $function, $priority = 10, $acceptedArguments = null)
  {
    if ($acceptedArguments === null) {
      $acceptedArguments = self::getNumberOfArguments($function);
    }

    // Remove it if it exists, so it doesn't get added twice
    self::remove($tag, $function, $priority, $acceptedArguments);

    self::$items[$tag][$priority][] = array(
      'function'          => $function,
      'acceptedArguments' => $acceptedArguments,
    );
  }

  /**
   * Gets the number of arguments the given function can accept (arity).
   *
   * @param callback $function
   * @return integer
   */
  final static private function getNumberOfArguments($function)
  {
    if (is_array($function)) {
      $method = new \ReflectionMethod($function[0], $function[1]);
    } else if (is_string($function)) {
      $method = new \ReflectionFunction($function);
    }

    return $method->getNumberOfParameters();
  }

  /**
   * @param string $tag
   * @param callback $function
   * @param integer $priority
   * @param integer $acceptedArguments
   * @return boolean
   */
  final static public function remove($tag, $function, $priority = 10, $acceptedArguments = null)
  {
    if ($acceptedArguments === null) {
      $acceptedArguments = self::getNumberOfArguments($function);
    }

    if (isset(self::$items[$tag][$priority])) {
      foreach (self::$items[$tag][$priority] as $key => $item) {
        if ($item == array('function' => $function, 'acceptedArguments' => $acceptedArguments)) {
          unset(self::$items[$tag][$priority][$key]);
          return true;
        }
      }
    }

    return false;
  }

  /**
   * Stops the rest of the filters and actions in the current tag from being run
   *
   * @param mixed $return Value to return
   * @return void
   */
  final static public function stop($return = null)
  {
    self::$stop = true;
  }

  /**
   * @return boolean
   */
  final static protected function isStopRequested()
  {
    if (self::$stop === true) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * @return void
   */
  final static protected function doStop()
  {
    self::$stop       = false;
    self::$currentTag = null;
  }

  /**
   * @param string $tag
   * @return void
   */
  final static protected function prioritize($tag)
  {
    ksort(self::$items[$tag]);
  }

  /**
   * @param mixed $callback
   * @param array $arguments
   */
  final static protected function execute($callback, array $arguments)
  {
    return call_user_func_array($callback, $arguments);

    // Using the reflection class seems to be slower than using call_user_func_array()
    if (is_array($callback)) {
      // Function is in a class
      $ref    = new \ReflectionClass(get_class($callback[0]));
      $method   = $ref->getMethod($callback[1]);
      return $method->invokeArgs($callback[0], $arguments);
    } else {
      // Function is not in a class
      $method   = new \ReflectionFunction($callback);
      return $method->invokeArgs($arguments);
    }
  }
}
