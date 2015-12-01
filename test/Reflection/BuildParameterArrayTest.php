<?php

	namespace Goblinoid\Util\Reflection;

	use PHPUnit_Framework_TestCase;

	/**
	 * Created by PhpStorm.
	 * User: jeff
	 * Date: 06/11/2015
	 * Time: 18:23
	 */
	class BuildParameterArrayTest extends PHPUnit_Framework_TestCase
	{

		public function testBuildsFromMethod()
		{
			$this->assertEquals(
				'BuildParameterTestClass::testMethod($arg_1, array $arg_2, callable $arg_3, BuildParameterTestClass $arg_4 = NULL, $arg_5 = true)',
				(string) BuildParameterArray::forMethod(BuildParameterTestClass::class, 'testMethod')
			);
		}

		public function testBuildsFromFunction()
		{
			$this->assertEquals(
				'testFunction($arg_1, array $arg_2, callable $arg_3, BuildParameterTestClass $arg_4 = NULL, $arg_5 = true)',
				(string) BuildParameterArray::forFunction(__NAMESPACE__ . '\testFunction')
			);
		}

		/**
		 * @expectedException \ReflectionException
		 * @expectedExceptionMessage Class NotAClass does not exist
		 */
		public function testThrowsIfNotAClass()
		{
			BuildParameterArray::forMethod('NotAClass', 'testMethod');
		}

		/**
		 * @expectedException \ReflectionException
		 * @expectedExceptionMessage Error building parameters, Method
		 *                           BuildParameterTestClass::notAMethod does not exist
		 */
		public function testThrowsIfNotAMethod()
		{
			BuildParameterArray::forMethod(BuildParameterTestClass::class, 'notAMethod');
		}

		/**
		 * @expectedException \ReflectionException
		 * @expectedExceptionMessage Error building parameters, Function notAFunction does not exist
		 */
		public function testThrowsIfNotAFunction()
		{
			BuildParameterArray::forFunction('notAFunction');
		}

		public function testBuildsBasicAssocArray()
		{
			$data = [
				'arg_1' => 'arg_1',
				'arg_2' => ['arg_2'],
				'arg_3' => 'strtolower',
				'arg_4' => new BuildParameterTestClass(),
				'arg_5' => false,
			];

			$class_output = BuildParameterArray::forMethod(BuildParameterTestClass::class, 'testMethod')->apply($data);
			$this->assertEquals(array_values($data), $class_output);

			$function_output = BuildParameterArray::forFunction(__NAMESPACE__ . '\testFunction')->apply($data);
			$this->assertEquals(array_values($data), $function_output);
		}

		public function testBuildsBasicIndexedArray()
		{
			$data = [
				'arg_1',
				['arg_2'],
				'strtolower',
				new BuildParameterTestClass(),
				false,
			];

			$class_output = BuildParameterArray::forMethod(BuildParameterTestClass::class, 'testMethod')->apply($data);
			$this->assertEquals(array_values($data), $class_output);

			$function_output = BuildParameterArray::forFunction(__NAMESPACE__ . '\testFunction')->apply($data);
			$this->assertEquals(array_values($data), $function_output);
		}

		public function testInvokesWithBasicArray()
		{
			$data = [
				'arg_1' => 'arg_1',
				'arg_2' => ['arg_2'],
				'arg_3' => 'strtolower',
				'arg_4' => new BuildParameterTestClass(),
				'arg_5' => false,
			];

			$build_parameter_method = BuildParameterArray::forMethod(BuildParameterTestClass::class, 'testMethod');
			$class_output = $build_parameter_method($data);
			$this->assertEquals(array_values($data), $class_output);

			$build_parameter_function = BuildParameterArray::forFunction(__NAMESPACE__ . '\testFunction');
			$function_output = $build_parameter_function($data);
			$this->assertEquals(array_values($data), $function_output);
		}

		public function testBuildsFromUnorderedIncompleteArray()
		{
			$data = [
				'arg_3' => 'strtolower',
				'arg_2' => ['arg_2'],
				'arg_4' => new BuildParameterTestClass(),
				'arg_1' => 'arg_1',
			];

			$expected_data = array_merge($data, ['arg_5' => true]);
			ksort($expected_data);
			$expected = array_values($expected_data);

			$class_output = BuildParameterArray::forMethod(BuildParameterTestClass::class, 'testMethod')->apply($data);
			$this->assertEquals($expected, $class_output);

			$function_output = BuildParameterArray::forFunction(__NAMESPACE__ . '\testFunction')->apply($data);
			$this->assertEquals($expected, $function_output);
		}

		public function testBuildsFromMixedArray()
		{
			$data = [
				'arg_5' => false,
				'arg_1',
				['arg_2'],
				'arg_4' => new BuildParameterTestClass(),
				'arg_3' => 'strtolower',
			];

			$expected_data = array_merge($data, ['arg_5' => true]);
			ksort($expected_data);
			$expected = [
				'arg_1',
				['arg_2'],
				'strtolower',
				new BuildParameterTestClass(),
				false
			];

			$class_output = BuildParameterArray::forMethod(BuildParameterTestClass::class, 'testMethod')->apply($data);
			$this->assertEquals($expected, $class_output);

			$function_output = BuildParameterArray::forFunction(__NAMESPACE__ . '\testFunction')->apply($data);
			$this->assertEquals($expected, $function_output);
		}

		/**
		 * @expectedException \OutOfRangeException
		 * @expectedExceptionMessage arg_2 in position 1 is not set in $data, and isn't optional
		 */
		public function testThrowsWhenMissingRequiredValues()
		{
			$data = [
				'arg_1' => 'arg_1',
				'arg_3' => 'strtolower',
				'arg_4' => new BuildParameterTestClass(),
				'arg_5' => false,
			];

			BuildParameterArray::forMethod(BuildParameterTestClass::class, 'testMethod')->apply($data);
		}

		/**
		 * @expectedException \InvalidArgumentException
		 * @expectedExceptionMessage Invalid value for arg_2, it should be an array
		 */
		public function testTestsForArrays()
		{
			$data = [
				'arg_1' => 'arg_1',
				'arg_2' => 'not_an_array',
				'arg_3' => 'strtolower',
				'arg_4' => new BuildParameterTestClass(),
				'arg_5' => false,
			];

			BuildParameterArray::forMethod(BuildParameterTestClass::class, 'testMethod')->apply($data);
		}

		/**
		 * @expectedException \InvalidArgumentException
		 * @expectedExceptionMessage Invalid value for arg_3, it should be callable
		 */
		public function testTestsForCallbacks()
		{
			$data = [
				'arg_1' => 'arg_1',
				'arg_2' => ['array'],
				'arg_3' => 'not_a_callable',
				'arg_4' => new BuildParameterTestClass(),
				'arg_5' => false,
			];

			BuildParameterArray::forMethod(BuildParameterTestClass::class, 'testMethod')->apply($data);
		}

		/**
		 * @expectedException \InvalidArgumentException
		 * @expectedExceptionMessage Invalid value for arg_4, it should be an instance of Goblinoid\Util\Reflection\BuildParameterTestClass
		 */
		public function testTestsForClasses()
		{
			$data = [
				'arg_1' => 'arg_1',
				'arg_2' => ['array'],
				'arg_3' => 'strtolower',
				'arg_4' => 'not_a_class',
				'arg_5' => false,
			];

			BuildParameterArray::forMethod(BuildParameterTestClass::class, 'testMethod')->apply($data);
		}

		/**
		 * @expectedException \InvalidArgumentException
		 * @expectedExceptionMessage Invalid value for arg_4, it should be an instance of Goblinoid\Util\Reflection\BuildParameterTestClass
		 */
		public function testTestsForClassType()
		{
			$data = [
				'arg_1' => 'arg_1',
				'arg_2' => ['array'],
				'arg_3' => 'strtolower',
				'arg_4' => new \stdClass(),
				'arg_5' => false,
			];

			BuildParameterArray::forMethod(BuildParameterTestClass::class, 'testMethod')->apply($data);
		}

		public function testAcceptsNullWhereNullable()
		{
			$data = [
				'arg_1' => 'arg_1',
				'arg_2' => ['array'],
				'arg_3' => 'strtolower',
				'arg_4' => null,
				'arg_5' => false,
			];

			$output = BuildParameterArray::forMethod(BuildParameterTestClass::class, 'testMethod')->apply($data);
			$this->assertEquals(array_values($data), $output);
		}

		public function testReflectsAnonymousFunction()
		{
			$function = function($req, $opt = 23){};
			$data = ['req' => 'data'];
			$expected = ['data', 23];

			$output = BuildParameterArray::forFunction($function)->apply($data);
			$this->assertEquals($expected, $output);
		}
	}

	class BuildParameterTestClass
	{
		function testMethod($arg_1, array $arg_2, callable $arg_3, BuildParameterTestClass $arg_4 = null, $arg_5 = true)
		{

		}
	}

	function testFunction($arg_1, array $arg_2, callable $arg_3, BuildParameterTestClass $arg_4 = null, $arg_5 = true)
	{

	}
