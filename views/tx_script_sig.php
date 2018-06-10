<div class="card">
	<div class="row">
		<div class="col-12">
			<div class="card-header">
				<h3 class="card-title">Input Scripts</h3>
			</div>
			<div class="card-body">
			<?php foreach($tx->vin as $txin) { 
					$in_type = get_script_sig_acronym($txin);
					$sigs = get_script_sig_scripts($txin);
			?>
				<div class="row">
					<div class="col-1">
						<div class="text-muted my-md-3"><?= $in_type ?></div>
					</div>
					<div class="col-11">
						<?php foreach($sigs as $sig) { ?>
							<pre><?= $sig ?></pre>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
			</div>
		</div>
	</div>
</div>