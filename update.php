<?php

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
	{
	$php = 'php';
	$composer = 'composer';
	}
else
	{
	$php = '/usr/bin/php8.1';
	$composer = $php . ' composer.phar';
	}

exec($composer . ' self-update');

include 'commonbase.php';

exec($composer . ' update');

// Localize files
$updater = new ComposerUpdate();

$updater->setIgnoredRepos([
	'components',
	'doctrine',
	'GPBMetadata',
	'Jean85',
	'OndraM',
	'PackageVersions',
	'phar-io',
	'PHPStan',
	'sebastian',
	'phpunit',
	'phpspec',
	'ralouphie',
	'Symplify',
	'tecnickcom',
	'theseer',
	'tinify',
	'twig',
]);

$updater->setBaseDirectory(PROJECT_ROOT . '/');
$updater->update();
$updater->deleteNamespace('Symfony\Polyfill');
$updater->deleteNamespace('HighlightUtilities');
$updater->deleteNamespace('Highlight\Highlight');
$updater->deleteNamespace('Highlight\HighlightUtilities');
$updater->deleteNamespace('HighlightUtilities');
$updater->deleteNamespace('cebe\markdown\tests');
$updater->deleteNamespace('Sample');
$updater->deleteFileInNamespace('NoNameSpace', 'fpdf.php');
$updater->deleteFileInNamespace('Laminas\ServiceManager', 'autoload.php');
$updater->deleteFileInNamespace('Laminas\ServiceManager', 'copyright.md');
$updater->deleteFileInNamespace('Laminas\ServiceManager', 'readme.md');
$updater->deleteFileInNamespace('setasign\Fpdi', 'autoload.php');
$updater->deleteFileInNamespace('DeepCopy', 'deep_copy.php');
$updater->deleteFileInNamespace('GuzzleHttp', 'functions.php');
$updater->deleteFileInNamespace('GuzzleHttp', 'functions_include.php');
$updater->deleteFileInNamespace('Clue\StreamFilter', 'functions_include.php');
$updater->deleteFileInNamespace('Clue\StreamFilter', 'functions.php');

// update the public files
exec($php . ' vendor/phpfui/instadoc/install.php www/PHPFUI');

