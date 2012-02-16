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
    $this->set('\Gustavus\Extensibility\CallbackFactory', 'callbackCache', null);
  }

  /**
   * @test
   */
  public function getCacheKey()
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
  public function getCallback()
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
}