<?php
/**
 * @package Extensibility
 * @subpackage Test
 */

namespace Gustavus\Extensibility\Test;

require_once 'Gustavus/Test/Test.php';
require_once 'Gustavus/Extensibility/Base.php';

/**
 * @package Extensibility
 * @subpackage Test
 */
abstract class Base extends \Gustavus\Test\Test
{
  /**
   * @var counter for the number of callbacks that have been called
   */
  protected $called = 0;

  /**
   * @var variable that can be set in callbacks
   */
  protected $testingVar = null;

  /**
   * @return void
   */
  public function setUp()
  {
    $this->set('\Gustavus\Extensibility\Base', 'items', array());
    $this->set('\Gustavus\Extensibility\Base', 'currentTag', null);
    $this->set('\Gustavus\Extensibility\Base', 'stop', false);

    $this->called     = 0;
    $this->testingVar = null;
  }

  /**
   * Callback for the apply test
   */
  public function noArgumentsCallback()
  {
    ++$this->called;
  }

  /**
   * Callback for the apply test
   */
  public function oneArgumentCallback($var)
  {
    $this->testingVar = $var;
    $this->noArgumentsCallback();
  }

  /**
   * Callback for the apply test
   */
  public function stopRequestedCallback()
  {
    \Gustavus\Extensibility\Base::stop();
    $this->noArgumentsCallback();
  }

  /**
   * Callback for the apply test
   */
  public function afterStopRequestedCallback()
  {
    $this->testingVar = 'STOP FAILED';
    $this->noArgumentsCallback();
  }
}