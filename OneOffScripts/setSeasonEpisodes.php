<?php

include 'common.php';

$showTable = new \App\Table\Show();

$showTable = new \App\Table\Show();
$showTable->addOrderBy('showId');

$seasonStart = [1 => 1, 2=> 23, 3 => 46, 4 => 73, 5 => 99, 6 => 125, 7 => 149, 8 => 175, 9 => 201, 10 => 223, 11 => 249];

function getSeason(int $showId) : int
	{
	$seasonEnd = [1 => 22, 2 => 45, 3 => 72, 4 => 98, 5 => 124, 6 => 148, 7 => 174, 8 => 200, 9 => 222, 10 => 248, 11 => 251];
	$lastSeason = 1;
	foreach ($seasonEnd as $season => $id)
		{
		$lastSeason = $season;
		if ($showId <= $id)
			{
			return $lastSeason;
			}
		}

	return $lastSeason;
	}

$episode = 0;
$lastSeason = 1;
$season = 1;
foreach ($showTable->getRecordCursor() as $show)
	{
	$show->season = getSeason($show->showId);
	if ($show->season > $lastSeason)
		{
		$lastSeason = $show->season;
		$episode = 0;
		}
	++$episode;
	$show->episode = $episode;
	echo "Show {$show->showId} Date {$show->airDate} Season {$show->season} episode {$show->episode}\n";
	$show->update();
	}

