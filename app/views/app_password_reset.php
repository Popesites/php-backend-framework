<?php
$this->view('app_header');
?>

<form method="post">
	<div class="form-group has-feedback">
		<input name="password" type="password" class="form-control" placeholder="New password">
		<span class="fa fa-lock form-control-feedback"></span>
	</div>
	<div class="form-group">
		<button type="submit" class="btn btn-primary btn-block btn-flat">Reset password</button>
	</div>
</form>

<a href="<?php echo $this->url; ?>/sign_in">I remember my password</a>

<?php
$this->view('app_footer');