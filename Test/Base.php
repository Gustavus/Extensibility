<?php
/**
 * @package Extensibility
 * @subpackage Test
 */

namespace Gustavus\Extensibility\Test;

use Gustavus\Extensibility\Actions;

require_once 'Gustavus/Test/Test.php';
require_once 'Gustavus/Extensibility/Base.php';

/**
 * @package Extensibility
 * @subpackage Test
 */
abstract class Base extends \Gustavus\Test\Test
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
}
