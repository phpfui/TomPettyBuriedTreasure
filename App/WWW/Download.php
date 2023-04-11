<?php

namespace App\WWW;

class Download extends \App\View\WWWBase implements \PHPFUI\Interfaces\NanoClass
	{
	public function home() : void
		{
		$this->page->addHeader('Download Buried Treasure Data');
		$ul = new \PHPFUI\UnorderedList();
		$ul->addItem(new \PHPFUI\ListItem(new \PHPFUI\Link('/Download/album', 'Albums', false)));
		$ul->addItem(new \PHPFUI\ListItem(new \PHPFUI\Link('/Download/artist', 'Artists', false)));
		$ul->addItem(new \PHPFUI\ListItem(new \PHPFUI\Link('/Download/show', 'Shows', false)));
		$ul->addItem(new \PHPFUI\ListItem(new \PHPFUI\Link('/Download/title', 'Titles', false)));
		$this->page->addPageContent($ul);
		}

	public function artist() : void
		{
		$csvWriter = new \App\Tools\CSVWriter('BuriedTreasureArtists.csv');
		$csvWriter->addHeaderRow();
		$table = new \App\Table\Artist();

		foreach ($table->getArrayCursor() as $row)
			{
			$csvWriter->outputRow($row);
			}
		}

	public function album() : void
		{
		$csvWriter = new \App\Tools\CSVWriter('BuriedTreasureAlbums.csv');
		$csvWriter->addHeaderRow();
		$table = new \App\Table\Album();

		foreach ($table->getArrayCursor() as $row)
			{
			$csvWriter->outputRow($row);
			}
		}

	public function title() : void
		{
		$csvWriter = new \App\Tools\CSVWriter('BuriedTreasureTitles.csv');
		$csvWriter->addHeaderRow();
		$table = new \App\Table\Title();

		foreach ($table->getArrayCursor() as $row)
			{
			$csvWriter->outputRow($row);
			}
		}

	public function show(?\App\Record\Show $show = null) : void
		{
		$showSequence = new \App\Table\ShowSequence();

		if (! $show)
			{
			$csvWriter = new \App\Tools\CSVWriter('BuriedTreasureShows.csv');
			$csvWriter->addHeaderRow();

			foreach ($showSequence->getShows() as $row)
				{
				$csvWriter->outputRow($row);
				}
			}
		elseif (! $show->empty())
			{
			$csvWriter = new \App\Tools\CSVWriter('BuriedTreasureShow' . $show->showId . '.csv');
			$csvWriter->addHeaderRow();

			foreach ($showSequence->getShow($show) as $row)
				{
				unset($row['sequence'], $row['artistId'], $row['titleId'], $row['albumId']);
				$csvWriter->outputRow($row);
				}
			}
		}
	}
