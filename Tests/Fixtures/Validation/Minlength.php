<?php

namespace Tests\Fixtures\Validation;

/**
 * Autogenerated. Do not modify. Modify SQL table, then run oneOffScripts\generateValidators.php table_name
 */
class Minlength extends DB\Validator
{
	public static array $validators = [
		'minlength' => ['minlength'],
	];

	public function __construct(\Tests\Fixtures\Record\Minlength $record)
	{
		parent::__construct($record);
	}
}