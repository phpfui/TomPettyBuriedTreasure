<?php

include 'common.php';

include 'Date.php';

$showTable = new \App\Table\Show();

$showTable = new \App\Table\Show();
$showTable->addOrderBy('showId');
$showDate = \Date::fromString('2004-12-02');

foreach ($showTable->getRecordCursor() as $show)
	{
	if ($show->showId > 247)
		{
		break;
		}
	$show->airDate = \Date::toString($showDate);
	$show->update();
	$showDate += 7;
	}
