<?php
	include_once("config.php");
	include_once("functions.php");
	include_once("getinfo.php");
	
	//connect to redis
	$_REDIS = new Redis();
	$_REDIS->pconnect(REDIS_CONFIG);
	
	//take chain key from cookie (or default chain)
	$_CHAIN_KEY = isset($_COOKIE["chain"]) ? $_COOKIE["chain"] : DEFAULT_CHAIN;
	
	//setup page view
	$path = isset($_GET["path"]) ? $_GET["path"] : "/";
	$path_split = explode("/", substr($path, 1));
	
	if(count($path_split) > 0) {
		$view = $path_split[0];
		if(isset(PAGES[$view])) {
			$_VIEW = (object)PAGES[$view];
		}else {
			$_VIEW = (object)PAGES["home"];
		}
		
		//overwrite $_CHAIN_KEY if we have a path with a specific chain set
		if(count($path_split) > 1) {
			$_CHAIN_KEY = strtoupper($path_split[1]);
		}
	} else {
		$_VIEW = (object)PAGES["home"];
	}
	
	if($_CHAIN_KEY != null && isset(NODES_CONFIG[$_CHAIN_KEY])) {
		$_CHAIN = (object)NODES_CONFIG[$_CHAIN_KEY];
	}
	
	$_CHAIN->price = get_current_price();
?>

<!doctype html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?= $_CHAIN->name ?> Info</title>
		
		<!-- tabler -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,300i,400,400i,500,500i,600,600i,700,700i&amp;subset=latin-ext">
		<script src="https://cdn.rawgit.com/tabler/tabler/dev/dist/assets/js/require.min.js"></script>
		<script>
		  requirejs.config({
			  baseUrl: 'https://cdn.rawgit.com/tabler/tabler/dev/dist/'
		  });
		</script>
		<link href="https://cdn.rawgit.com/tabler/tabler/dev/dist/assets/css/dashboard.css" rel="stylesheet" />
		<link href="https://cdn.rawgit.com/tabler/tabler/dev/dist/assets/plugins/charts-c3/plugin.css" rel="stylesheet" />
		<link href="https://cdn.rawgit.com/tabler/tabler/dev/dist/assets/plugins/maps-google/plugin.css" rel="stylesheet" />
		
		<script src="https://cdn.rawgit.com/tabler/tabler/dev/dist/assets/js/dashboard.js"></script>
		<script src="https://cdn.rawgit.com/tabler/tabler/dev/dist/assets/plugins/charts-c3/plugin.js"></script>
		<script src="https://cdn.rawgit.com/tabler/tabler/dev/dist/assets/plugins/maps-google/plugin.js"></script>
		<script src="https://cdn.rawgit.com/tabler/tabler/dev/dist/assets/plugins/input-mask/plugin.js"></script>
		<style>
			@media (min-width: 1440px) {
			  .container {
				max-width: 1400px;
			  }
			}
		</style>
	</head>
	<body>
		<div class="page">
			<div class="page-main">
				<!-- Main header -->
				<div class="header py-4">
				  <div class="container">
					<div class="d-flex">
						<a class="header-brand" href="<?=get_url("/")?>">Hashstream Explorer</a>
						<div class="d-flex order-lg-2 ml-auto">
							<div class="nav-item d-none d-md-flex">
								<a href="https://github.com/hashstream/explorer" class="btn btn-sm btn-outline-primary" target="_blank">Source code</a>
							</div>
							<div class="nav-item d-none d-md-flex">
								<select class="form-control custom-select" onchange="document.cookie = 'chain=' + this.value + ';path=/'; window.location.href = '/';">
									<?php foreach(NODES_CONFIG as $k => $v) { ?>
										<option <?= ($k == $_CHAIN_KEY ? "selected" : "") ?> value="<?= $k ?>"><?= $v["name"] ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="nav-item d-none d-md-flex">
								<?= $_CHAIN_KEY . "/USD" ?> $<?= format_money($_CHAIN->price) ?>
							</div>
						</div>
						<a href="#" class="header-toggler d-lg-none ml-3 ml-lg-0" data-toggle="collapse" data-target="#headerMenuCollapse">
							<span class="header-toggler-icon"></span>
						</a>
					</div>
				  </div>
				</div>
				
				<!-- Sub header -->
				<div class="header collapse d-lg-flex p-0" id="headerMenuCollapse">
					<div class="container">
						<div class="row align-items-center">
							<div class="col-lg-3 ml-auto">
								<form class="input-icon my-3 my-lg-0">
									<input type="search" class="form-control header-search" placeholder="Search (tx/address/block..)" tabindex="1">
									<div class="input-icon-addon">
										<i class="fe fe-search"></i>
									</div>
								</form>
							</div>
							<div class="col-lg order-lg-first">
								<ul class="nav nav-tabs border-0 flex-column flex-lg-row">
									<?php foreach(PAGES as $page) { ?>
										<li class="nav-item">
											<a href="<?=get_url($page["path"] != "/" ? $page["path"] . "/" . strtolower($_CHAIN_KEY) : $page["path"])?>" class="nav-link<?=($_VIEW->name == $page["name"] ? " active" : "")?>"><i class="fe fe-home"></i> <?=$page["name"]?></a>
										</li>
									<?php } ?>
								</ul>
							</div>
						</div>
					</div>
				</div>
				
				<!-- Main content -->
				<div class="my-3 my-md-5">
					<div class="container">
						<?php
							include_once($_VIEW->view);
						?>
					</div>
				</div>
			</div>
		</div>
		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-120508668-1"></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());

		  gtag('config', 'UA-120508668-1');
		</script>
	</body>
</html>