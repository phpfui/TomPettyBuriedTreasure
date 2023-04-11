<?php

include '../common.php';

// Turn on error reporting
\error_reporting(E_ALL);

// Turn on display_errors
\ini_set('display_errors', 1);

// Turn on display_startup_errors
\ini_set('display_startup_errors', 1);

$repo = new \Gitonomy\Git\Repository($_SERVER['DOCUMENT_ROOT'] . '/..');
$wc = $repo->getWorkingCopy();
$wc->checkout('master');
$repo->run('pull');
\header('location: /');
