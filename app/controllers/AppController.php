<?php

class AppController extends BaseController {
	function index () {
		// redirect app user based on auth
		if (AppUser::$signed_in) {
			$this->redirect('dashboard');
		}
		else {
			$this->redirect('sign_in');
		}
	}

	function four_zero_four () {
		// page not found, render view
		$this->page_title = '404';
		$this->view('app_404');
	}

	function sign_in () {
		// app user must be signed out
		$this->protect('signed_out');

		// process form submission
		if (!empty($_POST)) {
			// set object properties with desanitized data
			AppUser::$user->set_properties_by_array($this->desanitize($_POST));

			if (AppUser::locked_out('sign_in')) {
				// user is locked out, set alert
				$this->set_alert('danger', 'Locked out due to too many failed attempts');
			}
			else if (!AppUser::credentials_valid()) {
				// credentials invalid, create attempt & set error alert
				AppUser::create_attempt('sign_in');
				$this->set_alert('danger', 'Invalid username and/or password');
			}
			else {
				// not locked out & credentials valid, sign user in and send to dashboard
				$remember_me = (isset($_POST['remember_me'])) ? $_POST['remember_me'] : '';
				AppUser::sign_in($remember_me);
				AppUser::delete_attempts('sign_in');
				AppUser::create_action('Signed in');
				$this->redirect('dashboard');
			}
		}

		// render view
		$this->page_title = 'Sign In';
		$this->view('app_sign_in');
	}

	function password_forgot () {
		// app user must be signed out
		$this->protect('signed_out');

		// process form submission
		if (!empty($_POST)) {
			// set object properties with desanitized data
			AppUser::$user->email_address = $this->desanitize($_POST['email_address']);

			if (AppUser::locked_out('password_forgot')) {
				// user is locked out, set alert
				$this->set_alert('danger', 'Locked out due to too many failed attempts');
			}
			else if (!AppUser::$user->exists_by_email_address()) {
				// email address invalid, create attempt
				AppUser::create_attempt('password_forgot');
			}
			else {
				// not locked out & email address valid, send reset link
				AppUser::send_password_reset_link($this->url);
				AppUser::delete_attempts('password_forgot');
			}

			$this->set_alert('success', 'If email address valid, reset link sent');
		}

		// render view
		$this->page_title = 'Password Forgot';
		$this->view('app_password_forgot');
	}

	function password_reset ($email_address, $token) {
		// app user must be signed out
		$this->protect('signed_out');

		// ensure token is valid
		AppUser::$user->email_address = urldecode($email_address);

		if (!AppUser::password_reset_token_exists($token)) {
			// token is invalid, redirect them
			$this->set_alert('danger', 'Password reset token expired or invalid');
			$this->redirect('sign_in');
		}
		else if (isset($_POST['password']) && !empty($_POST['password'])) {
			// new password entered, reset it
			AppUser::$user->password = $this->desanitize($_POST['password']);
			AppUser::reset_password();
			$this->set_alert('success', 'Password reset successfully');
			$this->redirect('sign_in');
		}

		// render view
		$this->page_title = 'Password Reset';
		$this->view('app_password_reset');
	}

	function dashboard () {
		// app user must be signed in
		$this->protect('signed_in');

		// render view
		$this->page_title = 'Dashboard';
		$this->view('app_dashboard');
	}

	function update_profile () {
		// app user must be signed in
		$this->protect('signed_in');

		// set validator rules
		$validator = new Validator();
		$validator->rules[] = ['Email Address', 'email_address', 'required|email_address|unique:user:'.AppUser::$user->email_address];
		$validator->rules[] = ['Timezone', 'timezone', 'required|in_list:'.implode(',', timezone_identifiers_list())];

		// process form submission
		if (!empty($_POST)) {
			// set object properties with desanitized data & validate
			AppUser::$user->set_properties_by_array($this->desanitize($_POST));
			$validator->model = AppUser::$user;

			if ($validator->rules_passed()) {
				// rules passed, update
				AppUser::update_profile();

				// change password & delete tokens if new one entered
				if (!empty(AppUser::$user->password)) {
					AppUser::$user->update_password();
					AppUser::$user->delete_tokens();
					$this->redirect('sign_in');
				}

				AppUser::create_action('Updated profile', (array)AppUser::$user);
				$this->set_alert('success', 'Profile updated successfully');
			}
			else {
				// rules invalid, set error alert
				$this->set_alert('danger', $validator->errors);
			}
		}

		// render view
		$this->page_title = 'Update Profile';
		$this->view('app_update_profile');
	}

	function sign_out () {
		// app user must be signed in
		$this->protect('signed_in');

		// sign the app user out
		AppUser::sign_out();
		AppUser::create_action('Signed out');

		// redirect to sign in page
		$this->redirect('sign_in');
	}

	function config () {
		// app user must be signed in & have perm
		$this->protect('signed_in', 'App_config');

		// process form submission
		if (!empty($_POST)) {
			// update config
			Config::$value = $this->desanitize($_POST);
			Config::update();
			AppUser::create_action('Updated config', Config::$value);
			$this->set_alert('success', 'Config updated successfully');
		}

		// render view
		$this->page_title = 'Config';
		$this->view('app_config');
	}
}