<?php

include '../common.php';

// Turn on error reporting
\error_reporting(E_ALL);

// Turn on display_errors
\ini_set('display_errors', 1);

// Turn on display_startup_errors
\ini_set('display_startup_errors', 1);

$repo = new \Gitonomy\Git\Repository($_SERVER['DOCUMENT_ROOT'] . '/..');
$repo->run('prune');         // remove old versions and branches
$repo->run('stash');         // stash any changed files, user can get back stash if needed so we won't delete it
$repo->run('clean', ['-f']); // remove unstaged files
$wc = $repo->getWorkingCopy();
$wc->checkout('main');
$repo->run('pull');
\header('location: /');
