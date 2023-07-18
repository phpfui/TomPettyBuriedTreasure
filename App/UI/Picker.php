<?php

namespace App\UI;

class Picker
	{
	private readonly \PHPFUI\ORM\Table $table;

	public function __construct(private readonly \PHPFUI\Page $page, private readonly string $field, private readonly string $label, private \PHPFUI\ORM\Record $initial)
		{
		$class = '\\App\\Table\\' . \ucfirst($field);
		$this->table = new $class();
		}

	/**
	 * @param array<string,string> $parameters
	 *
	 * @return array<string, array<int, array<string, string>>>
	 */
	public function callback(array $parameters) : array
		{
		$returnValue = [];
		$returnValue[] = ['value' => '', 'data' => 'No Song Selected'];

		if (empty($parameters['save']))
			{
			$names = \explode(' ', (string)$parameters['AutoComplete']);
			$condition = new \PHPFUI\ORM\Condition();

			foreach ($names as $name)
				{
				$condition->or(new \PHPFUI\ORM\Condition($this->field, "%{$name}%", new \PHPFUI\ORM\Operator\Like()));
				}
			$this->table->setWhere($condition);

			foreach ($this->table->getArrayCursor() as $record)
				{
				$returnValue[] = ['value' => \str_replace(['&quot;', '"'], '', $record[$this->field]), 'data' => $record[$this->field . 'Id']];
				}
			}

		return ['suggestions' => $returnValue];
		}

	public function getEditControl() : \PHPFUI\Input\AutoComplete
		{
		$field = $this->field;
		$fieldId = $field . 'Id';
		$control = new \PHPFUI\Input\AutoComplete($this->page, $this->callback(...), 'text', $fieldId, $this->label, $this->initial->{$field});
		$hidden = $control->getHiddenField();
		$hidden->setValue($this->initial->{$fieldId} ?? '');
		$control->setNoFreeForm();

		return $control;
		}
	}
