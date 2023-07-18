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
		$questionMarks = \array_fill(0, \count($merges), '?');
		$input = \array_merge([$id], $merges);

		foreach ($this->fields as $field)
			{
			$sql = "update ShowSequence set {$field}=? where {$this->type}Id in (" . \implode(',', $questionMarks) . ')';
			\PHPFUI\ORM::execute($sql, $input);
			}

		$sql = "delete from {$this->type} where {$this->type}Id in (" . \implode(',', $questionMarks) . ')';
		\PHPFUI\ORM::execute($sql, $merges);

		return '';
		}
	}
