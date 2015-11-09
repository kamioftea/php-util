<?php
	/**
	 * Created by PhpStorm.
	 * User: jeff
	 * Date: 09/11/2015
	 * Time: 12:24
	 */
	
	namespace Goblinoid\Util\Lens;
	
	
	class ClassMethodTest extends \PHPUnit_Framework_TestCase
	{
		public function testAccessors()
		{
			$class_method = new ClassMethod('basicMethod');

			$this->assertEquals('basicMethod', $class_method->getMethod());
			$this->assertEquals([], $class_method->getArgs());

			$class_method->setMethod('methodWithArgs');
			$class_method->setArgs(['arg_1' => 'val_1']);

			$this->assertEquals('methodWithArgs', $class_method->getMethod());
			$this->assertEquals(['arg_1' => 'val_1'], $class_method->getArgs());
		}

		public function testBasicUsage()
		{
			$class_method = new ClassMethod('basicMethod');
			$test_class = new ClassMethodTestClass();

			$this->assertEquals('Basic Method Return Value', $class_method($test_class));
		}

		/**
		 * @dataProvider getArgMap
		 */
		public function testArgsUsage($args, $expected)
		{
			$class_method = new ClassMethod('methodWithArgs', $args);
			$test_class = new ClassMethodTestClass();

			$this->assertEquals($expected, $class_method($test_class));
		}

		public function getArgMap()
		{
			return [
				'normal' => [['arg_1' => 'val_1', 'arg_2' => ['val_2']], 'true false'],
				'with default' => [['arg_1' => 'val_2'], 'false true'],
			];
		}
	}

	class ClassMethodTestClass {
		public function basicMethod()
		{
			return 'Basic Method Return Value';
		}

		public function methodWithArgs($arg_1, array $arg_2 = [])
		{
			return ($arg_1 === 'val_1' ? 'true' : 'false') . ' ' . (empty($arg_2) ? 'true' : 'false');
		}
	}
