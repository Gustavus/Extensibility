<?php
/**
 * @package Gustavus\Extensibility
 *
 * @author  Joe Lencioni
 * @author  Billy Visto
 * @author  Chris Rog
 */

namespace Gustavus\Extensibility;

use \ReflectionFunction,
    \ReflectionMethod;



/**
 * Callbacks used by Filters and Actions
 *
 * @package Gustavus\Extensibility
 *
 * @author  Joe Lencioni
 * @author  Billy Visto
 * @author  Chris Rog
 */
class Callback
{
  /**
   * The callback we'll be using. This MUST be of the pseudo-type "callable," or a number of things
   * will break in pretty catastrophic ways.
   *
   * @var callable
   */
  private $callback;

  /**
   * @var integer Number of parameters the callback function takes
   */
  private $numberOfParameters;

  /**
   * @var object \ReflectionMethod or \ReflectionFunction
   */
  private $reflection;

////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * This function should only be constructed by the Callback factory. If you want to use this
   * object, use CallbackFactory::getCallback(...) instead.
   *
   * @param callable $callback callback
   * @param integer $numberOfParameters
   * @return void
   */
  public function __construct(callable $callback, $numberOfParameters = null)
  {
    $this->callback           = $callback;
    $this->numberOfParameters = $numberOfParameters;
  }

////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * @return object ReflectionMethod or ReflectionFunction
   */
  private function getReflection()
  {
    if (!isset($this->reflection)) {
      if (is_array($this->callback)) {
        $this->reflection = new ReflectionMethod($this->callback[0], $this->callback[1]);
      } else {
        $this->reflection = new ReflectionFunction($this->callback);
      }
    }

    return $this->reflection;
  }

  /**
   * Gets the number of parameters that this callback accepts.
   *
   * @return integer
   */
  public function getNumberOfParameters()
  {
    if (!isset($this->numberOfParameters)) {
      $this->numberOfParameters = $this->getReflection()->getNumberOfParameters();
    }

    return $this->numberOfParameters;
  }

  /**
   * Determines if this callback is a method.
   *
   * @return boolean
   */
  private function isMethod()
  {
    return is_array($this->callback) && is_object($this->callback[0]);
  }

  /**
   * Determines if this callback is a global, class or anonymous function.
   *
   * @return boolean
   */
  private function isFunction()
  {
    return !$this->isMethod();
  }

  /**
   * Executes this callback function.
   *
   * @param array $arguments
   * @return mixed
   */
  public function execute(array $arguments = [])
  {
    return call_user_func_array($this->callback, $arguments);
  }

////////////////////////////////////////////////////////////////////////////////////////////////////
}
