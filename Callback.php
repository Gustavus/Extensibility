<?php
/**
 * @package Extensibility
 */

namespace Gustavus\Extensibility;

/**
 * Callbacks used by Filters and Actions
 *
 * @package Extensibility
 */
class Callback
{
  /**
   * @var mixed callback
   */
  private $function;

  /**
   * @var integer Number of parameters the callback function takes
   */
  private $numberOfParameters;

  /**
   * @var object \ReflectionMethod or \ReflectionFunction
   */
  private $reflection;

  /**
   * This function should only be constructed by the Callback factory. If you want to use this object, use CallbackFactory::getCallback(...) instead.
   *
   * @param mixed $function callback
   * @param integer $numberOfParameters
   * @return void
   */
  public function __construct($function, $numberOfParameters = null)
  {
    $this->function           = $function;
    $this->numberOfParameters = $numberOfParameters;
  }

  /**
   * @return void
   */
  public function __destruct()
  {
    unset($this->function);
    unset($this->numberOfParameters);
    unset($this->reflection);
  }

  /**
   * @return object \ReflectionMethod or \ReflectionFunction
   */
  private function getReflection()
  {
    if ($this->reflection === null) {
      if (is_array($this->function)) {
        $this->reflection = new \ReflectionMethod($this->function[0], $this->function[1]);
      } else if (is_string($this->function)) {
        $this->reflection = new \ReflectionFunction($this->function);
      }
    }

    return $this->reflection;
  }

  /**
   * @return integer
   */
  public function getNumberOfParameters()
  {
    if ($this->numberOfParameters === null) {
      $this->numberOfParameters = $this->getReflection()->getNumberOfParameters();
    }

    return $this->numberOfParameters;
  }

  /**
   * @return boolean
   */
  public function isCallable()
  {
    return is_callable($this->function);
  }

  /**
   * @param array $arguments
   * @return mixed
   */
  public function execute(array $arguments = array())
  {
    if (is_array($this->function)) {
      return $this->getReflection()->invokeArgs($this->function[0], $arguments);
    } else {
      return $this->getReflection()->invokeArgs($arguments);
    }
  }
}
