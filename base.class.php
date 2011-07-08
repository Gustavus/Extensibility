<?php 
/**
 * @package General
 * @subpackage Extensibility
 *
 * $Id: base.class.php 4 2010-02-11 18:52:30Z jlencion $
 * @version $Revision: 4 $
 * $Date: 2010-02-11 12:52:30 -0600 (Thu, 11 Feb 2010) $
 */

/**
 * Base class for filters and actions
 *
 * @package General
 * @subpackage Extensibility
 */
abstract class Extensibility
{
	/**
	 * 
	 * @var array
	 */
	protected static $items	= array();
	
	/**
	 * 
	 * @var string
	 */
	protected static $currentTag	= NULL;
	
	/**
	 * 
	 * @var boolean
	 */
	private static $stop			= FALSE;
	
	/**
	 * @param string $tag
	 * @param callback $function
	 * @param integer $priority
	 * @param integer $acceptedArguments
	 * @return boolean
	 */
	final static public function add($tag, $function, $priority = 10, $acceptedArguments = 1)
	{
		// Remove it if it exists, so it doesn't get added twice
		self::remove($tag, $function, $priority, $acceptedArguments);
		self::$items[$tag][$priority][]	= array('function' => $function, 'acceptedArguments' => $acceptedArguments);
	}
	
	/**
	 * @param string $tag
	 * @param callback $function
	 * @param integer $priority
	 * @param integer $acceptedArguments
	 * @return boolean
	 */
	final static public function remove($tag, $function, $priority = 10, $acceptedArguments = 1)
	{
		if (isset(self::$items[$tag][$priority]))
		{
			foreach(self::$items[$tag][$priority] as $key => $item)
			{
				if ($item == array('function' => $function, 'acceptedArguments' => $acceptedArguments))
				{
					unset(self::$items[$tag][$priority][$key]);
					return TRUE;
				}
			}
		}

		return FALSE;
	}
	
	/**
	 * Stops the rest of the filters and actions in the current tag from being
	 * run 
	 * @param mixed $return Value to return
	 * @return void
	 */
	final static public function stop($return = NULL)
	{
		self::$stop	= TRUE;
	}
	
	/**
	 * @return boolean
	 */
	final static protected function isStopRequested()
	{
		if (self::$stop === TRUE)
			return TRUE;
		else
			return FALSE;
	}
	
	/**
	 * @return void
	 */
	final static protected function doStop()
	{
		self::$stop			= FALSE;
		self::$currentTag	= NULL;
	}
	
	/**
	 * @param string $tag
	 * @return void
	 */
	final static protected function prioritize($tag)
	{
		ksort(self::$items[$tag]);
	}

	/**
	 * @param mixed $callback
	 * @param array $arguments
	 */
	final static protected function execute($callback, array $arguments)
	{
		return call_user_func_array($callback, $arguments);
		
		// Using the reflection class seems to be slower than using call_user_func_array()
		if (is_array($callback)) // Function is in a class
		{
			$ref		= new \ReflectionClass(get_class($callback[0]));
			$method		= $ref->getMethod($callback[1]);
			return $method->invokeArgs($callback[0], $arguments);
		}
		else // Function is not in a class
		{
			$method		= new \ReflectionFunction($callback);
			return $method->invokeArgs($arguments);
		}
	}
}