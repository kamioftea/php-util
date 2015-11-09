<?php
	/**
	 * Created by PhpStorm.
	 * User: jeff
	 * Date: 06/11/2015
	 * Time: 13:48
	 */
	
	namespace Goblinoid\Util\Reflection;

	use Goblinoid\Util\Reflection\Argument\Argument;
	use Goblinoid\Util\Reflection\Argument\ArrayArgument;
	use Goblinoid\Util\Reflection\Argument\CallableArgument;
	use Goblinoid\Util\Reflection\Argument\ClassArgument;
	use Goblinoid\Util\Reflection\Argument\MixedArgument;
	use Goblinoid\Util\Text\ReplaceVarsFromArray;

	class BuildParameterArray
	{
		/**
		 * @var Argument[]
		 */
		private $arguments;

		private $method_name;

		/**
		 * BuildParameterArray constructor.
		 *
		 * @param \ReflectionParameter[] $reflection_parameters
		 * @param                        $method_name
		 */
		private function __construct($reflection_parameters, $method_name)
		{
			$this->arguments = array_map([
				$this,
				'buildParameter'
			], $reflection_parameters);

			$this->method_name = $method_name;
		}

		private function buildParameter(\ReflectionParameter $parameter)
		{
			switch (true)
			{
				case $parameter->getClass() !== null:
					return new ClassArgument($parameter);

				case $parameter->isArray():
					return new ArrayArgument($parameter);

				case $parameter->isCallable():
					return new CallableArgument($parameter);

				default:
					return new MixedArgument($parameter);
			}
		}

		/**
		 * @param $class
		 * @param $method
		 * @return BuildParameterArray
		 * @throws \ReflectionException
		 */
		public static function forMethod($class, $method)
		{
			$reflection_class = $class instanceof \ReflectionClass
				? $class
				: new \ReflectionClass($class);

			$method_name = $reflection_class->getShortName() . '::' . $method;

			if (!$reflection_class->hasMethod($method))
			{
				$message = (new ReplaceVarsFromArray('Error building parameters, Method {$method_name} does not exist'))
					->apply(['method_name' => $method_name]);

				throw new \ReflectionException($message);
			}

			$reflection_parameters = $reflection_class->getMethod($method)->getParameters();

			return new static($reflection_parameters, $method_name);
		}

		/**
		 * @param $function
		 * @return BuildParameterArray
		 * @throws \ReflectionException
		 */
		public static function forFunction($function)
		{
			if (!function_exists($function))
			{
				$message = (new ReplaceVarsFromArray('Error building parameters, Function {$method_name} does not exist'))
					->apply(['method_name' => $function]);

				throw new \ReflectionException($message);
			}

			$reflection_function = new \ReflectionFunction($function);
			$reflection_parameters = $reflection_function->getParameters();

			return new static($reflection_parameters, $reflection_function->getShortName());
		}

		/**
		 * @param array $data
		 * @return array
		 */
		public function __invoke(array $data)
		{
			return $this->apply($data);
		}

		/**
		 * @param array $data
		 * @return array
		 */
		public function apply(array $data)
		{
			$output = [];

			foreach($this->arguments as $argument)
			{
				$output[$argument->getPosition()] = $argument->getValueFromData($data);
			}

			return $output;
		}

		public function __toString()
		{
			return (new ReplaceVarsFromArray('{$method_name}({$arguments})'))->apply([
				'method_name' => $this->method_name,
			    'arguments' => implode(', ', $this->arguments),
			]);
		}
	}
