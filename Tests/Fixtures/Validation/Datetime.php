<?php

namespace Tests\Fixtures\Validation;

/**
 * Autogenerated. Do not modify. Modify SQL table, then run oneOffScripts\generateValidators.php table_name
 */
class Datetime extends DB\Validator
{
	public static array $validators = [
		'datetime' => ['datetime'],
	];

	public function __construct(\Tests\Fixtures\Record\Datetime $record)
	{
		parent::__construct($record);
	}
}
