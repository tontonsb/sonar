<?php

namespace Tontonsb\Sonar\Support;

trait Facade
{
	protected static $instance;

	protected static function getClass()
	{
		$facadeClass = static::$facadeClass;

		if (empty($facadeClass))
			throw new \Exception('Neither property `facadeClass` nor method `getClass` was found on '.$facadeClass);

		return $facadeClass;
	}

	protected static function getInstance()
	{
		return static::$instance ??= new (static::getClass());
	}

	/**
	 * Calls methods from a class via static interface.
	 */
	public static function __callStatic($method, $parameters)
	{
		return static::getInstance()->$method(...$parameters);
	}
}

