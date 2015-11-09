<?php
	/**
	 * Created by PhpStorm.
	 * User: jeff
	 * Date: 06/11/2015
	 * Time: 17:33
	 */
	
	namespace Goblinoid\Util\Reflection\Argument;
	
	class CallableArgument extends Argument
	{
		public function __construct(\ReflectionParameter $parameter)
		{
			if(!$parameter->isCallable())
			{
				throw new \InvalidArgumentException('Provided parameter should have a callable type hint');
			}

			parent::__construct($parameter);
		}

		protected function isValid($value)
		{
			$is_valid = is_callable($value);
			$error = $this->buildErrorMessage($is_valid, 'callable.');

			return [$is_valid, $error];
		}

		protected function getTypeHint()
		{
			return 'callable';
		}

	}
