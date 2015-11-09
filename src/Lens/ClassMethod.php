<?php
	/**
	 * Created by PhpStorm.
	 * User: jeff
	 * Date: 09/11/2015
	 * Time: 12:16
	 */
	
	namespace Goblinoid\Util\Lens;
	
	use Goblinoid\Util\Reflection\BuildParameterArray;

	class ClassMethod
	{
		protected $method;

		protected $args;

		function __construct($method = null, $args = [])
		{
			$this->setMethod($method);
			$this->setArgs($args);
		}

		public function __invoke($instance)
		{
			return $this->apply($instance);
		}

		public function apply($instance)
		{
			$params = BuildParameterArray::forMethod($instance, $this->getMethod())->apply($this->getArgs());

			return call_user_func_array([$instance, $this->getMethod()], $params);
		}

		/**
		 * @return mixed
		 */
		public function getMethod()
		{
			return $this->method;
		}

		/**
		 * @param mixed $method
		 * @return $this
		 */
		public function setMethod($method)
		{
			$this->method = $method;

			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getArgs()
		{
			return $this->args;
		}

		/**
		 * @param mixed $args
		 * @return $this
		 */
		public function setArgs($args)
		{
			$this->args = $args;

			return $this;
		}

	}
