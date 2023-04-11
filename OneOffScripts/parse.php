<?php

\ini_set('memory_limit', -1);

include 'commonbase.php';

$parser = new \App\Model\Parser();
$parser->run('tompettyFull.html');
