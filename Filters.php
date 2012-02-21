<?php
/**
 * @package Extensibility
 */

namespace Gustavus\Extensibility;
require_once __DIR__ . '/Base.php';

/**
 * Runs filters
 *
 * @package Extensibility
 */
class Filters extends Base
{
  /**
   * Calls all of the callback functions added to the given tag and sets $content to the result
   *
   * @param string $tag
   * @param mixed $content
   * @return mixed
   */
  final static public function apply($tag, $content)
  {
    self::startApply($tag);

    if ($iterator = self::getIterator($tag)) {
      foreach ($iterator as $callbacks) {
        foreach ($callbacks as $callback) {
          if ($callback->isCallable()) {
            $arguments  = func_get_args();
            $arguments  = array_merge(array($content), array_slice($arguments, 2));

            if ($callback->getNumberOfParameters() === 0) {
              $arguments  = array();
            } else if ($callback->getNumberOfParameters() < count($arguments)) {
              $arguments  = array_slice($arguments, 0, $callback->getNumberOfParameters());
            }

            $content  = $callback->execute($arguments);

            if (self::isStopRequested()) {
              return self::endApply($content);
            }
          }
        }
      }
    }

    return self::endApply($content);
  }

  /**
   * @param mixed $content
   * @return mixed
   */
  final static private function endApply($content)
  {
    self::doStop();
    return $content;
  }
}
