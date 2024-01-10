<?php

namespace App\WWW\Admin;

class Albums extends \App\View\WWWAdminBase implements \PHPFUI\Interfaces\NanoClass
	{
	public function edit(\App\Record\Album $album = new \App\Record\Album()) : void
		{
		if (! $album->loaded())
			{
			$this->page->redirect($this->page->getRelativeURL('home'));

			return;
			}

		$this->addHeaderFromMethod(__METHOD__, $album->album);

		$view = new \App\View\Edit($this->page);
		$this->page->addPageContent($view->edit($album));
		}

	public function merge() : void
		{
		$this->page->addHeader('Merge Albums');
		$view = new \App\View\Cleanup($this->page, new \App\Table\Album());
		$this->page->addPageContent($view->list());
		}
	}
