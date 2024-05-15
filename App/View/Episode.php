<?php

namespace App\View;

class Episode
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
			$post = $_POST;
			$post['showId'] = $show->showId;
			$show->setFrom($post);
			$show->update();

			$this->page->setResponse('Saved');

			return $form;
			}

		$fieldSet = new \PHPFUI\FieldSet('Episode');
		$multiColumn = new \PHPFUI\MultiColumn();
		$airDate = new \PHPFUI\Input\Date($this->page, 'airDate', 'Original Air Date', $show->airDate);
		$airDate->setToolTip('The original date the episode was first broadcast');
		$multiColumn->add($airDate);

		$season = new \PHPFUI\Input\Number('season', 'Season #', $show->season);
		$season->setToolTip('Season must be between 1 and 11')->setAttribute('max', '11')->setAttribute('min', '1');
		$multiColumn->add($season);
		$episode = new \PHPFUI\Input\Number('episode', 'Episode #', $show->episode);
		$episode->setToolTip('Episode must be between 1 and 28')->setAttribute('max', '28')->setAttribute('min', '1');
		$multiColumn->add($episode);

		$fieldSet->add($multiColumn);

		$notes = new \PHPFUI\Input\Text('notes', 'Notes', $show->notes);
		$notes->setToolTip('What Petty talked about, or interesting things about the episode')->setAttribute('maxlength', '255');
		$fieldSet->add($notes);
		$fieldSet->add($submit);

		$form->add($fieldSet);

		return $form;
		}
	}
