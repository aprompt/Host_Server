<?php
@ $db = new SQLite3('serv.db');
$db->exec("DROP TABLE IF EXISTS `connected`;");
$db->exec("DROP TABLE IF EXISTS `data`;");
$db->exec("DROP TABLE IF EXISTS `ctrl`;");
$db->exec("CREATE TABLE IF NOT EXISTS `connected` (`id` INTEGER PRIMARY KEY,`flag` int);");
$db->exec("CREATE TABLE IF NOT EXISTS `data` (`id` INTEGER PRIMARY KEY,`t` float,`h` float,`pm` float,`ptime` int);");
$db->exec("CREATE TABLE IF NOT EXISTS `ctrl` (`id` INTEGER PRIMARY KEY,`code` varchar);");
$db->exec("INSERT INTO `connected` (`id`,`flag`) values ('1','0');");
$db->exec("INSERT INTO `data` (`t`,`h`,`pm`,`ptime`) values ('0','0','0','0');");
$db->exec("INSERT INTO `ctrl` (`id`,`code`) values ('1','0');");
$db->exec("INSERT INTO `ctrl` (`id`,`code`) values ('2','0');");
$db->close();
exit('reset ok');
?>