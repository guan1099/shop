<?php
	$web=[
		'192.168.31.110',
		'192.168.31.120'
	];
	foreach($web as $k=>$v){
		$cmd='ssh '.$v . ' "cd /data/wwwroot/default/lara_weixin && git pull"';
		echo $cmd;echo "<br>";
		$res=shell_exec($cmd);
		echo $res;echo "<hr>";
	}
?>
