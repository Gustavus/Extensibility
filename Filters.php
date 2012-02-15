<?php
/**
 * @package General
 * @subpackage Extensibility
 */

namespace Gustavus\Extensibility;
require_once __DIR__ . '/Base.php';

/**
 * Runs filters
 *
 * @package General
 * @subpackage Extensibility
 */
class Filters extends Base
{
  /**
   * @param string $tag
   * @param mixed $content
   * @return mixed
   */
  final static public function apply($tag, $content)
  {
    self::startApply($tag);

    if (isset(self::$items[$tag])) {
      self::prioritize($tag);
      foreach (self::$items[$tag] as $priority) {
        foreach ($priority as $item) {
          if (is_callable($item['function'])) {
            $arguments  = func_get_args();
            $arguments  = array_merge(array($content), array_slice($arguments, 2));

            if ($item['acceptedArguments'] == 0) {
              $arguments  = null;
            } else if ($item['acceptedArguments'] < count($arguments)) {
              $arguments  = array_slice($arguments, 0, $item['acceptedArguments']);
            }

            $content  = self::execute($item['function'], $arguments);

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
   * @param string $tag
   * @return void
   */
  final static private function startApply($tag)
  {
    self::$currentTag = $tag;
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
