<?php
$this->view('app_header');
?>

<div class="box">
	<form method="post">
		<div class="box-body">
			<div class="form-group">
				<label for="email_address" class="control-label">Email address</label>
				<input name="email_address" id="email_address" type="email" class="form-control" value="<?php echo $this->sanitize(AppUser::$user->email_address); ?>">
			</div>
			<div class="form-group">
				<label for="password" class="control-label">New password</label>
				<input name="password" id="password" type="password" class="form-control">
			</div>
			<div class="form-group">
				<label for="timezone" class="control-label">Timezone</label>
				<select name="timezone" id="timezone" class="form-control">
					<?php
					$timezones = timezone_identifiers_list();

					foreach ($timezones as $timezone) {
						echo '<option value="'.$timezone.'"'.((AppUser::$user->timezone == $timezone) ? ' selected' : '').'>'.$timezone.'</option>';
					}
					?>
				</select>
			</div>
		</div>
		<div class="box-footer">
			<button type="submit" class="btn btn-primary btn-flat">Submit</button>
		</div>
	</form>
</div>

<?php
$this->view('app_footer');