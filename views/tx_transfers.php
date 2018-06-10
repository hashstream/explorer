<?php
	$isTxPage = $path_split[0] == "tx";
?>
<div class="card">
	<div class="row">
		<div class="col-12">
			<div class="card-header">
				<h3 class="card-title">
					<?php 
						if($isTxPage) {
							?> Transfers <?php
						} else {
							?> <a href="/tx/<?= strtolower($_CHAIN_KEY) ?>/<?= $tx->txid ?>"><?= $tx->txid ?></a> <?php
						}
					?>
				</h3>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-6">
						<?php foreach($tx->vin as $txin) {
							if(isset($txin->coinbase)) {
							?>
								<div style="overflow: hidden">
									<div class="btn btn-primary">Coinbase</div>
								</div>
							<?php
							} else {
								$txout = $txin->outpoint;
								include("tx_outpoint.php");
							}
						} ?>
					</div>
					<div class="col-md-6">
						<?php 
							foreach($tx->vout as $txout) {
								include("tx_outpoint.php");
							} 
						?>
						<div class="overflow: hidden">
							<div class="btn-list text-right">
								<div class="btn btn-primary"><?= format_coin($tx->value_out) ?></div>
								<div class="btn btn-secondary">$<?= format_money($tx->value_out * $_CHAIN->price) ?></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>