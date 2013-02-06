<?php
/**
 * @package Extensibility
 * @subpackage Test
 */

namespace Gustavus\Extensibility\Test;

use Gustavus\Extensibility\CallbackFactory;

/**
 * @package Extensibility
 * @subpackage Test
 */
class CallbackFactoryTest extends \Gustavus\Test\Test
{
  /**
   * @return void
   */
  public function setUp()
  {
    $this->set('\Gustavus\Extensibility\CallbackFactory', 'cache', null);
  }

////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * @test
   * @dataProvider dataForGetCallback
   */
  public function testGetCallback($callback, $paramCount, $success)
  {
    try {
      $callback = CallbackFactory::getCallback($callback, $paramCount);

      $this->assertInstanceOf('\\Gustavus\\Extensibility\\Callback', $callback);


      if (!$success) {
        $this->fail('An expected exception was not thrown.');
      }
    } catch (\Exception $e) {
      if ($success) {
        throw $e;
      }
    }
  }

  public function dataForGetCallback()
  {
    $foo = new Foo();

    return [
      ['is_string', 1, true],
      [[$foo, 'foo'], 0, true],
      [['\\Gustavus\\Extensibility\\Test\\Foo', 'bar'], null, true],
      ['\\Gustavus\\Extensibility\\Test\\Foo::bar', null, true],
      [function($party, $hard) { return; }, 2, true],

      ['is_string', -1, false],
      ['is_string', 'one', false],
      ['is_string', 1.23, false],
      ['is_string', true, false],
      ['is_string', [1], false],
      ['is_string', $foo, false]
    ];
  }

////////////////////////////////////////////////////////////////////////////////////////////////////

  /**
   * @test
   */
  public function testCallbackCaching()
  {
    $callbackA = CallbackFactory::getCallback('is_int');
    $callbackB = CallbackFactory::getCallback('is_string');
    $callbackC = CallbackFactory::getCallback('is_int');

    $this->assertSame($callbackA, $callbackC);
    $this->assertNotSame($callbackA, $callbackB);
    $this->assertInstanceOf('\Gustavus\Extensibility\Callback', $callbackA);
    $this->assertInstanceOf('\Gustavus\Extensibility\Callback', $callbackB);
    $this->assertInstanceOf('\Gustavus\Extensibility\Callback', $callbackC);



    $fooA = new Foo();
    $fooB = new Foo();

    $callbackA = CallbackFactory::getCallback(array($fooA, 'foo'));
    $callbackB = CallbackFactory::getCallback(array($fooB, 'foo'));
    $callbackC = CallbackFactory::getCallback(array($fooA, 'foo'));

    $this->assertSame($callbackA, $callbackC);
    $this->assertNotSame($callbackA, $callbackB);
    $this->assertInstanceOf('\Gustavus\Extensibility\Callback', $callbackA);
    $this->assertInstanceOf('\Gustavus\Extensibility\Callback', $callbackB);
    $this->assertInstanceOf('\Gustavus\Extensibility\Callback', $callbackC);
  }
}

////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////

class Foo
{
  public function foo() {
    return;
  }

  public static function bar() {
    return;
  }
}
