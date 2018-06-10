<div style="overflow: hidden">
	<div class="float-left">
		<?php 
			if($txout->scriptPubKey->type == "nulldata") {
				if(strpos($txout->scriptPubKey->hex, "aa21a9ed") !== false) {
					?> <span class="tag">Segwit Commitment</span> <?php
				} else {
					?> nulldata <?php
				}
			} else {
				?> <a href="/address/<?= strtolower($_CHAIN_KEY) ?>/<?= $txout->scriptPubKey->addresses[0] ?>"><?= $txout->scriptPubKey->addresses[0] ?></a> <?php
			}
		?>
	</div>
	<div class="float-right"><?= ($txout->scriptPubKey->type != "nulldata" ? format_coin($txout->value) : "") ?></div>
</div>