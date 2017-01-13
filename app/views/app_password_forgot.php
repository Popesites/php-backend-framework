<?php
$this->view('app_header');
?>

<form method="post">
	<div class="form-group has-feedback">
		<input name="email_address" type="email" class="form-control" placeholder="Email address">
		<span class="fa fa-envelope form-control-feedback"></span>
	</div>
	<div class="form-group">
		<button type="submit" class="btn btn-primary btn-block btn-flat">Send reset link</button>
	</div>
</form>

<a href="<?php echo $this->url; ?>/sign_in">I remember my password</a>

<?php
$this->view('app_footer');