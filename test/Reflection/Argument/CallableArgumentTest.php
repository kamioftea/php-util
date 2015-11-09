<?php
	/**
	 * Created by PhpStorm.
	 * User: jeff
	 * Date: 09/11/2015
	 * Time: 14:08
	 */
	
	namespace Goblinoid\Util\Reflection\Argument;
	
	
	class ArgumentTest extends \PHPUnit_Framework_TestCase
	{

		private $reflection_parameter;

		protected function setUp()
		{
			$function = function($a){};

			$this->reflection_parameter = (new \ReflectionFunction($function))->getParameters()[0];
		}

		/**
		 * @expectedException \InvalidArgumentException
		 * @expectedExceptionMessage Provided parameter should have an array type hint
		 */
		public function testArrayThrowsOnInvalidParameter()
		{
			new ArrayArgument($this->reflection_parameter);
		}

		/**
		 * @expectedException \InvalidArgumentException
		 * @expectedExceptionMessage Provided parameter should have a callable type hint
		 */
		public function testCallableThrowsOnInvalidParameter()
		{
			new CallableArgument($this->reflection_parameter);
		}

		/**
		 * @expectedException \InvalidArgumentException
		 * @expectedExceptionMessage Provided parameter should have a class type hint
		 */
		public function testClassThrowsOnInvalidParameter()
		{
			new ClassArgument($this->reflection_parameter);
		}
	}
