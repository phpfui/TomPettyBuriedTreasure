<?php

namespace App\View;

class Page extends \PHPFUI\Page
	{
	private bool $done = false;

	private \PHPFUI\HTML5Element $mainColumn;

	public function __construct(protected \PHPFUI\Interfaces\NanoController $controller)
		{
		parent::__construct();
		$this->addStyleSheet('/css/styles.css');
		$this->mainColumn = new \PHPFUI\HTML5Element('div');
		$this->mainColumn->addClass('main-column');
		$this->setPageName('Buried Treasure');

		$this->setFavIcon('/favicon.ico');
		$this->addHeadTag('<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="manifest" href="/site.webmanifest">
<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="theme-color" content="#ffffff">');
		}

	public function addHeader(string $header = '') : bool
		{
		$this->addPageContent(new \PHPFUI\Header('Tom Petty\'s Buried Treasure Playlists'));

		$parts = \explode('/', $this->controller->getInvokedPath());
		$activeMenu = \array_shift($parts);

		$menu = new \PHPFUI\Menu();
		$menu->addMenuItem(new \PHPFUI\MenuItem('Home', '/'));
		$menu->addMenuItem(new \PHPFUI\MenuItem('Shows', '/Shows/info/0'));
		$menu->addMenuItem(new \PHPFUI\MenuItem('Artists', '/Artists'));
		$menu->addMenuItem(new \PHPFUI\MenuItem('Titles', '/Titles'));
		$menu->addMenuItem(new \PHPFUI\MenuItem('Albums', '/Albums'));
		$menu->addMenuItem(new \PHPFUI\MenuItem('Download', '/Download'));
		$settings = new \App\Settings\Admin();

		if ($settings->allowAdmin)
			{
			$menu->addMenuItem(new \PHPFUI\MenuItem('Admin', '/Admin'));
			}
		$menu->setActiveName($activeMenu);
		$this->addPageContent($menu);

		if ($header)
			{
			$this->addPageContent(new \PHPFUI\SubHeader($header));
			}

		// add in flash messages
		$callouts = ['success', 'primary', 'secondary', 'warning', 'alert'];

		foreach ($callouts as $calloutClass)
			{
			$message = \PHPFUI\Session::getFlash($calloutClass);

			if (! $message)
				{
				continue;
				}

			$callout = new \PHPFUI\Callout($calloutClass);
			$callout->addAttribute('data-closable');

			if (\is_array($message))
				{
				$ul = new \PHPFUI\UnorderedList();

				foreach ($message as $error)
					{
					$ul->addItem(new \PHPFUI\ListItem($error));
					}
				$callout->add($ul);
				}
			else
				{
				$callout->add($message);
				}
			$this->addPageContent($callout);
			}
		$mdPath = PROJECT_ROOT . '/docs/' . $header . '.md';

		if (! \file_exists($mdPath))
			{
			return true;
			}
		$markdown = \file_get_contents($mdPath);
		$parser = new \cebe\markdown\MarkdownExtra();
		$this->addPageContent($parser->parse($markdown));

		return true;
		}

	public function addPageContent($item)
		{
		$show = true;

		if (! $this->getDone())
			{
			$this->mainColumn->add($item);
			}

		return $this;
		}

	public function getBaseURL() : string
		{
		// first character could be lower case, so upper case it to match class
		$url = '/' . \ucfirst(\substr(parent::getBaseURL(), 1));

		return $url;
		}

	public function getBody() : string
		{
		return "{$this->mainColumn}";
		}

	public function getDone() : bool
		{
		return $this->done;
		}

	/**
	 * replace last non class part
	 */
	public function getRelativeURL(string $lastPart) : string
		{
		$parts = \explode('/', \ucfirst(\substr(parent::getBaseURL(), 1)));

		while ($part = \array_pop($parts))
			{
			if (\ctype_upper($part[0]))
				{
				$parts[] = $part;

				break;
				}
			}
		$parts[] = $lastPart;

		return '/' . \implode('/', $parts);
		}

	public function isAuthorized(string $permission, ?string $menu = null) : bool
		{
		return true;
		}

	public function setDone($done = true) : Page
		{
		$this->done = $done;

		return $this;
		}
	}
