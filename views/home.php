<?php
	$info_cards = get_home_info_cards();
	$peers = get_peer_info();
?>
<div class="page-header">
	<h1 class="page-title"><?= $_CHAIN->name ?> Dashboard</h1>
</div>
<?php
	if(isset($network_info->warnings)) { ?>
		<div class="alert alert-icon alert-danger" role="alert">
			<i class="fe fe-alert-triangle mr-2" aria-hidden="true"></i><?= $network_info->warnings ?>
		</div>
	<?php }
?>
<div class="row row-cards">
	<?php
		foreach($info_cards as $info_card_name => $info_card_value) { ?>
			<div class="col-3">
				<div class="card">
					<div class="card-body p-3 text-center">
						<div class="h1 m-0"><?= $info_card_value ?></div>
						<div class="text-muted mb-4"><?= $info_card_name ?></div>
					</div>
				</div>
			</div>
		<?php
		}
	?>
</div>
<div class="row row-deck">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">Peers</h3>
			</div>
			<div class="table-responsive">
				<table class="table card-table table-vcenter text-nowrap">
					<thead>
						<tr>
							<th><?= implode("</th><th>", $peers->headers) ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($peers->rows as $peer) {
								echo "<tr><td>" . implode("</td><td>", $peer) . "</td></tr>";
							}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>