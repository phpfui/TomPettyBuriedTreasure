<?php

namespace App\View;

class Show
	{
	public function __construct(private \App\View\Page $page)
		{
		}

	public function edit(\App\Record\Show $show) : \PHPFUI\HTML5Element
		{
		$submit = new \PHPFUI\Submit();
		$form = new \PHPFUI\Form($this->page, $submit);

		if ($form->isMyCallback($submit))
			{
			$_POST['showId'] = $show->showId;
			$show->setFrom($_POST);
			$show->update();

			$this->page->setResponse('Saved');

			return $form;
			}

		return $form;
		}

	public function editSequence(\App\Record\ShowSequence $sequence) : \PHPFUI\HTML5Element
		{
		$submit = new \PHPFUI\Submit();
		$form = new \PHPFUI\Form($this->page, $submit);

		if ($form->isMyCallback($submit))
			{
			$_POST['showId'] = $sequence->showId;
			$_POST['sequence'] = $sequence->sequence;
			$sequence->setFrom($_POST);
			$sequence->update();

			$this->page->setResponse('Saved');

			return $form;
			}

		$titlePicker = new \App\UI\Picker($this->page, 'title', 'Song Title', $sequence->title);
		$form->add($titlePicker->getEditControl());
		$artistPicker = new \App\UI\Picker($this->page, 'artist', 'Artist', $sequence->artist);
		$form->add($artistPicker->getEditControl());
		$albumPicker = new \App\UI\Picker($this->page, 'album', 'Album Title', $sequence->album);
		$form->add($albumPicker->getEditControl());
		$form->add($submit);

		return $form;
		}
	}
