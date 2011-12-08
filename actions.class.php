<?php
/**
 * @package General
 * @subpackage Extensibility
 */

require_once 'base.class.php';

/**
 * Runs actions
 *
 * @package General
 * @subpackage Extensibility
 */
class Actions extends Extensibility
{
  /**
   * @param string $tag
   * @return void
   */
  final static public function apply($tag)
  {
    self::startApply($tag);

    $result = NULL;

    if (isset(self::$items[$tag]))
    {
      self::prioritize($tag);
      foreach(self::$items[$tag] as $priority)
      {
        foreach($priority as $item)
        {
          if (is_callable($item['function']))
          {
            $arguments  = func_get_args();
            $arguments  = array_slice($arguments, 1);

            if ($item['acceptedArguments'] == 0)
            {
              $arguments  = NULL;
            }
            else if ($item['acceptedArguments'] < count($arguments))
            {
              $arguments  = array_slice($arguments, 0, $item['acceptedArguments']);
            }

            $result = self::execute($item['function'], $arguments);

            if (self::isStopRequested())
            {
              return self::endApply();
            }
          }
        }
      }
    }

    return self::endApply();
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
   * @param string $string
   * @return void
   */
  final static private function endApply()
  {
    return self::doStop();
  }
}
?>