<?php
//本程序必须工作在php-cli模式 !
error_reporting(E_ALL);

set_time_limit(0);

ob_implicit_flush();
//运行环境初始化完毕
$address = '133.130.97.102'; //Server IP
$port = 3389; //Server Port

$socket =socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
$old_state=socket_get_option($socket,SOL_SOCKET,SO_REUSEADDR);
printf("Old sock state: %s\n", $old_state); //断点0
//允许端口重用
socket_set_option($socket,SOL_SOCKET,SO_REUSEADDR,1);
$new_state=socket_get_option($socket,SOL_SOCKET,SO_REUSEADDR);
printf("New sock state: %s\n",$new_state); //断点0
//异常处理
if(($sock=socket_create(AF_INET,SOCK_STREAM,SOL_TCP))===false){
	echo "socket_create() failed: reason: ".socket_strerror(socket_last_error())."\n";
}
if(socket_bind($sock,$address,$port)===false){
	echo "socket_bind() failed: reason: ".socket_strerror(socket_last_error($sock))."\n";
}
if(socket_listen($sock,5)===false){
	echo "socket_listen() failed: reason: ".socket_strerror(socket_last_error($sock))."\n";
}
//启动监听
do {
if(($msgsock=socket_accept($sock))===false){
	echo "socket_accept() failed: reason: ".socket_strerror(socket_last_error($sock))."\n";
	echo dataio('0',0);
	break;
}
//开始数据交互
$msg="OK\r\n";
echo dataio('1',0);
socket_write($msgsock,$msg,strlen($msg));

do {
	if(false===($buf=socket_read($msgsock,2048,PHP_NORMAL_READ))){
		echo "socket_read()failed: reason: ".socket_strerror(socket_last_error($msgsock))."\n";
		break;
	}
	if(!$buf=trim($buf)){
		continue;
	}
	if($buf=='close'){
		break;
	}
	if($buf=='exit'){
		socket_close($msgsock);
		break 2;
	}
	if(preg_match('/^([1-9]\d*|0)(\.\d{1,2})?,([1-9]\d*|0)(\.\d{1,2})?,([1-9]\d*|0)(\.\d{1,2})?/',$buf)) {
	$ctlcode = dataio($buf,1);
	echo "Data from Client: $buf\n"; //断点1
	
	$talkback="+$ctlcode\r\n";
	socket_write($msgsock,$talkback,strlen($talkback));
	} else {
		echo "Bad data !\r\n";
	}
} while(true);

socket_close($msgsock);
echo dataio('0',0);
} while(true);

socket_close($sock);
echo dataio('0',0);

function dataio($data='', $mode=0) { //数据I/O处理
	$db = new SQLite3('serv.db');
	if($mode==0) {
		$db->exec("CREATE TABLE IF NOT EXISTS `connected` (`id` INTEGER PRIMARY KEY,`flag` int);");
		$db->exec("replace into `connected` (`id`,`flag`) values ('1','$data');");
		$db->close();
		$status=($data=='1')?"connected !\r\n":"disconnect !\r\n";
		return $status;
	}
	else if($mode==1){
		$db->exec("CREATE TABLE IF NOT EXISTS `data` (`id` INTEGER PRIMARY KEY,`t` float,`h` float,`pm` float,`ptime` int);");
		$db->exec("CREATE TABLE IF NOT EXISTS `ctrl` (`id` INTEGER PRIMARY KEY,`code` varchar);");
		$ctlcode='';
		$ptime=time();
		$attr=explode(",",$data);
		$res0 = $db->exec("INSERT INTO `data` (`t`,`h`,`pm`,`ptime`) values ('$attr[0]','$attr[1]','$attr[2]','$ptime');");
		$res1 = $db->query("SELECT code FROM `ctrl` order by id asc");
		while($res = $res1->fetchArray(SQLITE3_ASSOC)) {
			$ctlcode.=$res['code'];
			}
		$db->close();
		return $ctlcode;
	}
	else return 'invalid mode !';
}
?>