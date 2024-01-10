<?php

namespace App\WWW\Admin;

class Shows extends \App\View\WWWAdminBase implements \PHPFUI\Interfaces\NanoClass
	{
	public function edit(\App\Record\Show $show = new \App\Record\Show()) : void
		{
		if (! $show->loaded())
			{
			$this->page->redirect($this->page->getRelativeURL('home'));

			return;
			}

		$this->addHeaderFromMethod(__METHOD__, 'Show ' . $show->showId);

		$view = new \App\View\Show($this->page);
		$this->page->addPageContent($view->edit($show));
		}

	public function editSequence(int $showId = 0, int $sequence = 0) : void
		{
		$showSequence = new \App\Record\ShowSequence();
		$showSequence->read(['showId' => $showId, 'sequence' => $sequence]);

		if (! $showSequence->loaded())
			{
			$this->page->redirect('/Admin/Shows/0');

			return;
			}

		$this->addHeaderFromMethod(__METHOD__, "{$showSequence->showId} Position {$showSequence->sequence}");

		$view = new \App\View\Show($this->page);
		$this->page->addPageContent($view->editSequence($showSequence));
		}

	public function home(?int $showId = null) : void
		{
		if (null === $showId)
			{
			$this->page->redirect('/Admin/Shows/0');

			return;
			}
		$show = new \App\Record\Show(++$showId);
		$table = new \App\Table\ShowSequence();
		$table->addJoin('title');
		$table->addJoin('artist');
		$table->addJoin('album');
		$table->setWhere(new \PHPFUI\ORM\Condition('showId', $show->showId));
		$table->setOrderBy('sequence');
		$table->setLimit(100);
		$view = new \App\UI\PaginatedTable($this->page, $table);

		if (! $show->loaded())
			{
			$this->page->addHeader("Show {$show->showId} not found");

			return;
			}

		$this->page->addHeader("Admin Edit Show {$show->showId}");

		$view->setDownloadName('Buried_Treasure_Show_' . $showId);
		$view->showLimitSelect(false);
		$view->alwaysShowPaginator(false);
		$headers = ['sequence', 'title', 'artist', 'album'];
		$view->setHeaders($headers);
		$view->addCustomColumn('sequence', static fn (array $row) => new \PHPFUI\Link("/Admin/Shows/editSequence/{$row['showId']}/{$row['sequence']}", $row['sequence'], false));
		$view->addCustomColumn('title', static fn (array $row) => new \PHPFUI\Link("/Admin/Titles/edit/{$row['titleId']}", $row['title'], false));
		$view->addCustomColumn('artist', static fn (array $row) => new \PHPFUI\Link("/Admin/Artists/edit/{$row['artistId']}", $row['artist'], false));
		$view->addCustomColumn('album', static fn (array $row) => new \PHPFUI\Link("/Admin/Albums/edit/{$row['albumId']}", $row['album'], false));

		$parts = \explode('\\', $view->getBaseUrl());
		\array_pop($parts);
		$parts[] = 'PAGE';
		$paginator = new \PHPFUI\Pagination($showId - 1, 251, \implode('\\', $parts));
		$paginator->center();
		$paginator->setWindow(4);
		$paginator->setFastForward(25);
		$this->page->addPageContent($paginator);

		$episodeView = new \App\View\Episode($this->page);
		$this->page->addPageContent($episodeView->edit($show));

		$this->page->addPageContent($view);
		}
	}
