<?php

namespace App\Application;

use App;
use Nette;

class Configuration implements \ArrayAccess
{

	use Nette\SmartObject;

	/** @var Nette\DI\Container */
	private $context;

	/**
	 * Configuration constructor.
	 * @param Nette\DI\Container $context
	 */
	public function __construct(Nette\DI\Container $context)
	{
		$this->context = $context;
	}

	/**
	 * @param $value
	 * @return mixed
	 */
	public function expand($value)
	{
		return $this->context->expand($value);
	}

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function &__get($name)
	{
		return $this->context->parameters[$name];
	}

	/**
	 * @param mixed $offset
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->context->parameters);
	}

	/**
	 * @param mixed $offset
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return $this->__get($offset);
	}

	/**
	 * !! You cannot change anything in configuration !!
	 * 	 *
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value)
	{
		throw new \LogicException('You cannot change anything in configuration');
	}

	/**
	 * throw new \LogicException('You cannot change anything in configuration');
	 *
	 * @param mixed $offset
	 */
	public function offsetUnset($offset)
	{
		throw new \LogicException('You cannot change anything in configuration');
	}

}
