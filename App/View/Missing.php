<?php

namespace App\View;

class Missing extends \App\View\Page implements \PHPFUI\Interfaces\NanoClass
	{
	public function __construct(\PHPFUI\Interfaces\NanoController $controller)
		{
		parent::__construct($controller);
		$this->addPageContent(new \PHPFUI\Header($controller->getUri() . ' was not found on this server.'));
		}
	}
