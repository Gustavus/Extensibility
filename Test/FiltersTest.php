<?php
/**
 * @package Extensibility
 * @subpackage Test
 */

namespace Gustavus\Extensibility\Test;

use Gustavus\Extensibility\Filters;

require_once __DIR__ . '/Base.php';
require_once 'Gustavus/Extensibility/Filters.php';

/**
 * @package Extensibility
 * @subpackage Test
 */
class FiltersTest extends Base
{
  /**
   * @test
   */
  public function apply()
  {
    Filters::add('TestTag', array($this, 'noArgumentsCallbackFilter'));

    $this->assertSame('test content', Filters::apply('TestTag', 'test content', 'test', 'test 2'));
    $this->assertSame(1, $this->called);
    $this->assertSame(null, $this->testingVar);

    Filters::add('TestTag', array($this, 'oneArgumentCallbackFilter'));
    Filters::add('TestTag', array($this, 'stopRequestedCallbackFilter'));
    Filters::add('TestTag', array($this, 'afterStopRequestedCallbackFilter'));

    $this->assertSame('Test content', Filters::apply('TestTag', 'test content', 'test', 'test 2'));
    $this->assertSame(4, $this->called);
    $this->assertSame('test', $this->testingVar);
  }

  /**
   * @test
   */
  public function applyCallbackWithNoArguments()
  {
    Filters::add('TestTag', array($this, 'noArgumentsCallback'));

    $this->assertNULL(Filters::apply('TestTag', 'test content', 'test', 'test 2'));
    $this->assertSame(1, $this->called);
    $this->assertSame(null, $this->testingVar);
  }

  /**
   * Callback for the apply test
   */
  public function noArgumentsCallbackFilter($content)
  {
    $this->noArgumentsCallback();
    return $content;
  }

  /**
   * Callback for the apply test
   */
  public function oneArgumentCallbackFilter($content, $var)
  {
    $this->oneArgumentCallback($var);
    return $content;
  }

  /**
   * Callback for the apply test
   */
  public function stopRequestedCallbackFilter($content)
  {
    $content = ucfirst($content);
    $this->stopRequestedCallback();
    return $content;
  }

  /**
   * Callback for the apply test
   */
  public function afterStopRequestedCallbackFilter($content)
  {
    $content = 'SOMETHING IS WRONG';
    $this->afterStopRequestedCallback();
    return $content;
  }

  /**
   * @test
   */
  public function afterStopRequestedCallbackFilterTest()
  {
    $this->assertSame('SOMETHING IS WRONG', $this->afterStopRequestedCallbackFilter('test'));
    $this->assertSame(1, $this->called);
    $this->assertSame('STOP FAILED', $this->testingVar);
  }

  /**
   * @test
   */
  public function endApply()
  {
    $this->set('\Gustavus\Extensibility\Base', 'stop', true);

    $this->assertSame('test', $this->call('\Gustavus\Extensibility\Filters', 'endApply', array('test')));

    $this->assertFalse($this->get('\Gustavus\Extensibility\Base', 'stop'));
    $this->assertFalse($this->call('\Gustavus\Extensibility\Base', 'isStopRequested'));
  }
}
