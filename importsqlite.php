<?php

include 'commonbase.php';

\PHPFUI\ORM::setLogger(new \PHPFUI\ORM\StandardErrorLogger());

$path = __DIR__ . '/sqlite/buriedtreasure.sqlite';
$sqlite = new \PDO('sqlite:' . $path);

$sqliteConnection = \PHPFUI\ORM::addConnection($sqlite);

\PHPFUI\ORM::execute("DROP TABLE IF EXISTS album");
\PHPFUI\ORM::execute("CREATE TABLE IF NOT EXISTS album (
albumId INTEGER primary key autoincrement,
album varchar(255) NOT NULL DEFAULT '',
plays integer NOT NULL DEFAULT '0',
rank integer NOT NULL DEFAULT '0')");

\PHPFUI\ORM::execute("DROP TABLE IF EXISTS artist");
\PHPFUI\ORM::execute("CREATE TABLE IF NOT EXISTS artist (
  artistId integer primary key autoincrement,
  artist varchar(255) NOT NULL DEFAULT '',
  plays integer NOT NULL DEFAULT '0',
  rank integer NOT NULL DEFAULT '0')");

\PHPFUI\ORM::execute("DROP TABLE IF EXISTS show");
\PHPFUI\ORM::execute("CREATE TABLE IF NOT EXISTS show (
  showId INTEGER primary key,
  airDate date DEFAULT NULL,
  repeat integer NOT NULL DEFAULT '0',
  season integer NOT NULL DEFAULT '0',
  episode integer NOT NULL DEFAULT '0',
  notes varchar(255) NOT NULL DEFAULT '')");

\PHPFUI\ORM::execute("DROP TABLE IF EXISTS showSequence");
\PHPFUI\ORM::execute("CREATE TABLE IF NOT EXISTS showSequence (
  showId integer NOT NULL,
  sequence integer NOT NULL,
  artistId integer NOT NULL,
  titleId integer NOT NULL,
  albumId integer NOT NULL,
  primary key (showId,sequence)) without rowid;");

\PHPFUI\ORM::execute("DROP TABLE IF EXISTS title");
\PHPFUI\ORM::execute("CREATE TABLE IF NOT EXISTS title (
  titleId integer primary key autoincrement,
  title varchar(255) NOT NULL DEFAULT '',
  plays integer NOT NULL DEFAULT '0',
  rank integer NOT NULL DEFAULT '0')");

$dbSettings = new \App\Settings\DB();
$pdo = $dbSettings->getPDO();
$mysqlConnection = \PHPFUI\ORM::addConnection($dbSettings->getPDO());

$cursors = [];
$cursors[] = (new \App\Table\Artist())->getRecordCursor();
$cursors[] = (new \App\Table\Album())->getRecordCursor();
$cursors[] = (new \App\Table\Title())->getRecordCursor();
$cursors[] = (new \App\Table\Show())->getRecordCursor();
$cursors[] = (new \App\Table\ShowSequence())->getRecordCursor();

\PHPFUI\ORM::useConnection($sqliteConnection);

foreach ($cursors as $cursor)
	{
	foreach ($cursor as $record)
		{
		$record->insert();
		}
	}


