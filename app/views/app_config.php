<?php
$this->view('app_header');
?>

<div class="box">
	<form method="post">
		<div class="box-body">
			<div class="form-group">
				<label for="title" class="control-label">Title</label>
				<input name="title" id="title" type="text" class="form-control" value="<?php echo $this->sanitize(Config::$value['title']); ?>">
			</div>
			<div class="form-group">
				<label for="default_timezone" class="control-label">Default Timezone</label>
				<select name="default_timezone" id="default_timezone" class="form-control">
					<?php
					$timezones = timezone_identifiers_list();

					foreach ($timezones as $timezone) {
						echo '<option value="'.$timezone.'"'.(($timezone == Config::$value['default_timezone']) ? ' selected' : '').'>'.$timezone.'</option>';
					}
					?>
				</select>
			</div>
			<div class="form-group">
				<label for="date_format" class="control-label">Date format</label>
				<input name="date_format" id="date_format" type="text" class="form-control" value="<?php echo $this->sanitize(Config::$value['date_format']); ?>">
				<p class="help-block">
					The <a href="http://php.net/manual/en/function.date.php" target="_blank">date format</a> the system uses to display date and time
				</p>
			</div>
			<div class="form-group">
				<label for="remember_time" class="control-label">Remember time</label>
				<input name="remember_time" id="remember_time" type="text" class="form-control" value="<?php echo $this->sanitize(Config::$value['remember_time']); ?>">
				<p class="help-block">
					How long to remember a users credentials if they choose to be remembered e.g. 1 day, 3 months, 1 year
				</p>
			</div>
			<div class="form-group">
				<label for="lockout_time" class="control-label">Lockout time</label>
				<input name="lockout_time" id="lockout_time" type="text" class="form-control" value="<?php echo $this->sanitize(Config::$value['lockout_time']); ?>">
				<p class="help-block">
					How long to lockout a user for failing to sign in or reset password too many times e.g. 30 seconds, 15 minutes, 1 hour
				</p>
			</div>
			<div class="form-group">
				<label for="allowed_attempts" class="control-label">Allowed attempts</label>
				<input name="allowed_attempts" id="allowed_attempts" type="number" class="form-control" value="<?php echo $this->sanitize(Config::$value['allowed_attempts']); ?>">
				<p class="help-block">
					How many attempts a user is allowed to sign in or reset password before they're locked out
				</p>
			</div>
			<div class="form-group">
				<label for="password_reset_expire" class="control-label">Password reset expire</label>
				<input name="password_reset_expire" id="password_reset_expire" type="text" class="form-control" value="<?php echo $this->sanitize(Config::$value['password_reset_expire']); ?>">
				<p class="help-block">
					How long before a users password reset link expires and can no longer be used e.g. 30 minutes, 2 hours, 1 day
				</p>
			</div>
		</div>
		<div class="box-footer">
			<button type="submit" class="btn btn-primary btn-flat">Submit</button>
		</div>
	</form>
</div>

<?php
$this->view('app_footer');