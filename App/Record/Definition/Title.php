<?php

namespace App\Record\Definition;

/**
 * Autogenerated. Do not modify. Modify SQL table, then generate with \PHPFUI\ORM\Tool\Generate\CRUD class.
 *
 * @property int $plays MySQL type integer
 * @property int $rank MySQL type integer
 * @property string $title MySQL type varchar(255)
 * @property ?int $titleId MySQL type integer
 */
abstract class Title extends \PHPFUI\ORM\Record
	{
	protected static bool $autoIncrement = true;

	/** @var array<string, array<mixed>> */
	protected static array $fields = [
		// MYSQL_TYPE, PHP_TYPE, LENGTH, ALLOWS_NULL, DEFAULT
		'plays' => ['integer', 'int', 0, false, 0, ],
		'rank' => ['integer', 'int', 0, false, 0, ],
		'title' => ['varchar(255)', 'string', 255, false, '', ],
		'titleId' => ['integer', 'int', 0, true, ],
	];

	/** @var array<string> */
	protected static array $primaryKeys = ['titleId', ];

	protected static string $table = 'title';
	}
