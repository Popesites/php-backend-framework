<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php echo $this->page_title.' | '.Config::$value['title']; ?></title>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

	<!-- CSS -->
	<link rel="stylesheet" href="<?php echo $this->url; ?>/public/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo $this->url; ?>/public/css/font-awesome.min.css">
	<link rel="stylesheet" href="<?php echo $this->url; ?>/public/css/AdminLTE.min.css">
	<link rel="stylesheet" href="<?php echo $this->url; ?>/public/css/skin-blue.min.css">
	<link rel="stylesheet" href="<?php echo $this->url; ?>/public/css/datatables.min.css">
	<link rel="stylesheet" href="<?php echo $this->url; ?>/public/css/datatables-font-awesome.min.css">
	<link rel="stylesheet" href="<?php echo $this->url; ?>/public/css/custom.css?version=<?php echo VERSION; ?>">

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="<?php echo $this->url; ?>/public/js/html5shiv.min.js"></script>
	<script src="<?php echo $this->url; ?>/public/js/respond.min.js"></script>
	<![endif]-->
</head>
<body class="hold-transition skin-blue<?php if (!AppUser::$signed_in) echo ' login-page'; ?>">
<?php if (AppUser::$signed_in) { ?>
	<div class="wrapper">
		<header class="main-header">
			<a href="<?php echo $this->url; ?>" class="logo"><?php echo Config::$value['title']; ?></a>
			<nav class="navbar navbar-static-top" role="navigation">
				<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button"><span class="sr-only">Toggle navigation</span></a>
				<div class="navbar-custom-menu">
					<ul class="nav navbar-nav">
						<li><a href="<?php echo $this->url; ?>/update_profile"><i class="fa fa-user"></i> <?php echo $this->sanitize(AppUser::$user->username); ?></a></li>
						<li><a href="<?php echo $this->url; ?>/sign_out"><i class="fa fa-sign-out"></i> Sign Out</a></li>
					</ul>
				</div>
			</nav>
		</header>

		<aside class="main-sidebar">
			<section class="sidebar">
				<ul class="sidebar-menu">
					<li<?php if ($this->page_title == 'Dashboard') echo ' class="active"'; ?>><a href="<?php echo $this->url; ?>"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
					<?php
					if (AppUser::has_permission('App_config')) {
						echo '<li'.(($this->page_title == 'Config') ? ' class="active"' : '').'><a href="'.$this->url.'/config"><i class="fa fa-cog"></i> <span>Config</span></a></li>';
					}
					if (AppUser::has_permission('User_read')) {
						echo '<li'.(($this->page_title == 'Users') ? ' class="active"' : '').'><a href="'.$this->url.'/user"><i class="fa fa-users"></i> <span>Users</span></a></li>';
					}
					// generator header menu hook
					?>
				</ul>
			</section>
		</aside>

		<div class="content-wrapper">
			<section class="content-header">
				<h1><?php echo $this->page_title; ?></h1>
			</section>
			<section class="content">
<?php } else { ?>
	<div class="login-box">
		<div class="login-logo">
			<a href="<?php echo $this->url; ?>"><?php echo Config::$value['title']; ?></a>
		</div>
		<div class="login-box-body">
<?php }

$this->alert();