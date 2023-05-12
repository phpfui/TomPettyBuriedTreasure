<?php

include '../common.php';

$daysBack = 1;

\App\Tools\SessionManager::purgeOld(24 * 60 * 60 * $daysBack);
