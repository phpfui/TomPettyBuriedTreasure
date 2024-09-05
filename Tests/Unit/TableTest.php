<?php

namespace Tests\Unit;

class TableTest extends \PHPUnit\Framework\TestCase
	{
	public function testAll() : void
		{
		$showSequenceTable = new \App\Table\ShowSequence();
		$input = [];
		$sql = $showSequenceTable->getSelectSQL($input);
		$sql = str_replace("\n", ' ', $sql);

		$this->assertEmpty($input, '$input is not empty for select all');
		$this->assertEquals('SELECT * FROM `showSequence`', $sql, 'sql for select all is bad');
		$this->assertEquals(4876, $showSequenceTable->count(), 'bad count of showSequence table');
		$this->assertEquals(4876, $showSequenceTable->getRecordCursor()->count(), 'bad cursor count of showSequence table');
		$this->assertEquals(4876, $showSequenceTable->getRecordCursor()->total(), 'bad total count of showSequence table');

		$showSequenceTable->addJoin('artist');
		$input = [];
		$sql = $showSequenceTable->getSelectSQL($input);
		$sql = str_replace("\n", ' ', $sql);
		$this->assertEmpty($input, '$input is not empty for addJoin("artist")');
		$this->assertEquals('SELECT  `showSequence`.*, `artist`.`artist`, `artist`.`artistId` as `artist_artistId`, `artist`.`plays`, `artist`.`rank` FROM `showSequence` LEFT JOIN `artist` ON `artist`.`artistId` = `showSequence`.`artistId`', $sql, 'SQL invalid for addJoin("artist")');
		$this->assertEquals(4876, $showSequenceTable->getDataObjectCursor()->count(), 'bad count on join artist showSequence table');
		$this->assertEquals(4876, $showSequenceTable->getDataObjectCursor()->total(), 'bad total on join artist showSequence table');

		$showSequenceTable->addJoin('title');
		$input = [];
		$sql = $showSequenceTable->getSelectSQL($input);
		$sql = str_replace("\n", ' ', $sql);
		$this->assertEmpty($input, '$input is not empty for addJoin("title")');
		$this->assertEquals('SELECT  `showSequence`.*, `artist`.`artist`, `artist`.`artistId` as `artist_artistId`, `artist`.`plays`, `artist`.`rank`, `title`.`plays` as `title_plays`, `title`.`rank` as `title_rank`, `title`.`title`, `title`.`titleId` as `title_titleId` FROM `showSequence` LEFT JOIN `artist` ON `artist`.`artistId` = `showSequence`.`artistId` LEFT JOIN `title` ON `title`.`titleId` = `showSequence`.`titleId`', $sql, 'SQL invalid for addJoin("artist")');
		$this->assertEquals(4876, $showSequenceTable->getDataObjectCursor()->count(), 'bad count on join title showSequence table');
		$this->assertEquals(4876, $showSequenceTable->getDataObjectCursor()->total(), 'bad total on join title showSequence table');

		$showSequenceTable->setLimit(10);
		$input = [];
		$sql = $showSequenceTable->getSelectSQL($input);
		$sql = str_replace("\n", ' ', $sql);
		$this->assertEmpty($input, '$input is not empty for setLimit(10)');
		$this->assertEquals('SELECT  `showSequence`.*, `artist`.`artist`, `artist`.`artistId` as `artist_artistId`, `artist`.`plays`, `artist`.`rank`, `title`.`plays` as `title_plays`, `title`.`rank` as `title_rank`, `title`.`title`, `title`.`titleId` as `title_titleId` FROM `showSequence` LEFT JOIN `artist` ON `artist`.`artistId` = `showSequence`.`artistId` LEFT JOIN `title` ON `title`.`titleId` = `showSequence`.`titleId` LIMIT 10', $sql, 'SQL invalid for setLimit(10)');
		$this->assertEquals(4876, $showSequenceTable->getDataObjectCursor()->total(), 'bad total with limit on showSequence table');
		$this->assertEquals(10, $showSequenceTable->getDataObjectCursor()->count(), 'bad count with limit on showSequence table');

		$showSequenceTable->setOffset(1000);
		$input = [];
		$sql = $showSequenceTable->getSelectSQL($input);
		$sql = str_replace("\n", ' ', $sql);
		$this->assertEmpty($input, '$input is not empty for setOffset(100)');

		$this->assertEquals('SELECT  `showSequence`.*, `artist`.`artist`, `artist`.`artistId` as `artist_artistId`, `artist`.`plays`, `artist`.`rank`, `title`.`plays` as `title_plays`, `title`.`rank` as `title_rank`, `title`.`title`, `title`.`titleId` as `title_titleId` FROM `showSequence` LEFT JOIN `artist` ON `artist`.`artistId` = `showSequence`.`artistId` LEFT JOIN `title` ON `title`.`titleId` = `showSequence`.`titleId` LIMIT 1000, 10', $sql, 'SQL invalid for setOffset(1000)');
		$this->assertEquals(10, $showSequenceTable->getDataObjectCursor()->count(), 'bad count with offset on showSequence table');
		$this->assertEquals(4876, $showSequenceTable->getDataObjectCursor()->total(), 'bad total with offset on showSequence table');

		$showSequenceTable->setOffset(100);
		$input = [];
		$showSequenceTable->setWhere(new \PHPFUI\ORM\Condition('sequence', 2));
		$sql = $showSequenceTable->getSelectSQL($input);
		$sql = str_replace("\n", ' ', $sql);
		$this->assertEquals([2], $input, '$input is not [2] for setOffset(100)');
		$this->assertEquals('SELECT  `showSequence`.*, `artist`.`artist`, `artist`.`artistId` as `artist_artistId`, `artist`.`plays`, `artist`.`rank`, `title`.`plays` as `title_plays`, `title`.`rank` as `title_rank`, `title`.`title`, `title`.`titleId` as `title_titleId` FROM `showSequence` LEFT JOIN `artist` ON `artist`.`artistId` = `showSequence`.`artistId` LEFT JOIN `title` ON `title`.`titleId` = `showSequence`.`titleId` WHERE `sequence` = ? LIMIT 100, 10', $sql, 'SQL invalid for where sequence=2 with setOffset(100)');
		$this->assertEquals(10, $showSequenceTable->getDataObjectCursor()->count(), 'bad count with offset on showSequence table');
		$this->assertEquals(251, $showSequenceTable->getDataObjectCursor()->total(), 'bad total with offset on showSequence table');
		}

	public function testDistinct() : void
		{
		$showSequenceTable = new \App\Table\ShowSequence();
		$showSequenceTable->setDistinct();
		$showSequenceTable->addSelect('showId');
		$input = [];
		$sql = $showSequenceTable->getSelectSQL($input);
		$sql = str_replace("\n", ' ', $sql);
		$this->assertEmpty($input, '$input is not empty in ' . __METHOD__);
		$this->assertEquals('SELECT DISTINCT `showId` FROM `showSequence`', $sql);
		$this->assertEquals(251, $showSequenceTable->getDataObjectCursor()->count(), 'bad count with distinct showId on showSequence table');
		$this->assertEquals(251, $showSequenceTable->getDataObjectCursor()->total(), 'bad count with distinct showId on showSequence table');
		}

	public function testFind() : void
		{
		$artistTable = new \App\Table\Artist();
		$records = $artistTable->find(['artist' => 'Beatles']);
		$input = [];
		$sql = $artistTable->getSelectSQL($input);
		$sql = str_replace("\n", ' ', $sql);
		$this->assertEquals('SELECT * FROM `artist` WHERE `artist`.`artist` LIKE ?', $sql);
		$this->assertEquals(2, $records->count(), 'bad count with find artist Beatles');
		$this->assertEquals(['%Beatles%'], $input, '$input is not ["%Beatles%"] ' . __METHOD__);

		$records = $artistTable->find(['artist' => 'Beatles', 'rank' => 2]);
		$input = [];
		$sql = $artistTable->getSelectSQL($input);
		$sql = str_replace("\n", ' ', $sql);
		$this->assertEquals('SELECT * FROM `artist` WHERE `artist`.`artist` LIKE ? AND `artist`.`rank` = ?', $sql);
		$this->assertEquals(['%Beatles%', 2], $input, '$input is not ["%Beatles%", 2] ' . __METHOD__);
		$this->assertEquals(1, $records->count(), 'bad count with find artist Beatles,rank');
		$this->assertEquals(1, $records->total(), 'bad total with find artist Beatles,rank');

		$records = $artistTable->find(['artist' => 'Beatles', 'rank' => 1]);
		$input = [];
		$sql = $artistTable->getSelectSQL($input);
		$sql = str_replace("\n", ' ', $sql);
		$this->assertEquals('SELECT * FROM `artist` WHERE `artist`.`artist` LIKE ? AND `artist`.`rank` = ?', $sql);
		$this->assertEquals(['%Beatles%', 1], $input, '$input is not ["Beatles", 1] ' . __METHOD__);
		$this->assertEquals(0, $records->count(), 'bad count with find artist Beatles,rank');
		$this->assertEquals(0, $records->total(), 'bad total with find artist Beatles,rank');
		}

	public function testWhere() : void
		{
		$condition = new \PHPFUI\ORM\Condition('artist', 'Beatles');
		$condition->or('artist', '%stone%', new \PHPFUI\ORM\Operator\Like());
		$artistTable = new \App\Table\Artist();
		$artistTable->setWhere($condition);
		$records = $artistTable->getRecordCursor();
		$sql = $artistTable->getLastSql();
		$sql = str_replace("\n", ' ', $sql);
		$input = $artistTable->getLastInput();
		$this->assertEquals(4, $records->count(), 'bad count with find artist Beatles or like stone');
		$this->assertEquals(['Beatles', '%stone%'], $input, '$input is not ["Beatles"] ' . __METHOD__);
		$this->assertEquals('SELECT * FROM `artist` WHERE `artist` = ? OR `artist` LIKE ?', $sql);
		}

	public function testGroupBy() : void
		{
		$showSequenceTable = new \App\Table\ShowSequence();
		$showSequenceTable->addJoin('artist');
		$showSequenceTable->addGroupBy('artist.artist');
		$input = [];
		$sql = $showSequenceTable->getSelectSQL($input);
		$sql = str_replace("\n", ' ', $sql);
		$this->assertEmpty($input, '$input is not empty for addJoin("artist") with group by artist.artist');
		$this->assertEquals('SELECT  `showSequence`.*, `artist`.`artist`, `artist`.`artistId` as `artist_artistId`, `artist`.`plays`, `artist`.`rank` FROM `showSequence` LEFT JOIN `artist` ON `artist`.`artistId` = `showSequence`.`artistId` GROUP BY `artist`.`artist`', $sql, 'SQL invalid for addJoin("artist") with group by artist.artist');
		$artistTable = new \App\Table\Artist();
		$this->assertEquals($artistTable->count(), $showSequenceTable->getDataObjectCursor()->count(), 'bad count on join artist showSequence table');
		$this->assertEquals($artistTable->total(), $showSequenceTable->getDataObjectCursor()->total(), 'bad total on join artist showSequence table');
		}

	public function testUnion() : void
		{
		$artistTable = new \App\Table\Artist();
		$artistTable->addUnion(new \App\Table\Title());
		$artistTable->addUnion(new \App\Table\Album());
		$this->assertEquals(4089, $artistTable->count(), 'bad count for union');
		$this->assertEquals(4089, $artistTable->total(), 'bad total for union');

		$artistTable->setLimit(100);
		$this->assertEquals(100, $artistTable->count(), 'bad count for union limit 100');
		$this->assertEquals(4089, $artistTable->total(), 'bad total for union limit 100');
		}
	}
