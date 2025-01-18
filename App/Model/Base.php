<?php

namespace App\Model;

class Base
	{
	/**
	 * @var array<string> $fields
	 */
	private array $fields = [];

	public function __construct(protected string $type)
		{
		$this->addField($type . 'Id');
		}

	public function addField(string $field) : self
		{
		$this->fields[] = $field;

		return $this;
		}

	/**
	 * @param array<int,mixed> $merges
	 */
	public function merge(int $id, array $merges) : string
		{
		$className = "\\App\\Record\\{$this->type}";
		$artist = new $className($id);

		if ($artist->empty())
			{
			return "{$this->type} {$id} not found";
			}

		if (empty($merges))
			{
			return "No {$this->type} selected to merge";
			}

		$exists = \array_search($id, $merges);

		if (false !== $exists)
			{
			unset($merges[$id]);
			}

		$showSequenceTable = new \App\Table\ShowSequence();
		$showSequenceTable->setWhere(new \PHPFUI\ORM\Condition("{$this->type}Id", $merges, new \PHPFUI\ORM\Operator\In()));

		foreach ($this->fields as $field)
			{
			$showSequenceTable->update([$field => $id]);
			}

		$className = "\\App\\Table\\{$this->type}";
		$deleteTable = new $className();
		$deleteTable->setWhere(new \PHPFUI\ORM\Condition("{$this->type}Id", $merges, new \PHPFUI\ORM\Operator\In()));
		$deleteTable->delete();

		return '';
		}
	}
