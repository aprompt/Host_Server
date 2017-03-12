<?php
@ $db = new SQLite3('serv.db');
$db->exec("CREATE TABLE IF NOT EXISTS `connected` (`id` INTEGER PRIMARY KEY,`flag` int);");
$db->exec("CREATE TABLE IF NOT EXISTS `data` (`id` INTEGER PRIMARY KEY,`t` float,`h` float,`pm` float,`ptime` int);");
$db->exec("CREATE TABLE IF NOT EXISTS `ctrl` (`id` INTEGER PRIMARY KEY,`code` varchar);");
if(isset($_GET["ctrl"])) {
	$strs = $_GET["ctrl"];
	switch($strs) {
		case 10:
		$db->exec("replace into `ctrl` (`id`,`code`) values ('1','0');");
		break;
		case 11:
		$db->exec("replace into `ctrl` (`id`,`code`) values ('1','1');");
		break;		
		case 20:
		$db->exec("replace into `ctrl` (`id`,`code`) values ('2','0');");
		break;
		case 21:
		$db->exec("replace into `ctrl` (`id`,`code`) values ('2','1');");
		break;
		$db->close();
		exit;
	}
}
if(isset($_GET["t"])) {
	$result = $db->query("SELECT max(id),t,h,pm FROM `data`");
	$row = $result->fetchArray(SQLITE3_ASSOC);
	echo $row['t'].','.$row['h'].','.$row['pm'].',';
	$result = $db->query("SELECT * FROM `ctrl` order by id asc");
	while($row = $result->fetchArray(SQLITE3_ASSOC)) {
		echo $row['code'].',';
	}
	$result = $db->query("SELECT flag FROM `connected` order by id asc");
	$row = $result->fetchArray(SQLITE3_ASSOC);
	echo $row['flag'];
	$db->close();
	exit;
}
?>