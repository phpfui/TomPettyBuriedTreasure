<?php

namespace App\View;

class HomePage extends \App\View\WWWPublicBase implements \PHPFUI\Interfaces\NanoClass
	{
	public function __construct(\PHPFUI\Interfaces\NanoController $controller)
		{
		parent::__construct($controller);
		$this->page->addHeader('Home');
		}
	}
