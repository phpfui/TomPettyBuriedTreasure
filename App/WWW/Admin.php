<?php

namespace App\WWW;

class Admin extends \App\View\WWWPublicBase implements \PHPFUI\Interfaces\NanoClass
	{
	public function home() : void
		{
		$this->page->addHeader('Admin');
		$ul = new \PHPFUI\UnorderedList();
		$ul->addItem(new \PHPFUI\ListItem(new \PHPFUI\Link('/Admin/Albums', 'Albums', false)));
		$ul->addItem(new \PHPFUI\ListItem(new \PHPFUI\Link('/Admin/Artists', 'Artists', false)));
		$ul->addItem(new \PHPFUI\ListItem(new \PHPFUI\Link('/Admin/Titles', 'Titles', false)));
		$ul->addItem(new \PHPFUI\ListItem(new \PHPFUI\Link('/Admin/Shows/0', 'Shows', false)));
		$this->page->addPageContent($ul);
		}
	}
