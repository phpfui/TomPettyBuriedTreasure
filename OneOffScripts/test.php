<?php

include '../commonbase.php';

$csvReader = new \App\Tools\CSVReader(PROJECT_ROOT . '/data/TomPettyShowSongs.csv');
$csvWriter = new \App\Tools\CSVWriter('TomPettyShowSongsClean.csv', ',', false);
$csvWriter->addHeaderRow();

$artists = [];
$titles = [];
$albums = [];

foreach ($csvReader as $row)
	{
	$artist = $row['artist'];

	if (0 === \strpos($artist, 'The '))
		{
		$artist = \substr($artist, 4);
		}
	\add($artists, $artist);
	\add($titles, $row['title']);
	\add($albums, $row['album'] ?? 'UNKNOWN');
	$row['artist'] = $artist;
	$csvWriter->outputRow($row);
	}

//display($artists, "Artists");
//display($titles, "Titles");
\display($albums, 'Albums');

function add(array &$list, string $thing) : void
	{
	if (! isset($list[$thing]))
		{
		$list[$thing] = 0;
		}
	++$list[$thing];
	}

function display(array &$list, string $thing) : void
	{
	\ksort($list);

	\print_r($list);

	echo \count($list) . ' ' . $thing . "\n";
	}
