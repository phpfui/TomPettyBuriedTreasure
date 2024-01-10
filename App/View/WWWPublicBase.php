<?php

namespace App\View;

class WWWPublicBase extends WWWBase implements \Stringable
	{
	public function home() : void
		{
		$this->page->addHeader("The {$this->title} of Buried Treasure");

		$className = $this->tableClassName;
		$table = new $className();
		$view = new \App\UI\ContinuousScrollTable($this->page, $table);
		$headers = [$this->fieldName, 'plays', 'rank'];

		$fieldName = $this->fieldName;

		$title = $this->title;
		$view->addCustomColumn($this->fieldName, static fn (array $row) => new \PHPFUI\Link("/{$title}/shows/{$row[$fieldName . 'Id']}", $row[$fieldName], false));

		$view->setSearchColumns($headers)->setHeaders($headers)->setSortableColumns($headers);

		$this->page->addPageContent($view);
		}

	public function shows(int $id = 0) : void
		{
		$className = $this->recordClassName;
		$record = new $className($id);

		if (! $record->loaded())
			{
			$this->page->addHeader("{$this->type} not found");

			return;
			}

		$fieldName = $this->fieldName;
		$name = $record->{$fieldName};
		$this->page->addHeader("Shows featuring {$name}");

		$table = new \App\Table\ShowSequence();
		$table->addJoin('title');
		$table->addJoin('artist');
		$table->addJoin('album');
		$condition = new \PHPFUI\ORM\Condition('showSequence.' . $fieldName . 'Id', $id);

		$table->setWhere($condition);
		$view = new \App\UI\PaginatedTable($this->page, $table);
		$headers = ['showId', 'sequence', 'title', 'artist', 'album'];

		foreach(['title', 'artist', 'album'] as $field)
			{
			if ($field == $fieldName)
				{
				continue;
				}
			$plural = \ucfirst($field . 's');
			$view->addCustomColumn($field, static fn (array $row) => new \PHPFUI\Link("/{$plural}/shows/" . $row[$field . 'Id'], $row[$field], false));
			}
		$view->addCustomColumn('showId', static fn (array $row) => new \PHPFUI\Link('/Shows/info/' . ($row['showId'] - 1), $row['showId'], false));

		$view->setSearchColumns($headers)->setHeaders($headers)->setSortableColumns($headers);

		$this->page->addPageContent($view);
		}
	}
