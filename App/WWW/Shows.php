<?php

namespace App\WWW;

class Shows extends \App\View\WWWPublicBase implements \PHPFUI\Interfaces\NanoClass
	{
	public function home() : void
		{
		$this->page->addHeader('The Shows of Buried Treasure');

		$table = new \App\Table\Show();
		$view = new \App\UI\PaginatedTable($this->page, $table);
		$headers = ['showId', 'season', 'episode', 'airDate', 'notes'];
		$view->addCustomColumn('showId', static function(array $row)
			{
			$id = $row['showId'] - 1;

			return new \PHPFUI\Link("/Shows/info/{$id}", $row['showId'], false);
			});
		$view->setSearchColumns($headers)->setHeaders($headers)->setSortableColumns($headers);

		$this->page->addPageContent($view);
		}

	public function info(?int $showId = null) : void
		{
		if (null === $showId)
			{
			$this->page->redirect('/Shows/info/0');

			return;
			}
		$show = new \App\Record\Show(++$showId);

		if (! $show->loaded())
			{
			$this->page->addHeader("Show {$show->showId} not found");

			return;
			}

		$this->page->addHeader("Show {$show->showId} / Season {$show->season} / Episode {$show->episode} / {$show->airDate}");

		$table = new \App\Table\ShowSequence();
		$table->addJoin('title');
		$table->addJoin('artist');
		$table->addJoin('album');
		$table->setWhere(new \PHPFUI\ORM\Condition('showId', $show->showId));
		$table->setOrderBy('sequence');
		$table->setLimit(100);
		$view = new \App\UI\PaginatedTable($this->page, $table);
		$view->setDownloadName('Buried_Treasure_Show_' . $showId);
		$view->showLimitSelect(false);
		$view->alwaysShowPaginator(false);
		$headers = ['sequence', 'title', 'artist', 'album'];
		$view->setHeaders($headers);
		$view->addCustomColumn('title', static fn (array $row) => new \PHPFUI\Link("/Titles/shows/{$row['titleId']}", $row['title'], false));
		$view->addCustomColumn('artist', static fn (array $row) => new \PHPFUI\Link("/Artists/shows/{$row['artistId']}", $row['artist'], false));
		$view->addCustomColumn('album', static fn (array $row) => new \PHPFUI\Link("/Albums/shows/{$row['albumId']}", $row['album'], false));

		$parts = \explode('\\', $view->getBaseUrl());
		\array_pop($parts);
		$parts[] = 'PAGE';
		$paginator = new \PHPFUI\Pagination($showId - 1, 251, \implode('\\', $parts));
		$paginator->center();
		$paginator->setWindow(4);
		$paginator->setFastForward(25);
		$this->page->addPageContent($paginator);

		$this->page->addPageContent($view);
		}
	}
