<?php
/**
 * @package Extensibility
 * @subpackage Test
 */

namespace Gustavus\Extensibility\Test;

/**
 * @package Extensibility
 * @subpackage Test
 */
class BaseTest extends Base
{

  /**
   * @param array|string $function
   * @return string
   */
  public function getFunctionName($function)
  {
    if (is_array($function)) {
      return $function[1];
    } else {
      return $function;
    }
  }

  /**
   * @test
   */
  public function testGetFunctionName()
  {
    $this->assertSame('test', $this->getFunctionName('test'));
    $this->assertSame('testTwo', $this->getFunctionName(array('arst', 'testTwo')));
  }

  /**
   * @param array $expectedStructure
   */
  private function checkItems(array $expectedStructure)
  {
    $allItems = $this->get('\Gustavus\Extensibility\Base', 'items');
    $this->assertInternalType('array', $allItems);
    $this->assertCount(count($expectedStructure), $allItems);

    $itemsIterator = new \MultipleIterator();
    $itemsIterator->attachIterator(new \ArrayIterator($allItems));
    $itemsIterator->attachIterator(new \ArrayIterator($expectedStructure));

    foreach ($itemsIterator as $items) {
      $keys = $itemsIterator->key();
      $this->assertSame($keys[1], $keys[0]);

      $prioritiesIterator = new \MultipleIterator();
      $prioritiesIterator->attachIterator(new \ArrayIterator($items[0]));
      $prioritiesIterator->attachIterator(new \ArrayIterator($items[1]));

      foreach ($prioritiesIterator as $priorities) {
        $keys = $prioritiesIterator->key();
        $this->assertSame($keys[1], $keys[0]);

        $callbacksIterator = new \MultipleIterator();
        $callbacksIterator->attachIterator($priorities[0]); // SplObjectStorage
        $callbacksIterator->attachIterator(new \ArrayIterator($priorities[1]));

        foreach ($callbacksIterator as $callbacks) {
          $function = $this->getFunctionName($this->get($callbacks[0], 'callback'));

          $keys = $callbacksIterator->key();
          $this->assertSame($keys[1], $function);

          $this->assertSame($callbacks[0]->getNumberOfParameters(), $callbacks[1]);
        }
      }
    }
  }
  /**
   * @test
   */
  public function addAndRemove()
  {
    $this->assertTrue(\Gustavus\Extensibility\Base::add('TestTag', 'is_int'));

    $expected = array(
      'TestTag' => array(
        10  => array(
          'is_int' => 1,
        ),
      ),
    );

    $this->checkItems($expected);

    \Gustavus\Extensibility\Base::add('TestTag', 'is_int');
    $this->checkItems($expected);

    \Gustavus\Extensibility\Base::add('TestTag', 'is_string');
    $expected['TestTag'][10]['is_string'] = 1;
    $this->checkItems($expected);

    \Gustavus\Extensibility\Base::add('TestTag', 'is_object', 100);
    $expected['TestTag'][100]['is_object'] = 1;
    $this->checkItems($expected);

    \Gustavus\Extensibility\Base::add('SecondTestTag', 'is_array');
    $expected['SecondTestTag'][10]['is_array'] = 1;
    $this->checkItems($expected);

    $this->assertTrue(\Gustavus\Extensibility\Base::remove('TestTag', 'is_int'));
    unset($expected['TestTag'][10]['is_int']);
    $this->checkItems($expected);

    \Gustavus\Extensibility\Base::add('TestTag', 'is_int');
    $expected['TestTag'][10]['is_int'] = 1;
    $this->checkItems($expected);
  }

  /**
   * @test
   */
  public function removeNonExistent()
  {
    $expected = array();
    $this->checkItems($expected);

    $this->assertFalse(\Gustavus\Extensibility\Base::remove('TestTag', 'is_int'));
    $this->checkItems($expected);

    \Gustavus\Extensibility\Base::add('TestTag', 'is_int');
    $this->assertFalse(\Gustavus\Extensibility\Base::remove('TestTag', 'is_string'));

    $expected = array(
      'TestTag' => array(
        10  => array(
          'is_int' => 1,
        ),
      ),
    );

    $this->checkItems($expected);
  }

  /**
   * @test
   */
  public function getIteratorWithNonExistentTag()
  {
    $this->assertNULL($this->call('\Gustavus\Extensibility\Base', 'getIterator', array('nonsenseTag')));
  }

  /**
   * @test
   */
  public function stop()
  {
    $this->assertFalse($this->get('\Gustavus\Extensibility\Base', 'stop'));
    $this->assertFalse($this->call('\Gustavus\Extensibility\Base', 'isStopRequested'));

    \Gustavus\Extensibility\Base::stop();

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
  public function startApply()
  {
    $this->assertNULL($this->get('\Gustavus\Extensibility\Base', 'currentTag'));

    $this->call('\Gustavus\Extensibility\Base', 'startApply', array('TestTag'));

    $this->assertSame('TestTag', $this->get('\Gustavus\Extensibility\Base', 'currentTag'));
  }

}
