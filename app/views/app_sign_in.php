<?php
$this->view('app_header');
?>

<form method="post">
	<div class="form-group has-feedback">
		<input name="username" type="text" class="form-control" placeholder="Username">
		<span class="fa fa-user form-control-feedback"></span>
	</div>
	<div class="form-group has-feedback">
		<input name="password" type="password" class="form-control" placeholder="Password">
		<span class="fa fa-lock form-control-feedback"></span>
	</div>
	<div class="form-group">
		<div class="checkbox"><label><input name="remember_me" type="checkbox"> Remember me</label></div>
	</div>
	<div class="form-group">
		<button type="submit" class="btn btn-primary btn-block btn-flat">Sign in</button>
	</div>
</form>

<a href="<?php echo $this->url; ?>/password_forgot">I forgot my password</a>

<?php
$this->view('app_footer');