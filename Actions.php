<?php
/**
 * @package Extensibility
 */

namespace Gustavus\Extensibility;

/**
 * Runs actions.
 *
 * @package Extensibility
 */
class Actions extends Base
{
  /**
   * Calls all of the callback functions added to the given tag.
   *
   * @param string $tag
   * @return void
   */
  final static public function apply($tag)
  {
    self::startApply($tag);

    if ($iterator = self::getIterator($tag)) {
      foreach ($iterator as $callbacks) {
        foreach ($callbacks as $callback) {
          if ($callback->isCallable()) {
            $arguments  = func_get_args();
            $arguments  = array_slice($arguments, 1);

            if ($callback->getNumberOfParameters() === 0) {
              $arguments  = array();
            } else if ($callback->getNumberOfParameters() < count($arguments)) {
              $arguments  = array_slice($arguments, 0, $callback->getNumberOfParameters());
            }

            $callback->execute($arguments);

            if (self::isStopRequested()) {
              return self::endApply();
            }
          }
        }
      }
    }

    return self::endApply();
  }

  /**
   * Called when actions are done being applied, whether they were stopped or completed naturally.
   *
   * @param string $string
   * @return void
   */
  final static private function endApply()
  {
    return self::doStop();
  }
}
