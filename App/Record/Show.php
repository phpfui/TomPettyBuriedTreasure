<?php

namespace App\Record;

class Show extends \App\Record\Definition\Show
	{
	protected static array $relationships = [
		'ShowSequenceChildren' => true,
	];
	}
