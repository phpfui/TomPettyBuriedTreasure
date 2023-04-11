<?php

include __DIR__ . '/../common.php';

\PHPFUI\ORM::execute(\file_get_contents(PROJECT_ROOT . '/docs/schema.sql'));

// add in all the shows
for ($i = 1; $i <= 251; ++$i)
	{
	$show = new \App\Record\Show();
	$show->showId = $i;
	$show->insert();
	}

// update the ones we know about
$csvReader = new \App\Tools\CSVReader(PROJECT_ROOT . '/data/TomPettyShows.csv');

foreach ($csvReader as $row)
	{
	$show = new \App\Record\Show($row['showId']);
	$show->setFrom($row);
	$show->update();
	}

$csvReader = new \App\Tools\CSVReader(PROJECT_ROOT . '/data/TomPettyShowSongs.csv');
$lastshowId = 0;
$sequence = 0;

foreach ($csvReader as $row)
	{
	++$sequence;

	if ($lastshowId != $row['showId'])
		{
		$lastshowId = $row['showId'];
		$sequence = 1;
		}
	$showSequence = new \App\Record\ShowSequence();
	$showSequence->showId = $lastshowId;
	$showSequence->sequence = $sequence;
	$showSequence->artistId = \get($row['artist'], 'Artist');
	$showSequence->titleId = \get($row['title'], 'Title');
	$showSequence->albumId = \get($row['album'] ?? 'UNKNOWN', 'Album');
	$showSequence->insert();
	}

\PHPFUI\ORM::reportErrors();
