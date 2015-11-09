<?php
	/**
	 * Created by PhpStorm.
	 * User: jeff
	 * Date: 06/11/2015
	 * Time: 17:33
	 */
	
	namespace Goblinoid\Util\Reflection\Argument;
	
	class ArrayArgument extends Argument
	{
		public function __construct(\ReflectionParameter $parameter)
		{
			if(!$parameter->isArray())
			{
				throw new \InvalidArgumentException('Provided parameter should have an array type hint');
			}

			parent::__construct($parameter);
		}

		protected function isValid($value)
		{
			$is_valid = is_array($value);
			$error = $this->buildErrorMessage($is_valid, 'an array');

			return [$is_valid, $error];
		}

		protected function getTypeHint()
		{
			return 'array';
		}


	}
