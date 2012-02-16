<?php
/**
 * @package Extensibility
 * @subpackage Test
 */

namespace Gustavus\Extensibility\Test;

use Gustavus\Extensibility\Callback;

require_once 'Gustavus/Test/Test.php';
require_once 'Gustavus/Extensibility/Callback.php';

/**
 * @package Extensibility
 * @subpackage Test
 */
class CallbackTest extends \Gustavus\Test\Test
{
  /**
   * @return string
   */
  public function aCallbackMethod()
  {
    return 'callback!';
  }

  /**
   * @return void
   */
  private function privateMethod()
  {
    return;
  }

  /**
   * For coverage report
   * @test
   */
  public function testPrivateMethod()
  {
    $this->assertNULL($this->privateMethod());
  }

  /**
   * @test
   */
  public function execute()
  {
    $callback = new Callback('is_int');
    $this->assertTrue($callback->execute(array(1)));
    $this->assertTrue($callback->execute(array(100)));
    $this->assertFalse($callback->execute(array('100')));

    $callback = new Callback(array($this, 'aCallbackMethod'));
    $this->assertSame('callback!', $callback->execute());
  }

  /**
   * @test
   */
  public function getNumberOfParameters()
  {
    $callback = new Callback('is_int');
    $this->assertSame(1, $callback->getNumberOfParameters());

    $callback = new Callback('mktime');
    $this->assertSame(6, $callback->getNumberOfParameters());

    $callback = new Callback(array($this, __FUNCTION__));
    $this->assertSame(0, $callback->getNumberOfParameters());
  }

  /**
   * @test
   */
  public function isCallable()
  {
    $callback = new Callback('is_int');
    $this->assertTrue($callback->isCallable());

    $callback = new Callback(array($this, __FUNCTION__));
    $this->assertTrue($callback->isCallable());

    $callback = new Callback(array($this, 'privateMethod'));
    $this->assertFalse($callback->isCallable());
  }
}
