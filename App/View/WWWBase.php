<?php

namespace App\View;

class WWWBase implements \Stringable
	{
	protected \App\View\Page $page;

	protected string $title;

	protected string $type;

	protected string $fieldName;

	protected string $recordClassName;

	protected string $tableClassName;

	public function __construct(protected \PHPFUI\Interfaces\NanoController $controller)
		{
		$this->page = new \App\View\Page($controller);
		$parts = \explode('\\', \get_debug_type($this));

		if (\in_array('Admin', $parts))
			{
			$settings = new \App\Settings\Admin();

			if (! $settings->allowAdmin)
				{
				$this->page->redirect('/');

				return;
				}
			}
		$this->title = \array_pop($parts);
		$this->type = \substr($this->title, 0, \strlen($this->title) - 1);
		$this->fieldName = \strtolower($this->type);
		$this->recordClassName = "\\App\\Record\\{$this->type}";
		$this->tableClassName = "\\App\\Table\\{$this->type}";
		}

	public function __toString() : string
		{
		$errors = $this->controller->getErrors();

		if ($errors)
			{
			$callout = new \PHPFUI\Callout('alert');
			$ul = new \PHPFUI\UnorderedList();

			foreach ($errors as $error)
				{
				$ul->addItem(new \PHPFUI\ListItem($error));
				}
			$callout->add($ul);
			$this->page->addPageContent(new \PHPFUI\SubHeader('Errors:'));
			$this->page->addPageContent($callout);
			}

		return "{$this->page}";
		}
	}
