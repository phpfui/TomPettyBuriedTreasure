<?php

include __DIR__ . '/../common.php';

//\PHPFUI\Base::setDebug(\PHPFUI\Session::DEBUG_HTML);
$controller = new \PHPFUI\NanoController($_SERVER['REQUEST_URI']);
$controller->setMissingClass(\App\View\Missing::class);
$controller->setHomePageClass('App\\View\\HomePage');
$controller->setMissingMethod('home');
$controller->setRootNamespace('App\\WWW');
$page = $controller->run();
echo $page;
\PHPFUI\ORM::reportErrors();
