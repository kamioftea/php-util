<?php
	/**
	 * Created by PhpStorm.
	 * User: jeff
	 * Date: 06/08/2015
	 * Time: 15:08
	 */

	namespace HTC\PhinxExtensions\Util\Text;


	class ReplaceVarsFromArrayTest extends \PHPUnit_Framework_TestCase
	{

		public function testBasicUsage()
		{
			$replace_vars = new ReplaceVarsFromArray('$text');
			$result = $replace_vars->apply(['text' => 'hello']);

			$this->assertEquals('hello', $result);
			$this->assertEquals(false, $replace_vars->getAllowNumericalKeys());
		}

		public function testAccessors()
		{
			$replace_vars = (new ReplaceVarsFromArray())
				->setTemplate('$text')
				->setAllowNumericalKeys();
			$result = $replace_vars->apply(['text' => 'hello']);

			$this->assertEquals('hello', $result);
			$this->assertEquals(true, $replace_vars->getAllowNumericalKeys());
		}

		public function testWithBasicTemplate()
		{
			$replace_vars = new ReplaceVarsFromArray('hello $name');
			$result = $replace_vars->apply(['name' => 'world']);

			$this->assertEquals('hello world', $result);
		}

		public function testDuplicateUsage()
		{
			$replace_vars = new ReplaceVarsFromArray('$text $text');
			$result = $replace_vars->apply(['text' => 'hello']);

			$this->assertEquals('hello hello', $result);
		}

		public function testBasicUsageWithMultipleKeys()
		{
			$replace_vars = new ReplaceVarsFromArray('$key $value');
			$result = $replace_vars->apply(['key' => 'hello', 'value' => 'world']);

			$this->assertEquals('hello world', $result);
		}

		public function testBasicUsageWithSentinels()
		{
			$replace_vars = new ReplaceVarsFromArray('${key} {$value}');
			$result = $replace_vars->apply(['key' => 'hello', 'value' => 'world']);

			$this->assertEquals('hello world', $result);
		}

		/**
		 * @expectedException \InvalidArgumentException
		 * @expectedExceptionMessage 'missing_key' was not present in the provided array
		 */
		public function testThrowsWhenMissingData()
		{
			$replace_vars = new ReplaceVarsFromArray('$missing_key $hello');
			$replace_vars->apply(['hello'=> 'world']);
		}

		/**
		 * @expectedException \BadMethodCallException
		 * @expectedExceptionMessage Template must be set before applying to an array
		 */
		public function testThrowsWhenTemplateNotSet()
		{
			$replace_vars = new ReplaceVarsFromArray();
			$replace_vars->apply(['hello'=> 'world']);
		}

		/**
		 * @expectedException \InvalidArgumentException
		 * @expectedExceptionMessage $template should be a string
		 */
		public function testThrowsWhenInvalidTemplate()
		{
			$replace_vars = new ReplaceVarsFromArray();
			$replace_vars->setTemplate([]);
		}

		public function testIndent()
		{
			$template =
<<<'EOD'
    $space_indented
    Some Plain Text $with_var1
	$tab_indented

	Some Plain Text $with_var2
EOD;
			$strings = [
				'space_indented' => 'single line',
			    'tab_indented' => <<<EOD
Some Text
On Multiple lines
	Some of Which is Indented
EOD
,               'with_var1' => 'Hello',
				'with_var2' => 'World',
			];

			$replace_vars = new ReplaceVarsFromArray($template);
			$result = $replace_vars->apply($strings);

			$expected =
<<<'EOD'
    single line
    Some Plain Text Hello
	Some Text
	On Multiple lines
		Some of Which is Indented

	Some Plain Text World
EOD;


			$this->assertEquals($expected, $result);
		}

		public function testIgnoresNumericKeys()
		{
			$replace_vars = new ReplaceVarsFromArray('$0 $1');
			$result = $replace_vars->apply(['hello', 'world']);

			$this->assertEquals('$0 $1', $result);
		}

		public function testUsesNumericKeysWhenRequested()
		{
			$replace_vars = (new ReplaceVarsFromArray('$0 $1'))
				->setAllowNumericalKeys();

			$result = $replace_vars->apply(['hello', 'world']);

			$this->assertEquals('hello world', $result);
		}
	}
