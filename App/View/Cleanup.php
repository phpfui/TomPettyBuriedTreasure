<?php

namespace App\View;

class Cleanup
	{
	private string $lcType;

	private \App\UI\ContinuousScrollTable $view;

	public function __construct(private \App\View\Page $page, private \PHPFUI\ORM\Table $table)
		{
		$this->lcType = \lcfirst($this->table->getTableName());
		$this->table->addOrderBy($this->lcType);

		$this->view = new \App\UI\ContinuousScrollTable($page, $table);

		$sortableColumns = [$this->lcType, 'plays', 'rank'];

		$headers = ['Keep', 'Combine', ];
		$headers = \array_merge($headers, $sortableColumns);

		$fields = ['artist', 'title', 'album'];

		foreach ($fields as $field)
			{
			$fieldName = $field . 's';

			if ($this->table->getTableName() != $field)
				{
				$headers[] = $fieldName;
				$this->view->addCustomColumn($fieldName, $this->relatedCallback(...), [$field]);
				}
			else
				{
				$this->view->addCustomColumn($this->lcType, $this->editCallback(...));
				}
			}
		$this->view->addCustomColumn('Keep', $this->keepCallback(...));
		$this->view->addCustomColumn('Combine', $this->combineCallback(...));
		$this->view->addCustomColumn('Search', $this->googleCallback(...));
		$headers[] = 'Search';

		$this->view->setSearchColumns($sortableColumns)->setHeaders($headers)->setSortableColumns($sortableColumns);
		}

	public function list() : \PHPFUI\Container
		{
		$container = new \PHPFUI\Container();
		$form = new \PHPFUI\Form($this->page);
		$form->setAreYouSure(false);
		$submit = new \PHPFUI\Submit('Merge');

		if (\App\Model\Session::checkCSRF() && $submit->getText() == ($_POST['submit'] ?? ''))
			{
			$modelClass = "\\App\Model\\{$this->table->getTableName()}";
			$model = new $modelClass();
			$result = $model->merge($_POST['Keep'] ?? 0, $_POST['Combine'] ?? []);

			if ($result)
				{
				\PHPFUI\Session::setFlash('error', $result);
				}
			else
				{
				\PHPFUI\Session::setFlash('success', 'Merged');
				}
			$this->page->redirect('', \http_build_query($_GET));
			}
		else
			{
			$form->add($submit);
			$form->add($this->view);
			$container->add($form);
			}

		return $container;
		}

	/**
	 * @param array<array<mixed>> $item
	 * @param array<string> $additionalData
	 */
	public function relatedCallback(array $item, array $additionalData) : string
		{
		$currentField = $additionalData[0];

		$id = $this->lcType . 'Id';
		$showSequenceTable = new \App\Table\ShowSequence();
		$showSequenceTable->setDistinct();
		$showSequenceTable->addSelect("{$currentField}.{$currentField}");
		$showSequenceTable->addSelect("{$currentField}.{$currentField}Id");
		$showSequenceTable->addJoin($currentField);
		$showSequenceTable->setWhere(new \PHPFUI\ORM\Condition("ShowSequence.{$id}", $item[$id]));
		$rows = $showSequenceTable->getArrayCursor();
		$text = '';
		$results = [];

		$url = \ucfirst($currentField);

		foreach ($rows as $row)
			{
			$results[] = new \PHPFUI\Link("/Admin/{$url}s/edit/{$row[$currentField . 'Id']}", $row[$currentField], false);
			}

		return \implode('<br>', $results);
		}

	/**
	 * @param array<string, int> $row
	 */
	private function combineCallback(array $row) : \PHPFUI\Input\CheckBox
		{
		return new \PHPFUI\Input\CheckBox('Combine[]', '', $row[$this->lcType . 'Id']);
		}

	/**
	 * @param array<string, string> $row
	 */
	private function editCallback(array $row) : \PHPFUI\Link
		{
		$url = \ucfirst($this->lcType);

		return new \PHPFUI\Link("/Admin/{$url}s/edit/{$row[$this->lcType . 'Id']}", $row[$this->lcType], false);
		}

	/**
	 * @param array<string, string> $row
	 */
	private function googleCallback(array $row) : \PHPFUI\FAIcon
		{
		$stripped = \str_replace('\'"', '', $this->lcType);
		$icon = new \PHPFUI\FAIcon('fab', 'google', 'https://www.google.com/search?q=music ' . $stripped . ' ' . $row[$this->lcType]);
		$icon->addAttribute('target', '_blank');

		return $icon;
		}

	/**
	 * @param array<string, string> $row
	 */
	private function keepCallback(array $row) : \PHPFUI\Input\Radio
		{
		return new \PHPFUI\Input\Radio('Keep', '', $row[$this->lcType . 'Id']);
		}
	}
