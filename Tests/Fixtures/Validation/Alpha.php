<?php

namespace Tests\Fixtures\Validation;

/**
 * Autogenerated. Do not modify. Modify SQL table, then run oneOffScripts\generateValidators.php table_name
 */
class Alpha extends DB\Validator
{
	public static array $validators = [
		'alpha' => ['alpha'],
	];

	public function __construct(\Tests\Fixtures\Record\Alpha $record)
	{
		parent::__construct($record);
	}
}