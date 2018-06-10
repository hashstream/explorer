<?php
	$isTx = count($path_split) > 2;
	if($isTx) {
		include_once("tx.php");
	} else {
		include_once("tx_stats.php");
	}
?>