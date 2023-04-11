<?php

namespace App\View;

class WWWAdminBase extends WWWBase implements \Stringable
	{
	public function addHeaderFromMethod(string $method, string $extra) : void
		{
		$parts = \explode('::', $method);
		$parts = \explode('\\', $parts[0]);
		$type = \array_pop($parts);
		$type = \substr($type, 0, \strlen($type) - 1);
		$this->page->addHeader(\ucfirst(\substr($method, \strpos($method, '::') + 2)) . " {$type} {$extra}");
		}

	public function home() : void
		{
		$parts = \explode('\\', static::class);
		$section = \array_pop($parts);

		$this->page->addHeader('Admin ' . $section);
		$menu = new \PHPFUI\Menu();

		$ul = new \PHPFUI\UnorderedList();
		$ul->addItem(new \PHPFUI\ListItem(new \PHPFUI\Link("/Admin/{$section}/list", "Edit {$section}", false)));
		$ul->addItem(new \PHPFUI\ListItem(new \PHPFUI\Link("/Admin/{$section}/merge", "Merge {$section}", false)));
		$this->page->addPageContent($ul);
		}

	public function list() : void
		{
		$this->page->addHeader("Edit {$this->title} of Buried Treasure");

		$className = $this->tableClassName;
		$table = new $className();
		$view = new \App\UI\PaginatedTable($this->page, $table);
		$headers = [$this->fieldName, 'plays', 'rank', 'Search'];

		$fieldName = $this->fieldName;

		$title = $this->title;
		$view->addCustomColumn($this->fieldName, static fn (array $row) => new \PHPFUI\Link("/Admin/{$title}/edit/{$row[$fieldName . 'Id']}", $row[$fieldName], false));
		$view->addCustomColumn('Search', $this->googleCallback(...));

		$view->setSearchColumns($headers)->setHeaders($headers)->setSortableColumns($headers);
		$view->setContinuousScroll();

		$this->page->addPageContent($view);
		}

	private function googleCallback(array $row) : \PHPFUI\FAIcon
		{
		$type = \lcfirst($this->type);
		$text = \str_replace(['"', "'"], '', $row[$type]);
		$text = \str_replace(' ', '+', $text);
		$icon = new \PHPFUI\FAIcon('fab', 'google', "https://www.google.com/search?q=music+{$type}+{$text}");
		$icon->addAttribute('target', '_blank');

		return $icon;
		}
	}
