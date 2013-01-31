<?php
/**
 * @package Extensibility
 * @subpackage Test
 */

namespace Gustavus\Extensibility\Test;

use Gustavus\Extensibility\Callback;

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
   * @param  string $content
   * @return string
   */
  public static function staticCallbackFunction($content)
  {
    return $content . 'arst';
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

    $callback = new Callback(array('Gustavus\Extensibility\Test\CallbackTest', 'staticCallbackFunction'));
    $this->assertSame('tsraarst', $callback->execute(array('tsra')));
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

    $callback = new Callback(array('Gustavus\Extensibility\Test\CallbackTest', 'staticCallbackFunction'));
    $this->assertTrue($callback->isCallable());
  }
}
