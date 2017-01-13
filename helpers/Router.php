<?php

class Router {
	public $app_methods = ['sign_in', 'password_forgot', 'password_reset', 'dashboard', 'update_profile', 'sign_out', 'config'];
	public $app_method = false;
	public $path;
	public $controller = 'AppController';
	public $method = 'index';
	public $params = [];

	function __construct () {
		$this->set_path();
		$this->set_app_method();
		$this->set_controller();
		$this->set_method();
		$this->set_params();
	}

	function set_path () {
		// controller/method/params or app_method/params
		$this->path = (isset($_GET['path'])) ? strtolower(trim($_GET['path'], '/')) : '';
		$this->path = explode('/', $this->path);
	}

	function set_app_method () {
		if (isset($this->path[0]) && in_array($this->path[0], $this->app_methods)) {
			$this->app_method = true;
		}
	}

	function set_controller () {
		if (!$this->app_method && !empty($this->path[0])) {
			// dynamic controller class
			$this->controller = ucfirst($this->path[0]).'Controller';
		}
	}

	function set_method () {
		if ($this->app_method) {
			// app method
			$this->method = $this->path[0];
		}
		else if (!$this->app_method && isset($this->path[1])) {
			// dynamic method
			$this->method = $this->path[1];
		}
	}

	function set_params () {
		if ($this->app_method && isset($this->path[1])) {
			// app method, params are first element
			$this->params = array_slice($this->path, 1);
		}
		else if (!$this->app_method && isset($this->path[2])) {
			// non app method, params are 2nd element
			$this->params = array_slice($this->path, 2);
		}
	}

	function route () {
		// show 404 if class/method invalid
		if (!method_exists($this->controller, $this->method)) {
			$this->controller = 'AppController';
			$this->method = 'four_zero_four';
		}

		// set controller object
		$controller_object = new $this->controller();

		if (!empty($this->params)) {
			// params not empty, call method with them
			@call_user_func_array([$controller_object, $this->method], $this->params);
		}
		else {
			// call method with no params
			@call_user_func([$controller_object, $this->method]);
		}
	}
}