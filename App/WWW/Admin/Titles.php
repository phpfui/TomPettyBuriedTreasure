<?php

namespace App\WWW\Admin;

class Titles extends \App\View\WWWAdminBase implements \PHPFUI\Interfaces\NanoClass
	{
	public function edit(?\App\Record\Title $title = new \App\Record\Title()) : void
		{
		if ($title->empty())
			{
			$this->page->redirect($this->page->getRelativeURL('home'));

			return;
			}

		$this->addHeaderFromMethod(__METHOD__, $title->title);

		$view = new \App\View\Edit($this->page);
		$this->page->addPageContent($view->edit($title));
		}

	public function merge() : void
		{
		$this->page->addHeader('Merge Titles');

		$view = new \App\View\Cleanup($this->page, new \App\Table\Title());
		$this->page->addPageContent($view->list());
		}
	}
