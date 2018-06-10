<?php
	$n_blocks = 100;
	$blocks = get_last_n_blocks($n_blocks);
	$stat = get_pool_stats($blocks);
?>
<div class="page-header">
	<h1 class="page-title">Last <?= $n_blocks . " " . $_CHAIN->name ?> blocks</h1>
</div>

<!-- pool stats -->
<div class="row row-deck">
	<div class="col-4">
		<div class="card">
		  <div class="card-header">
			<h3 class="card-title">Pool Stats</h3>
		  </div>
		  <div class="card-body">
			<div id="pool-stats-chart-wrapper" style="height: 16rem"></div>
		  </div>
		</div>
	</div>
</div>
<div class="row row-deck">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">Blocks</h3>
			</div>
			<div class="table-responsive">
				<table class="table card-table table-vcenter text-nowrap">
					<thead>
						<tr>
							<th>Height</th>
							<th>Created By</th>
							<th>Txns</th>
							<th>Size</th>
							<th>Version</th>
							<th>Time</th>
							<th>Diff</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($blocks as $block) { ?>
							<tr>
								<td><a href="/block/<?= strtolower($_CHAIN_KEY) ?>/<?= $block->height ?>"><?= number_format($block->height) ?></a></td>
								<td><?= ($block->createdby != null ? "<a target=\"_blank\" href=\"" . $block->createdby->link . "\">" . $block->createdby->name . "</a>" : "") ?></td>
								<td><?= number_format(count($block->tx)) ?></td>
								<td><?= format_bytes($block->size) ?></td>
								<td>0x<?= $block->versionHex ?></td>
								<td><?= format_timespan($block->time) ?> ago</td>
								<td><?= format_metric($block->difficulty) ?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<script>
	var stat = <?= json_encode($stat) ?>;
	var pool_stat = [];
	
	for(pool in stat) {
		pool_stat.push([pool].concat(stat[pool]));
	}
	
	require(['c3', 'jquery'], function(c3, $) {
		$(document).ready(function(){
			var chart = c3.generate({
				bindto: '#pool-stats-chart-wrapper',
				data: {
					columns: pool_stat,
					type: 'pie'
				}
			});
		});
	});
</script>