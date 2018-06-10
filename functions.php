<?php
	//Time constants
	define("_TS_SEC", 1);
	define("_TS_MIN", _TS_SEC * 60);
	define("_TS_HOUR", _TS_MIN * 60);
	define("_TS_DAY", _TS_HOUR * 24);
	define("_TS_WEEK", _TS_DAY * 7);
	define("_TS_YEAR", _TS_DAY * 365);
	
	//crypto constants
	define("SAT", 0.00000001);
	
	function curl_post($url, $data, $headers = array()) 
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;	
	}
	
	function curl_get($url, $headers = array()) 
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;	
	}
	
	function http_rpc_query($method, $params, $cache_time = RPC_CACHE) {
		global $_CHAIN, $_REDIS;
		
		$req = json_encode(array("jsonrpc" => "1.0", "method" => $method, "params" => $params, "id" => 1));
		$headers = array(
			"Content-Type: text/plain",
			"Authorization: Basic " . base64_encode($_CHAIN->rpc_user . ":" . $_CHAIN->rpc_password)
		);
					
		if($cache_time == -1 ) {
			$res = json_decode(curl_post($_CHAIN->rpc_url, $req, $headers));
		} else {
			$cache_key = sprintf("hs:exp:%s:rpc-cache:%s:%s", $_CHAIN->name, $method, implode("-", $params));
			
			$cache = $_REDIS->get($cache_key);
			if($cache == false) {
				$raw = curl_post($_CHAIN->rpc_url, $req, $headers);
				
				if($cache_time == 0) {
					$_REDIS->set($cache_key, $raw);
				} else {
					$_REDIS->setEx($cache_key, $cache_time, $raw);
				}
			} else {
				$raw = $cache;
			}
			
			$res = json_decode($raw);
			if(isset($res->error) && $cache != false) {
				$_REDIS->del($cache_key); //reset the cache if there was an error
			}
		}
		
		return $res->result;
	}
	
	function get_url($path) {
		return sprintf(BASE_URL, $path);
	}
	
	function format_bytes($bytes, $prec = 2)
	{
		if ($bytes >= 1073741824) {
			return number_format($bytes / 1073741824, $prec) . ' GiB';
		} elseif ($bytes >= 1048576) {
			return number_format($bytes / 1048576, $prec) . ' MiB';
		} elseif ($bytes >= 1024) {
			return number_format($bytes / 1024, $prec) . ' KiB';
		}
		
		return $bytes . ' B';
	}
	
	function format_metric($val, $prec = 2) 
	{
		if($val >= 1e+18) {
			return number_format($val / 1e+18, $prec) . ' E';
		} elseif ($val >= 1e+15) {
			return number_format($val / 1e+15, $prec) . ' P';
		} elseif ($val >= 1e+12) {
			return number_format($val / 1e+12, $prec) . ' T';
		} elseif ($val >= 1e+9) {
			return number_format($val / 1e+9, $prec) . ' G';
		} elseif ($val >= 1e+6) {
			return number_format($val / 1e+6, $prec) . ' M';
		} elseif ($val >= 1e+3) {
			return number_format($val / 1e+3, $prec) . ' k';
		}
		
		return number_format($val, $prec);
	}

	function format_timespan($from, $prec = 0, $to = 0) {
		if($to == 0) {
			$to = time();
		}
		$dif = floatval($to) - floatval($from);
		
		if($dif >= _TS_YEAR) {
			return number_format($dif / _TS_YEAR, $prec) . " years";
		} elseif ($dif >= _TS_WEEK) {
			return number_format($dif / _TS_WEEK, $prec) . " weeks";
		} elseif ($dif >= _TS_DAY) { 
			return number_format($dif / _TS_DAY, $prec) . " days";
		} elseif ($dif >= _TS_HOUR) {
			return number_format($dif /_TS_HOUR, $prec) . " hours";
		} elseif ($dif >= _TS_MIN) {
			return number_format($dif / _TS_MIN, $prec) . " mins";
		} else {
			return number_format($dif, $prec) . " secs";
		}
	}
	
	function format_coin($val, $prec = 8) {
		return number_format($val, $prec);
	}
	
	function format_money($val) {
		if($val < 0.01) {
			return number_format($val, 4);
		} else {
			return number_format($val, 2);
		}
	}
?>