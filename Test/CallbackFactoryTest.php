<?php
/**
 * @package Extensibility
 * @subpackage Test
 */

namespace Gustavus\Extensibility\Test;

use Gustavus\Extensibility\CallbackFactory;

require_once 'Gustavus/Test/Test.php';
require_once 'Gustavus/Extensibility/CallbackFactory.php';

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
    $this->set('\Gustavus\Extensibility\CallbackFactory', 'objectCallbackCache', null);
    $this->set('\Gustavus\Extensibility\CallbackFactory', 'nonObjectCallbackCache', null);
  }

  /**
   * @test
   */
  public function isCallbackInObject()
  {
    $this->assertFalse($this->call('\Gustavus\Extensibility\CallbackFactory', 'isCallbackInObject', array('is_int')));

    $this->assertFalse($this->call('\Gustavus\Extensibility\CallbackFactory', 'isCallbackInObject', array(array('is_int'))));

    $this->assertFalse($this->call('\Gustavus\Extensibility\CallbackFactory', 'isCallbackInObject', array(array('\Gustavus\Extensibility\CallbackFactory', 'getCallback'))));

    $foo = new Foo();
    $this->assertTrue($this->call('\Gustavus\Extensibility\CallbackFactory', 'isCallbackInObject', array(array($foo, 'foo'))));
  }

  /**
   * @test
   */
  public function getCacheKeyFromSimpleFunctions()
  {
    $keyA = $this->call('\Gustavus\Extensibility\CallbackFactory', 'getCacheKey', array('is_int'));

    $keyB = $this->call('\Gustavus\Extensibility\CallbackFactory', 'getCacheKey', array('is_string'));

    $keyC = $this->call('\Gustavus\Extensibility\CallbackFactory', 'getCacheKey', array('is_int'));

    $this->assertSame($keyA, $keyC);
    $this->assertNotSame($keyA, $keyB);
    $this->assertInternalType('string', $keyA);
    $this->assertInternalType('string', $keyB);
    $this->assertInternalType('string', $keyC);
  }

  /**
   * @test
   */
  public function getCacheKeyFromDifferentInstancesOfSameClass()
  {
    $fooA = new Foo();
    $fooB = new Foo();

    $keyA = $this->call('\Gustavus\Extensibility\CallbackFactory', 'getCacheKey', array(array($fooA, 'foo')));

    $keyB = $this->call('\Gustavus\Extensibility\CallbackFactory', 'getCacheKey', array(array($fooB, 'foo')));

    $this->assertSame($keyA, $keyB);
    $this->assertInternalType('string', $keyA);
    $this->assertInternalType('string', $keyB);
  }

  /**
   * @test
   */
  public function getCacheKeyFromDifferentClassesWithSameFunctionNames()
  {
    $foo = new Foo();
    $bar = new Bar();

    $keyA = $this->call('\Gustavus\Extensibility\CallbackFactory', 'getCacheKey', array(array($foo, 'foo')));

    $keyB = $this->call('\Gustavus\Extensibility\CallbackFactory', 'getCacheKey', array(array($bar, 'foo')));

    $this->assertSame($keyA, $keyB);
    $this->assertInternalType('string', $keyA);
    $this->assertInternalType('string', $keyB);
  }

  /**
   * @test
   */
  public function getCallbackWithSimpleFunctions()
  {
    $callbackA = CallbackFactory::getCallback('is_int');
    $callbackB = CallbackFactory::getCallback('is_string');
    $callbackC = CallbackFactory::getCallback('is_int');

    $this->assertSame($callbackA, $callbackC);
    $this->assertNotSame($callbackA, $callbackB);
    $this->assertInstanceOf('\Gustavus\Extensibility\Callback', $callbackA);
    $this->assertInstanceOf('\Gustavus\Extensibility\Callback', $callbackB);
    $this->assertInstanceOf('\Gustavus\Extensibility\Callback', $callbackC);
  }

  /**
   * @test
   */
  public function getCallbackWithFunctionsInClasses()
  {
    $fooA   = new Foo();
    $fooB   = new Foo();

    $callbackA = CallbackFactory::getCallback(array($fooA, 'foo'));
    $callbackB = CallbackFactory::getCallback(array($fooB, 'foo'));
    $callbackC = CallbackFactory::getCallback(array($fooA, 'foo'));

    $this->assertSame($callbackA, $callbackC);
    $this->assertNotSame($callbackA, $callbackB);
    $this->assertInstanceOf('\Gustavus\Extensibility\Callback', $callbackA);
    $this->assertInstanceOf('\Gustavus\Extensibility\Callback', $callbackB);
    $this->assertInstanceOf('\Gustavus\Extensibility\Callback', $callbackC);
  }

  /**
   * @test
   */
  public function classesForTests()
  {
    $foo = new Foo();
    $this->assertNULL($foo->foo());

    $bar = new Bar();
    $this->assertNULL($bar->foo());
  }
}

class Foo
{
  public function foo()
  {
    return;
  }
}

class Bar
{
  public function foo()
  {
    return;
  }
}
