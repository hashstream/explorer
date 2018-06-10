<?php

?>
<div class="card">
	<div class="row">
		<div class="col-12">
			<div class="card-header">
				<h3 class="card-title">Output Scripts</h3>
			</div>
			<div class="card-body">
			<?php foreach($tx->vout as $txout) { ?>
				<div class="row">
					<div class="col-md-1">
						<div class="text-muted my-md-3"><?= get_script_pubkey_acronym($txout) ?></div>
					</div>
					<div class="col-md-11">
						<pre><?= $txout->scriptPubKey->asm ?></pre>
					</div>
				</div>
			<?php } ?>
			</div>
		</div>
	</div>
</div>