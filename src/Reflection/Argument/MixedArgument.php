<?php
	/**
	 * Created by PhpStorm.
	 * User: jeff
	 * Date: 06/11/2015
	 * Time: 17:32
	 */
	
	namespace Goblinoid\Util\Reflection\Argument;
	
	class MixedArgument extends Argument
	{
		protected function isValid($value)
		{
			return [true, null];
		}

		protected function getTypeHint()
		{
			return null;
		}
		
	}
