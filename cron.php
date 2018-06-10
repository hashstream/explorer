<?php
	header("Content-Type: text/plain");
	
	include_once("config.php");
	include_once("functions.php");
	include_once("getinfo.php");
	
	$_REDIS = new Redis();
	$_REDIS->pconnect(REDIS_CONFIG);
	$_CHAIN = null;
	
	//check last 100 blocks are cached
	foreach(NODES_CONFIG as $ch) {
		$_CHAIN = (object)$ch;
		
		echo "Checking chain: $_CHAIN->name\n";
		$best = get_best_block();
		$hash = get_block_hash($best);
		echo "Block height is: $best ($hash)\n";
		
		//count backwards 100 blocks
		for($x = $best; $x >= $best-10; $x--) {
			$tb = get_block($hash);
			
			echo json_encode($tb) . "\n";
			
			$hash = $tb->previousblockhash;
		}
	}
?>