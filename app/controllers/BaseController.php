<?php

class BaseController {
	public $url;
	public $page_title;
	public $alert = [];
	public $model;
	public $datatable;
	public $data;

	function __construct () {
		$this->set_url();
	}

	function set_url () {
		// set base app url
		$get_path = ($_GET['path']) ? $_GET['path'] : '';
		$full_url = 'http'.(($_SERVER['SERVER_PORT'] == 443) ? 's://' : '://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$this->url = rtrim(str_replace($get_path, '', $full_url), '/');
	}

	function protect ($authentication_required, $permission_required = null) {
		// protect controller method by app user auth and/or perm
		$block = false;
		$location = '';

		if ($authentication_required == 'signed_in' && !AppUser::$signed_in) {
			// app user must be signed in
			$block = true;
			$location = 'sign_in';
		}
		else if ($authentication_required == 'signed_out' && AppUser::$signed_in) {
			// app user must be signed out
			$block = true;
		}
		else if ($permission_required != null && !AppUser::has_permission($permission_required)) {
			// app user must have specific permission
			$block = true;
		}

		if ($block) {
			// app user is blocked, redirect to location
			$this->redirect($location);
		}
	}

	function redirect ($location) {
		// redirect and die afterwards to stop script execution
		header('Location: '.trim($this->url.'/'.$location, '/'));
		die();
	}

	function view ($file_name, $data = null) {
		$this->data = $data;
		$file = 'app/views/'.$file_name.'.php';

		if (file_exists($file)) {
			// file exists, render view by file name
			include $file;
		}
	}

	function set_alert ($class, $message) {
		// set session alert
		$_SESSION['alert'] = [
			'class' => $class,
			'message' => $message
		];
	}

	function alert () {
		// show session alert if set
		if (isset($_SESSION['alert'])) {
			$this->alert = $_SESSION['alert'];
			$this->view('app_alert');

			// unset afterwards so it only shows once
			unset($_SESSION['alert']);
		}
	}

	function sanitize ($data) {
		// sanitize array or string values for safe output
		if (is_array($data)) {
			foreach ($data as $key => &$value) {
				$value = $this->sanitize($value);
			}
		}
		else {
			$data = htmlspecialchars($data);
		}

		return $data;
	}

	function desanitize ($data) {
		// desanitize array or string values for db interactions
		if (is_array($data)) {
			foreach ($data as $key => &$value) {
				$value = $this->desanitize($value);
			}
		}
		else {
			$data = trim(htmlspecialchars_decode($data));
		}

		return $data;
	}
}