<?php
$this->view('app_header');
?>

<div class="box">
	<form method="post">
		<div class="box-body">
			<div class="form-group">
				<label for="username" class="control-label">Username</label>
				<input name="username" id="username" type="text" class="form-control" value="<?php echo $this->sanitize($this->model->username); ?>">
			</div>
			<div class="form-group">
				<label for="email_address" class="control-label">Email address</label>
				<input name="email_address" id="email_address" type="email" class="form-control" value="<?php echo $this->sanitize($this->model->email_address); ?>">
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
						echo '<option value="'.$timezone.'"'.(($this->model->timezone == $timezone) ? ' selected' : '').'>'.$timezone.'</option>';
					}
					?>
				</select>
			</div>
			<div class="form-group">
				<label for="level" class="control-label">Level</label>
				<select name="level" id="level" class="form-control" data-showhide=".permissions">
					<option value="Admin"<?php if ($this->model->level == 'Admin') echo ' selected'; ?>>Admin</option>
					<option value="Standard" data-show=".permissions"<?php if ($this->model->level == 'Standard') echo ' selected'; ?>>Standard</option>
				</select>
			</div>
			<div class="form-group permissions<?php if ($this->model->level != 'Standard') echo ' collapse'; ?>">
				<label class="control-label">Permissions</label>
				<button type="button" data-check="permissions[]" class="btn btn-default btn-flat btn-xs">Check All</button>
				<button type="button" data-uncheck="permissions[]" class="btn btn-default btn-flat btn-xs">Uncheck All</button><br>
				<ul class="list-group">
					<?php
					$user_permissions = json_decode(USER_PERMISSIONS, true);

					foreach ($user_permissions as $object => $permissions) {
						?>
						<li class="list-group-item">
							<strong><?php echo $object; ?></strong><br>
							<?php
							foreach ($permissions as $permission) {
								$value = $object.'_'.$permission;
								$checked = (in_array($value, $this->model->permissions)) ? ' checked' : '';

								echo '<label class="checkbox-inline"><input type="checkbox" name="permissions[]" value="'.$value.'"'.$checked.'>'.$permission.'</label>';
							}
							?>
						</li>
						<?php
					}
					?>
				</ul>
			</div>
		</div>
		<div class="box-footer">
			<button type="submit" class="btn btn-primary btn-flat">Submit</button>
		</div>
	</form>
</div>

<?php
$this->view('app_footer');