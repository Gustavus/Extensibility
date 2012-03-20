<?php
/**
 * @package Extensibility
 */

namespace Gustavus\Extensibility;

/**
 * Callbacks factory
 *
 * @package Extensibility
 */
abstract class CallbackFactory
{
  /**
   * @var array
   */
  private static $nonObjectCallbackCache;

  /**
   * @var \SplObjectStorage
   */
  private static $objectCallbackCache;

  /**
   * @param mixed $function callback
   * @return boolean
   */
  private static function isCallbackInObject($function)
  {
    return (is_array($function) && is_object($function[0]));
  }

  /**
   * @param mixed $function callback
   * @param integer $numberOfParameters
   * @return string
   */
  private static function getCacheKey($function, $numberOfParameters = null)
  {
    if (self::isCallbackInObject($function)) {
      $function = $function[1];
    }

    return hash('md4', json_encode(array($function, $numberOfParameters)));
  }

  /**
   * @param mixed $function callback
   * @param integer $numberOfParameters
   * @return Callback
   */
  public static function getCallback($function, $numberOfParameters = null)
  {
    if (self::isCallbackInObject($function)) {
      // Initialize SplObjectStorage
      if (self::$objectCallbackCache === null) {
        self::$objectCallbackCache = new \SplObjectStorage();
      }

      $object = $function[0];
      if (!isset(self::$objectCallbackCache[$object])) {
        self::$objectCallbackCache[$object] = array();
      }

      $cache = self::$objectCallbackCache[$object];
    } else {
      $cache = &self::$nonObjectCallbackCache;
    }

    $key   = self::getCacheKey($function, $numberOfParameters);

    if (!isset($cache[$key])) {
      $cache[$key] = new Callback($function, $numberOfParameters);

      if (self::isCallbackInObject($function)) {
        // We need to do it this way because SplObjectStorage does not use indirect modification. More info: http://stackoverflow.com/questions/9380430/using-splobjectstorage-as-a-data-map-can-you-use-a-mutable-array-as-the-data
        self::$objectCallbackCache[$object] = $cache;
      }
    }

    return $cache[$key];
  }
}