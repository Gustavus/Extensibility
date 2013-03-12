<?php
/**
 * @package Extensibility
 * @subpackage Test
 */

namespace Gustavus\Extensibility\Test;

use Gustavus\Extensibility\Filters;

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
    $ftest = new FilterTestClass();

    Filters::add('TestTag', array($this, 'noArgumentsCallbackFilter'));

    $this->assertSame('test content', Filters::apply('TestTag', 'test content', 'test', 'test 2'));
    $this->assertSame(1, $this->called);
    $this->assertSame(null, $this->testingVar);

    Filters::add('TestTag', 'Gustavus\\Extensibility\\Test\\filterContent');
    Filters::add('TestTag', [$ftest, 'filter']);
    Filters::add('TestTag', ['Gustavus\\Extensibility\\Test\\FilterTestClass', 'staticFilter']);
    Filters::add('TestTag', function($content) { return $content . '4'; });

    Filters::add('TestTag', array($this, 'oneArgumentCallbackFilter'));
    Filters::add('TestTag', array($this, 'stopRequestedCallbackFilter'));
    Filters::add('TestTag', array($this, 'afterStopRequestedCallbackFilter'));

    $this->assertSame('Test content1234', Filters::apply('TestTag', 'test content', 'test', 'test 2'));
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


/**
 * Test function for the apply test
 */
function filterContent($content)
{
  return $content . '1';
}

/**
 * Test class for the apply test
 */
class FilterTestClass
{
  public function filter($content)
  {
    return $content . '2';
  }

  public static function staticFilter($content)
  {
    return $content . '3';
  }
}
