<?php

namespace Tests\Fixtures\Definition;

/**
 * Autogenerated. Do not modify. Modify SQL table, then run oneOffScripts\generateCRUD.php table_name
 */
abstract class Enum extends DB\Record
{
	public static bool $autoIncrement = false;

	public static array $fields = [
		'enum' => ['sqltype', 'string', 19, false, '', false, ],
	];

	public static string $primaryKey = '';

	public static string $table = '';

	public function __construct($parameters = null)
	{
		parent::__construct($parameters);
	}
}
