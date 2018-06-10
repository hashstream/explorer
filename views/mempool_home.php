<?php
	$mempool = get_mempool();
?>
<div class="row row-deck">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">Mempool</h3>
			</div>
			<?php if($mempool == null || count($mempool) == 0) { ?>
				<div class="card-alert alert alert-danger mb-0">
					Mempool is empty
				</div>
			<?php } ?>
			<div class="table-responsive">
				<table class="table card-table table-vcenter text-nowrap">
					<thead>
						<tr>
							<th>txid</th>
							<th>size</th>
							<th>fee</th>
							<th>sat/B</th>
							<th>volume</th>
							<th>value</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($mempool as $txid => $tx) { ?>
							<tr>
								<td><a href="/tx/<?= strtolower($_CHAIN_KEY) ?>/<?= $txid ?>"><?= $txid ?></a></td>
								<td><?= format_bytes($tx->size) ?></td>
								<td><?= format_coin($tx->fees) ?></td>
								<td><?= number_format($tx->sat_per_byte, 2) ?></td>
								<td><?= format_coin($tx->value_out) ?></td>
								<td>$<?= format_money($tx->value_out * $_CHAIN->price) ?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>