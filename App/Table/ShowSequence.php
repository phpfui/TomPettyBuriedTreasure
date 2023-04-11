<?php

namespace App\Table;

class ShowSequence extends \PHPFUI\ORM\Table
	{
	protected static string $className = '\\' . \App\Record\ShowSequence::class;

	public function getShows() : \PHPFUI\ORM\ArrayCursor
		{
		$this->addJoin('artist');
		$this->addJoin('title');
		$this->addJoin('album');
		$this->setOrderBy('showId')->addOrderBy('sequence');
		$this->addSelect('showId');
		$this->addSelect('sequence');
		$this->addSelect(new \PHPFUI\ORM\Field('artist.artist'));
		$this->addSelect(new \PHPFUI\ORM\Field('title.title'));
		$this->addSelect(new \PHPFUI\ORM\Field('album.album'));

		return $this->getArrayCursor();
		}

	public function getShow(\App\Record\Show $show) : \PHPFUI\ORM\ArrayCursor
		{
		$this->addJoin('artist');
		$this->addJoin('title');
		$this->addJoin('album');
		$this->setOrderBy('sequence');
		$this->addSelect('showId');
		$this->addSelect('sequence');
		$this->addSelect(new \PHPFUI\ORM\Field('artist.artist'));
		$this->addSelect(new \PHPFUI\ORM\Field('title.title'));
		$this->addSelect(new \PHPFUI\ORM\Field('album.album'));
		$this->setWhere(new \PHPFUI\ORM\Condition('showId', $show->showId));

		return $this->getArrayCursor();
		}
	}
