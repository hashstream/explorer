<?php
	$txid = $path_split[2];
	$tx = get_raw_tx($txid, true);
?>
<div class="card card-collapsed">
	<div class="card-header">
		<h3 class="card-title">JSON</h3>
		<div class="card-options">
			<a href="#" class="card-options-collapse" data-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a>
		</div>
	</div>
	<div class="card-body">
		<pre><?= json_encode($tx, JSON_PRETTY_PRINT) ?></pre>
	</div>
</div>

<div class="card">
	<div class="row">
		<div class="col-12">
			<div class="card-header">
				<h3 class="card-title">Info</h3>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-6">
						<div style="overflow: hidden">
							<div class="text-muted float-left">Txid</div>
							<div class="float-right"><?= $tx->txid ?></div>
						</div>
						<div style="overflow: hidden">
							<div class="text-muted float-left">Size</div>
							<div class="float-right"><?= format_bytes($tx->size) ?></div>
						</div>
						<div style="overflow: hidden">
							<div class="text-muted float-left">Weight</div>
							<div class="float-right"><?= number_format($tx->weight) ?></div>
						</div>
						<div style="overflow: hidden">
							<div class="text-muted float-left">Time</div>
							<div class="float-right"><?= format_timespan($tx->time) ?> ago</div>
						</div>
					</div>
					<div class="col-md-6">
						<div style="overflow: hidden">
							<div class="text-muted float-left">Value In</div>
							<div class="float-right"><?= format_coin($tx->value_in) ?></div>
						</div>
						<div style="overflow: hidden">
							<div class="text-muted float-left">Value Out</div>
							<div class="float-right"><?= format_coin($tx->value_out) ?></div>
						</div>
						<div style="overflow: hidden">
							<div class="text-muted float-left">Fees</div>
							<div class="float-right"><?= format_coin($tx->fees) ?></div>
						</div>
						<div style="overflow: hidden">
							<div class="text-muted float-left">Fee per byte</div>
							<div class="float-right"><?= format_coin($tx->sat_per_byte, 2) ?> sat/B</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
	include("tx_transfers.php");
	include("tx_script_sig.php");
	include("tx_pubkey_script.php");
?>