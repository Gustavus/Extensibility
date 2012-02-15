<?php
/**
 * @package Extensibility
 * @subpackage Test
 */

namespace Gustavus\Extensibility\Test;

use Gustavus\Extensibility\Base;

require_once 'Gustavus/Test/Test.php';
require_once 'Gustavus/Extensibility/Base.php';

/**
 * @package Extensibility
 * @subpackage Test
 */
class BaseTest extends \Gustavus\Test\Test
{

  /**
   * @return void
   */
  public function setUp()
  {
    $this->set('\Gustavus\Extensibility\Base', 'items', array());
    $this->set('\Gustavus\Extensibility\Base', 'currentTag', null);
    $this->set('\Gustavus\Extensibility\Base', 'stop', false);
  }

  /**
   * @test
   */
  public function addAndRemove()
  {
    Base::add('TestTag', 'is_int');

    $expected = array(
      'TestTag' => array(
        10  => array(
          0 => array(
            'function'          => 'is_int',
            'acceptedArguments' => 1,
          ),
        ),
      ),
    );

    $this->assertSame($expected, $this->get('\Gustavus\Extensibility\Base', 'items'));

    Base::add('TestTag', 'is_int');

    $expected = array(
      'TestTag' => array(
        10  => array(
          1 => array(
            'function'          => 'is_int',
            'acceptedArguments' => 1,
          ),
        ),
      ),
    );

    $this->assertSame($expected, $this->get('\Gustavus\Extensibility\Base', 'items'));

    Base::add('TestTag', 'is_string');

    $expected = array(
      'TestTag' => array(
        10  => array(
          1 => array(
            'function'          => 'is_int',
            'acceptedArguments' => 1,
          ),
          2 => array(
            'function'          => 'is_string',
            'acceptedArguments' => 1,
          ),
        ),
      ),
    );

    $this->assertSame($expected, $this->get('\Gustavus\Extensibility\Base', 'items'));

    Base::add('TestTag', 'is_object', 100);

    $expected = array(
      'TestTag' => array(
        10  => array(
          1 => array(
            'function'          => 'is_int',
            'acceptedArguments' => 1,
          ),
          2 => array(
            'function'          => 'is_string',
            'acceptedArguments' => 1,
          ),
        ),
        100 => array(
          array(
            'function'          => 'is_object',
            'acceptedArguments' => 1,
          ),
        ),
      ),
    );

    $this->assertSame($expected, $this->get('\Gustavus\Extensibility\Base', 'items'));

    Base::add('SecondTestTag', 'is_array');

    $expected = array(
      'TestTag' => array(
        10  => array(
          1 => array(
            'function'          => 'is_int',
            'acceptedArguments' => 1,
          ),
          2 => array(
            'function'          => 'is_string',
            'acceptedArguments' => 1,
          ),
        ),
        100 => array(
          array(
            'function'          => 'is_object',
            'acceptedArguments' => 1,
          ),
        ),
      ),
      'SecondTestTag' => array(
        10 => array(
          array(
            'function'          => 'is_array',
            'acceptedArguments' => 1,
          ),
        ),
      ),
    );

    $this->assertSame($expected, $this->get('\Gustavus\Extensibility\Base', 'items'));

    Base::remove('TestTag', 'is_int');

    $expected = array(
      'TestTag' => array(
        10  => array(
          2 => array(
            'function'          => 'is_string',
            'acceptedArguments' => 1,
          ),
        ),
        100 => array(
          array(
            'function'          => 'is_object',
            'acceptedArguments' => 1,
          ),
        ),
      ),
      'SecondTestTag' => array(
        10 => array(
          array(
            'function'          => 'is_array',
            'acceptedArguments' => 1,
          ),
        ),
      ),
    );

    $this->assertSame($expected, $this->get('\Gustavus\Extensibility\Base', 'items'));
  }

  /**
   * @test
   */
  public function getNumberOfArguments()
  {
    $this->assertSame(1, $this->call('\Gustavus\Extensibility\Base', 'getNumberOfArguments', array('is_int')));
    $this->assertSame(6, $this->call('\Gustavus\Extensibility\Base', 'getNumberOfArguments', array('mktime')));
    $this->assertSame(0, $this->call('\Gustavus\Extensibility\Base', 'getNumberOfArguments', array(array($this, 'getNumberOfArguments'))));
  }

  /**
   * @test
   */
  public function stop()
  {
    $this->assertFalse($this->get('\Gustavus\Extensibility\Base', 'stop'));
    $this->assertFalse($this->call('\Gustavus\Extensibility\Base', 'isStopRequested'));

    Base::stop();

    $this->assertTrue($this->get('\Gustavus\Extensibility\Base', 'stop'));
    $this->assertTrue($this->call('\Gustavus\Extensibility\Base', 'isStopRequested'));

    $this->call('\Gustavus\Extensibility\Base', 'doStop');

    $this->assertFalse($this->get('\Gustavus\Extensibility\Base', 'stop'));
    $this->assertFalse($this->call('\Gustavus\Extensibility\Base', 'isStopRequested'));
  }

  /**
   * @test
   */
  public function prioritize()
  {
    $items = array(
      'TestTag' => array(
        10  => array(),
        100 => array(),
        2   => array(),
        30  => array(),
        200 => array(),
      ),
      'SecondTestTag' => array(
        10 => array(),
        5  => array(),
      )
    );

    $this->set('\Gustavus\Extensibility\Base', 'items', $items);

    $this->call('\Gustavus\Extensibility\Base', 'prioritize', array('TestTag'));

    $items = array(
      'TestTag' => array(
        2   => array(),
        10  => array(),
        30  => array(),
        100 => array(),
        200 => array(),
      ),
      'SecondTestTag' => array(
        10 => array(),
        5  => array(),
      )
    );

    $this->assertSame($items, $this->get('\Gustavus\Extensibility\Base', 'items'));
  }

  /**
   * @test
   */
  public function execute()
  {
    $this->assertTrue($this->call('\Gustavus\Extensibility\Base', 'execute', array('is_int', array(1))));
    $this->assertTrue($this->call('\Gustavus\Extensibility\Base', 'execute', array('is_int', array(100))));
    $this->assertFalse($this->call('\Gustavus\Extensibility\Base', 'execute', array('is_int', array('100'))));
  }

}
