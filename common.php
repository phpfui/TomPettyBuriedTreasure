<?php
include 'commonbase.php';

function trans(string $text, array $parameters = []) : string
	{
	return \PHPFUI\Translation\Translator::trans($text, $parameters);
	}

\App\Tools\SessionManager::start();

$path = __DIR__ . '/data/buriedtreasure.sqlite';
\PHPFUI\ORM::addConnection(new \PHPFUI\ORM\PDOInstance('sqlite:' . $path));
\PHPFUI\ORM::setLogger(new \PHPFUI\ORM\StandardErrorLogger());
\PHPFUI\Translation\Translator::setTranslationDirectory(__DIR__ . '/languages');
\PHPFUI\Translation\Translator::setLocale('en_US');
