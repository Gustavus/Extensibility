<?php
/**
 * @package Extensibility
 * @subpackage Test
 */

namespace Gustavus\Extensibility\Test;

use Gustavus\Extensibility\Actions;

require_once __DIR__ . '/Base.php';
require_once 'Gustavus/Extensibility/Actions.php';

/**
 * @package Extensibility
 * @subpackage Test
 */
class ActionsTest extends Base
{
  private $called = 0;

  private $testingVar = null;

  /**
   * @return void
   */
  public function setUp()
  {
    $this->called = 0;
    $this->testingVar = null;

    parent::setUp();
  }

  /**
   * @test
   */
  public function apply()
  {
    Actions::add('TestTag', array($this, 'noArgumentsCallback'));

    Actions::apply('TestTag', 'test', 'test2');
    $this->assertSame(1, $this->called);
    $this->assertSame(null, $this->testingVar);

    Actions::add('TestTag', array($this, 'oneArgumentCallback'));
    Actions::add('TestTag', array($this, 'stopRequestedCallback'));
    Actions::add('TestTag', array($this, 'afterStopRequestedCallback'));

    Actions::apply('TestTag', 'test', 'test2');
    $this->assertSame(4, $this->called);
    $this->assertSame('test', $this->testingVar);
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
    Actions::stop();
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

  /**
   * @test
   */
  public function endApply()
  {
    $this->set('\Gustavus\Extensibility\Base', 'stop', true);

    $this->call('\Gustavus\Extensibility\Actions', 'endApply');

    $this->assertFalse($this->get('\Gustavus\Extensibility\Base', 'stop'));
    $this->assertFalse($this->call('\Gustavus\Extensibility\Base', 'isStopRequested'));
  }
}
