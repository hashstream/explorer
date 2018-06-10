<?php
	$block_height_or_hash = $path_split[2];
	$hash = strlen($block_height_or_hash) != 64 ? get_block_hash(intval($block_height_or_hash)) : $block_height_or_hash;
	
	$block = get_block($hash);
	$txns = get_txns($block->tx, 100);
?>
<div class="card card-collapsed">
	<div class="card-header">
		<h3 class="card-title">JSON</h3>
		<div class="card-options">
			<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
		</div>
	</div>
	<div class="card-body">
		<pre><?= json_encode($block, JSON_PRETTY_PRINT) ?></pre>
	</div>
</div>

<div class="card">
	<div class="row">
		<div class="col-12">
			<div class="card-header">
				<h3 class="card-title">Block <?= number_format($block->height) ?></h3>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-6">
						<div style="overflow: hidden">
							<div class="text-muted float-left">Hash</div>
							<div class="float-right"><?= $block->hash ?></div>
						</div>
						<div style="overflow: hidden">
							<div class="text-muted float-left">Merkle Root</div>
							<div class="float-right"><?= $block->merkleroot ?></div>
						</div>
						<div style="overflow: hidden">
							<div class="text-muted float-left">Size</div>
							<div class="float-right"><?= format_bytes($block->size) ?></div>
						</div>
						<div style="overflow: hidden">
							<div class="text-muted float-left">Difficulty</div>
							<div class="float-right"><?= format_metric($block->difficulty) ?></div>
						</div>
						<div style="overflow: hidden">
							<div class="text-muted float-left">Time</div>
							<div class="float-right"><?= format_timespan($block->time) ?> ago</div>
						</div>
					</div>
					<div class="col-md-6">
						<div style="overflow: hidden">
							<div class="text-muted float-left">Bits</div>
							<div class="float-right">0x<?= $block->bits ?></div>
						</div>
						<div style="overflow: hidden">
							<div class="text-muted float-left">Nonce</div>
							<div class="float-right">0x<?= str_pad(dechex($block->nonce), 8, "0", STR_PAD_LEFT) ?></div>
						</div>
						<div style="overflow: hidden">
							<div class="text-muted float-left">Target</div>
							<div class="float-right"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php 
	foreach($txns as $tx) {
		include("tx_transfers.php");
	} 
?>