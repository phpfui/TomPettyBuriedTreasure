<?php

namespace App\Record\Definition;

/**
 * Autogenerated. Do not modify. Modify SQL table, then generate with \PHPFUI\ORM\Tool\Generate\CRUD class.
 *
 * @property int $albumId MySQL type integer
 * @property \App\Record\Album $album related record
 * @property int $artistId MySQL type integer
 * @property \App\Record\Artist $artist related record
 * @property ?int $second_artistId MySQL type integer
 * @property int $sequence MySQL type integer
 * @property int $showId MySQL type integer
 * @property \App\Record\Show $show related record
 * @property int $titleId MySQL type integer
 * @property \App\Record\Title $title related record
 */
abstract class ShowSequence extends \PHPFUI\ORM\Record
	{
	protected static bool $autoIncrement = false;

	/** @var array<string, \PHPFUI\ORM\FieldDefinition> */
	protected static array $fields = [];

	/** @var array<string> */
	protected static array $primaryKeys = ['showId', 'sequence', ];

	protected static string $table = 'showSequence';

	public function initFieldDefinitions() : static
		{
		if (! \count(static::$fields))
			{
			static::$fields = [
				'albumId' => new \PHPFUI\ORM\FieldDefinition('integer', 'int', 0, false, ),
				'artistId' => new \PHPFUI\ORM\FieldDefinition('integer', 'int', 0, false, ),
				'second_artistId' => new \PHPFUI\ORM\FieldDefinition('integer', 'int', 0, true, null, ),
				'sequence' => new \PHPFUI\ORM\FieldDefinition('integer', 'int', 0, false, ),
				'showId' => new \PHPFUI\ORM\FieldDefinition('integer', 'int', 0, false, ),
				'titleId' => new \PHPFUI\ORM\FieldDefinition('integer', 'int', 0, false, ),
			];
			}

		return $this;
		}
	}
