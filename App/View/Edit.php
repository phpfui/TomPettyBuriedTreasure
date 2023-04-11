<?php

namespace App\View;

class Edit
	{
	public function __construct(private \App\View\Page $page)
		{
		}

	public function edit(\PHPFUI\ORM\Record $record) : \PHPFUI\HTML5Element
		{
		$submit = new \PHPFUI\Submit('Save');
		$form = new \PHPFUI\Form($this->page, $submit);
		$field = $record->getTableName();

		if ($form->isMyCallback())
			{
			unset($_POST[$field . 'Id']);
			$record->setFrom($_POST);
			$record->update();
			$this->page->setResponse('Saved');
			}
		else
			{
			$name = new \PHPFUI\Input\Text($field, 'Name', $record->{$field});
			$name->setRequired()->addAttribute('maxlength', '255');
			$form->add($name);
			$form->add($submit);
			}

		return $form;
		}
	}
