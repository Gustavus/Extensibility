<?php
/**
 * @package Extensibility
 */

namespace Gustavus\Extensibility;
require_once __DIR__ . '/Callback.php';

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
  private static $callbackCache;

  /**
   * @param mixed $function callback
   * @param integer $numberOfParameters
   * @return string
   */
  private static function getCacheKey($function, $numberOfParameters = null)
  {
    return hash('md4', json_encode(array($function, $numberOfParameters)));
  }

  /**
   * @param mixed $function callback
   * @param integer $numberOfParameters
   * @return Callback
   */
  public static function getCallback($function, $numberOfParameters = null)
  {
    $key = self::getCacheKey($function, $numberOfParameters);
    if (!isset(self::$callbackCache[$key])) {
      self::$callbackCache[$key] = new Callback($function, $numberOfParameters);
    }

    return self::$callbackCache[$key];
  }
}
