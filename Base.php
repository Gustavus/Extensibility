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
   * Callbacks organized by tag > priority > order added
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
   * Attaches a callback function to the given tag with an optional priority.
   *
   * Example:
   * <code>
   * Filters::add('myTag', 'myFunction');
   * </code>
   *
   * @param string $tag
   * @param callback $function
   * @param integer $priority
   * @param integer $numberOfParameters
   * @return boolean
   */
  final static public function add($tag, $function, $priority = 10, $numberOfParameters = null)
  {
    if (!isset(self::$items[$tag][$priority])) {
      self::$items[$tag][$priority] = new \SplObjectStorage();
    }

    $callback = CallbackFactory::getCallback($function, $numberOfParameters);

    if (!self::$items[$tag][$priority]->contains($callback)) {
      self::$items[$tag][$priority]->attach($callback);
    }

    return true;
  }

  /**
   * Removes a callback function from the specified tag.
   *
   * Example:
   * <code>
   * Filters::remove('myTag', 'myFunction');
   * </code>
   *
   * @param string $tag
   * @param callback $function
   * @param integer $priority
   * @param integer $numberOfParameters
   * @return boolean
   */
  final static public function remove($tag, $function, $priority = 10, $numberOfParameters = null)
  {
    if (isset(self::$items[$tag][$priority])) {
      $callback = CallbackFactory::getCallback($function, $numberOfParameters);

      if (self::$items[$tag][$priority]->contains($callback)) {
        self::$items[$tag][$priority]->detach($callback);
        return true;
      }
    }

    return false;
  }

  /**
   * Gets the iterator for the specified tag.
   *
   * @param string $tag
   * @return \RecursiveIteratorIterator
   */
  final static protected function getIterator($tag)
  {
    if (isset(self::$items[$tag])) {
      self::prioritize($tag);

      return new \RecursiveIteratorIterator(
          new \RecursiveArrayIterator(self::$items[$tag]),
          \RecursiveIteratorIterator::SELF_FIRST,
          \RecursiveIteratorIterator::CATCH_GET_CHILD
      );
    }
  }

  /**
   * Prevents further filters or actions in the current tag from running.
   *
   * @return void
   */
  final static public function stop()
  {
    self::$stop = true;
  }

  /**
   * Determines of a callback function has requested filters or actions to stop.
   *
   * @return boolean
   */
  final static protected function isStopRequested()
  {
    return self::$stop;
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
   * Sorts the callbacks for the given tag by priority.
   *
   * @param string $tag
   * @return void
   */
  final static protected function prioritize($tag)
  {
    ksort(self::$items[$tag]);
  }

  /**
   * @param string $tag
   * @return void
   */
  final static protected function startApply($tag)
  {
    self::$currentTag = $tag;
  }

  /**
   * Clears the specified tag of callbacks
   *
   * @param  string $tag Tag to clear
   * @return void
   */
  final static public function clear($tag)
  {
    unset(self::$items[$tag]);
  }
}
