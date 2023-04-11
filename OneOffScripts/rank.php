<?php

include __DIR__ . '/../common.php';

// compute ranks

$showSequence = new \App\Table\ShowSequence();
$artists = [];
$titles = [];
$albums = [];

foreach ($showSequence->getRecordCursor() as $showSequence)
	{
	\add($artists, $showSequence->artistId);
	\add($titles, $showSequence->titleId);
	\add($albums, $showSequence->albumId);
	}

\rank($artists, 'Artist');
\rank($albums, 'Album');
\rank($titles, 'Title');

function rank(array $list, string $class) : void
	{
	$class = "\\App\\Record\\{$class}";
	\arsort($list);
	$rank = $tieRank = 1;
	$crud = new $class();
	$lastPlays = 0;

	foreach ($list as $id => $plays)
		{
		if ($lastPlays != $plays)
			{
			$lastPlays = $plays;
			$tieRank = $rank;
			}
		$crud->read($id);
		$crud->plays = $plays;
		$crud->rank = $tieRank;
		$crud->update();
		++$rank;
		}
	}

function add(array &$list, ?int $thing) : void
	{
	if (empty($thing))
		{
		return;
		}

	if (! isset($list[$thing]))
		{
		$list[$thing] = 0;
		}
	++$list[$thing];
	}

function get(string $thing, string $type) : int
	{
	$class = "\\App\\Record\\{$type}";
	$id = $type . 'Id';
	$field = \strtolower($type);
	$record = new $class();

	if ($record->read([$field => $thing]))
		{
		return $record->{$id};
		}
	$record->{$field} = $thing;

	return $record->insert();
	}

\PHPFUI\ORM::reportErrors();
