<?php
/**
 * CallbackFactory.php
 *
 * @package Extensibility
 *
 * @author Joe Lencioni
 * @author Chris Rog
 */
namespace Gustavus\Extensibility;

use \InvalidArgumentException;



/**
 * Callback factory
 *
 * Example:
 * <code>
 * $callback = CallbackFactory::getCallback('myFunction');
 * </code>
 *
 * @package Extensibility
 *
 * @author Joe Lencioni
 * @author Chris Rog
 */
abstract class CallbackFactory
{
  /**
   * Contains an array of arrays, keyed by the callback's hashcode. Each value in the sub array
   * contains an associative array containing the original key and the value:
   *
   * cache[hashcode] > [ [key=>input, value=>Callback], [key=>input, value=>Callback], ... ]
   *
   * @var array
   */
  private static $cache;

  /**
   * Generates a hashcode for the callback.
   *
   * @param callable $callback
   *  The callback for which to generate the hashcode.
   *
   * @param integer $paramCount
   *  The number of parameters the callback is expecting.
   *
   * @return string
   *  The hashcode of the callback.
   */
  private static function getHashCode(callable $callback, $paramCount)
  {
    $data = ['params' => $paramCount];

    if (is_array($callback)) {
      $data['base'] = is_object($callback[0]) ? spl_object_hash($callback[0]) : $callback[0];
      $data['offset'] = $callback[1];
    } else if (is_object($callback)) {
      $data['base'] = spl_object_hash($callback);
    } else {
      $data['base'] = $callback;
    }

    return hash('md4', json_encode($data));
  }


  /**
   * Creates a Callback wrapper for the given callback.
   *
   * @param callable $callback
   *  The callback to wrap.
   *
   * @param integer $paramCount
   *  Optional. The number of parameters the callback is expecting. Must be a non-negative integer.
   *
   * @throws InvalidArgumentException
   *  if $paramCount is given as a non-integer value, or a negative value.
   *
   * @return Callback
   *  A Callback instance wrapping the specified callback.
   */
  public static function getCallback(callable $callback, $paramCount = null)
  {
    if (isset($paramCount) && (!is_int($paramCount) || $paramCount < 0)) {
      throw new InvalidArgumentException('$paramCount is not a valid integer value.');
    }

    $hashcode = self::getHashCode($callback, $paramCount);
    $wrapper = null;

    if (!isset(self::$cache)) {
      self::$cache = [];
    }

    if (!isset(self::$cache[$hashcode])) {
      self::$cache[$hashcode] = [];
    }

    foreach (self::$cache[$hashcode] as $kvpair) {
      if ($kvpair['key'] === $callback) {
        $wrapper = $kvpair['value'];
        break;
      }
    }

    if (!isset($wrapper)) {
      $wrapper = new Callback($callback, $paramCount);
      array_push(self::$cache[$hashcode], ['key' => $callback, 'value' => $wrapper]);
    }

    return $wrapper;
  }
}
