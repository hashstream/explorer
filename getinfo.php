<?php
	include_once("functions.php");
	
	function get_home_info_cards() {
		global $_CHAIN;
		
		if($_CHAIN->version < 160000) {
			$info = http_rpc_query("getinfo", array());
			
			return array(
				"Chain" => $info->testnet ? "testnet" : "main",
				"Height" => $info->blocks,
				"Difficulty" => format_metric($info->difficulty),
				"Version" => $info->protocolversion,
				"Connections" => $info->connections
			);
		} else {
			$blockchain_info = http_rpc_query("getblockchaininfo", array());
			$network_info = http_rpc_query("getnetworkinfo", array());
			
			return array(
				"Chain" => $blockchain_info->chain,
				"Height" => $blockchain_info->blocks,
				"Difficulty" => format_metric($blockchain_info->difficulty),
				"Size" => format_bytes($blockchain_info->size_on_disk),
				"Version" => $network_info->protocolversion,
				"Subversion" => $network_info->subversion,
				"Connections" => $network_info->connections
			);
		}
	}
	
	function get_peer_info() {
		global $_CHAIN;
		
		$peers = http_rpc_query("getpeerinfo", array());
		$ret = (object)[
			"headers" => array(),
			"rows" => array()
		];
		
		if ($_CHAIN->version < 130000) {
			$ret->headers = array("IP", "Version", "Subversion", "Bytes Sent", "Bytes Recv");
			
			foreach($peers as $peer) {
				array_push($ret->rows, array($peer->addr, $peer->version, $peer->subver, format_bytes($peer->bytessent), format_bytes($peer->bytesrecv)));
			}
		} else {
			$ret->headers = array("ID", "IP", "Version", "Subversion", "Bytes Sent", "Bytes Recv", "Ping");
			
			foreach($peers as $peer) {
				array_push($ret->rows, array($peer->id, $peer->addr, $peer->version, $peer->subver, format_bytes($peer->bytessent), format_bytes($peer->bytesrecv), number_format($peer->pingtime * 1000, 2) . "ms"));
			}
		}
		
		return $ret;
	}
	
	function get_best_block() {
		return http_rpc_query("getblockcount", array());
	}
	
	function get_block_hash($height) {		
		return http_rpc_query("getblockhash", array($height), 0);	
	}
	
	function get_block($hash) {
		global $_CHAIN;
		
		$bk = http_rpc_query("getblock", array($hash), 0);
		$cb_tx = get_raw_tx($bk->tx[0]);
		$bk->createdby = get_pool_from_tx($cb_tx);
		$bk->reward = get_block_reward($bk->height);
		
		if($_CHAIN->version < 130000) {
			$bk->versionHex = str_pad(dechex($bk->version), 8, "0");
		}
		
		return $bk;
	}
	
	function get_raw_tx_internal($txid, $with_inputs = false) {
		global $_CHAIN;
		
		$tx = http_rpc_query("getrawtransaction", array($txid, 1), -1);
		$tx->is_coinbase = isset($tx->vin[0]->coinbase);
		$tx->is_confirmed = isset($tx->blockhash);
		
		//Fill in some data if its not set
		if(!isset($tx->size)) { //i cant find docs of when this was added
			$tx->size = strlen($tx->hex) / 2; //take len from hex value
			$tx->weight = $tx->size * 4;
		}
		
		$tx->value_out = 0;
		foreach($tx->vout as $txout) {
			$tx->value_out += $txout->value;
		}
		
		if($with_inputs) {
			foreach($tx->vin as $k => $txin) {
				$tx->vin[$k]->outpoint = get_raw_tx($txin->txid)->vout[$txin->vout];
			}
			
			$tx->value_in = 0;
			foreach($tx->vin as $txin) {
				if($txin->outpoint != null) {
					$tx->value_in += $txin->outpoint->value;
				}
			}
			
			if($tx->is_coinbase) {
				$tx->value_in = $tx->value_out;
			}
			
			$tx->fees = round($tx->value_in - $tx->value_out, 8);
			$tx->sat_per_byte = $tx->fees != 0 ? (($tx->fees / SAT) / $tx->size) : 0;
		}
		
		return $tx;
	}
	
	function get_raw_tx($txid, $with_inputs = false) {
		global $_REDIS, $_CHAIN, $_CHAIN_KEY;
		
		$res = null;
		
		$cache_key = sprintf("hs:exp:%s:tx:%s%s", strtolower($_CHAIN_KEY), strtolower($txid), ($with_inputs ? "with-inputs" : ""));
		$tx_cache = $_REDIS->get($cach_key);
		if($tx_cache == false) {
			$res = get_raw_tx_internal($txid, $with_inputs);
			
			if($res->is_confirmed) {
				$_REDIS->set($cach_key, json_decode($res)); //cache forever
			} else {
				$_REDIS->setEx($cach_key, _TS_MIN, json_decode($res));
			}
		}else {
			$res = json_decode($tx_cache);
		}
		
		return $res;
	}
	
	function get_mempool() {
		$mempool = http_rpc_query("getrawmempool", array(), -1);
		return get_txns($mempool, 0, true);
	}
	
	function get_txns($txids, $limit = 0, $with_inputs = true) {
		$ret = array();
		
		if($limit == 0) {
			$limit = count($txids);
		}
		
		foreach($txids as $txid) {
			if($limit == 0) {
				break;
			}
			
			$ret[$txid] = get_raw_tx($txid, $with_inputs);
			$limit--;
		}
		
		return $ret;
	}
	
	function get_last_n_blocks($n) {
		$ret = array();
		
		$best = get_best_block();
		$hash = get_block_hash($best);
		
		for($x = 0; $x < $n; $x++) {
			$bk = get_block($hash);
			array_push($ret, $bk);
			
			$hash = $bk->previousblockhash;
		}
		
		return $ret;
	}
	
	function get_pools() {
		global $_REDIS;
		
		$pools_key = "hs:exp:pools_json";
		
		$pools = $_REDIS->get($pools_key);
		if($pools == false) {
			$pools = curl_get("https://raw.githubusercontent.com/hashstream/pools/master/pools.json");
			$_REDIS->setEx($pools_key, _TS_DAY, $pools);
			$pools = json_decode($pools);
		} else {
			$pools = json_decode($pools);
		}
		
		return $pools;
	}
	
	function get_pool_from_tx($tx) {
		if(isset($tx->vin[0]->coinbase)) {
			$pools = get_pools();
			$cb = hex2bin($tx->vin[0]->coinbase);
			
			foreach($pools->coinbase_tags as $tag => $tag_details) {
				if(strpos($cb, $tag) != false) {
					return $tag_details;
				}
			}
			
			foreach($pools->payout_addresses as $tag => $tag_details) {
				foreach($tx->vout as $txout) {
					if(isset($txout->scriptPubKey->addresses) && in_array($tag, $txout->scriptPubKey->addresses)) {
						return $tag_details;
					}
				}
			}
		}
	}
	
	function get_pool_stats($blocks) {
		$ret = array();
		
		foreach($blocks as $block) {
			$created_by = $block->createdby != null ? $block->createdby->name : "Unknown";
			
			if(!isset($ret[$created_by])) {
				$ret[$created_by] = 1;
			} else {
				$ret[$created_by]++;
			}
		}
		
		return $ret;
	}
	
	function get_script_pubkey_acronym($vout) {
		switch($vout->scriptPubKey->type) {
			case "pubkeyhash": return "P2PKH";
			case "scripthash": return "P2SH";
			case "witness_v0_pubkeyhash": return "P2WPKH";
			case "witness_v0_scripthash": return "P2WSH";
			case "nulldata": return "NULLDATA";
		}
	}
	
	function get_script_sig_acronym($vin) {
		if(isset($vin->coinbase)) {
			return "COINBASE";
		} else {
			switch($vin->outpoint->scriptPubKey->type) {
				case "pubkeyhash": return "P2PKH";
				case "scripthash": {
					if(strpos($vin->scriptSig->hex, "160014") == 0) {
						return "P2SH_P2WPKH";
					} elseif (strpos($vin->scriptSig->hex, "220020") == 0) {
						return "P2SH_P2WSH";
					}
					return "P2SH";
				}
				case "witness_v0_pubkeyhash": return "P2WPKH";
				case "witness_v0_scripthash": return "P2WSH";
				case "nulldata": return "NULLDATA";
			}
		}
	}
	
	function get_script_sig_scripts($vin) {
		$ret = array();
		
		if(isset($vin->coinbase)) {
			array_push($ret, $vin->coinbase);
			array_push($ret, hex2bin($vin->coinbase));
		} else {
			if($vin->outpoint->scriptPubKey->type == "pubkeyhash" || $vin->outpoint->scriptPubKey->type == "scripthash") {
				array_push($ret, $vin->scriptSig->hex);
			}
			
			if(isset($vin->txinwitness)) {
				array_push($ret, implode("\n", $vin->txinwitness));
			}
		}
		
		return $ret;
	}
	
	function get_block_reward($height) {
		global $_CHAIN;
		
		$hx = floor($height / $_CHAIN->halving);
		return $_CHAIN->reward * pow(0.5, $hx);
	}
	
	function get_current_price() {
		global $_CHAIN, $_REDIS;
		
		//https://api.coinmarketcap.com/v2/ticker/988/
		$cmc_k = "hs:exp:cmc:ticker:" . $_CHAIN->cmc_id;
		
		$lp = $_REDIS->get($cmc_k);
		if($lp == false) {
			$cmc_raw = curl_get("https://api.coinmarketcap.com/v2/ticker/" . $_CHAIN->cmc_id . "/");
			$_REDIS->setEx($cmc_k, _TS_MIN, $cmc_raw);
			$lp = json_decode($cmc_raw);
		} else {
			$lp = json_decode($lp);
		}
		
		return $lp->data->quotes->USD->price;
	}
?>