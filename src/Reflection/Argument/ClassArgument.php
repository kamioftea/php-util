<?php
	/**
	 * Created by PhpStorm.
	 * User: jeff
	 * Date: 06/11/2015
	 * Time: 17:33
	 */
	
	namespace Goblinoid\Util\Reflection\Argument;
	
	class ClassArgument extends Argument
	{
		private $class_name;

		private $short_name;

		public function __construct(\ReflectionParameter $parameter)
		{
			if($parameter->getClass() === null)
			{
				throw new \InvalidArgumentException('Provided parameter should have a class type hint');
			}

			parent::__construct($parameter);

			$this->class_name = $parameter->getClass()->getName();
			$this->short_name = $parameter->getClass()->getShortName();
		}

		protected function isValid($value)
		{
			$is_valid = $value instanceof $this->class_name;

			$error = $this->buildErrorMessage($is_valid, 'an instance of {$class}', ['class' => $this->class_name]);

			return [$is_valid, $error];
		}

		protected function getTypeHint()
		{
			return $this->short_name;
		}
	}
