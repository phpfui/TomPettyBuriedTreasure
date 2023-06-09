<?php

namespace Tests\Fixtures\Validation;

/**
 * Autogenerated. Do not modify. Modify SQL table, then run oneOffScripts\generateValidators.php table_name
 */
class Domain extends DB\Validator
{
	public static array $validators = [
		'domain' => ['domain'],
	];

	public function __construct(\Tests\Fixtures\Record\Domain $record)
	{
		parent::__construct($record);
	}
}
