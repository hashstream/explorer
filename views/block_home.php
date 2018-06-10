<?php
	$isBlock = count($path_split) > 2;
	if($isBlock) {
		include_once("block.php");
	} else {
		include_once("recent_blocks.php");
	}
?>