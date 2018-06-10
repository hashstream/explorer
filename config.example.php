<?php
	define("RPC_CACHE", 60);
	define("REDIS_CONFIG", "127.0.0.1");
	
	define("DEFAULT_CHAIN", "BTC");
	define("BASE_URL", "https://hashstream.net%s");
		
	define("NODES_CONFIG", array(
		"BTC" => array(
			"name" => "Bitcoin",
			"version" => 169900,
			"rpc_user" => "rpcusername",
			"rpc_password" => "rpcpassword",
			"rpc_url" => "http://127.0.0.1:8332/",
			"reward" => 50,
			"halving" => 210000,
			"cmc_id" => 1
		)
	));
	
	define("PAGES", array(
		"home" => array(
			"name" => "Home",
			"path" => "/",
			"view" => "views/home.php"
		),
		"block" => array(
			"name" => "Block",
			"path" => "/block",
			"view" => "views/block_home.php"
		),
		"tx" => array(
			"name" => "Transaction",
			"path" => "/tx",
			"view" => "views/tx_home.php"
		),
		"address" => array(
			"name" => "Address",
			"path" => "/address",
			"view" => "views/address_home.php"
		),
		"mempool" => array(
			"name" => "Mempool",
			"path" => "/mempool",
			"view" => "views/mempool_home.php"
		),
	));
?>