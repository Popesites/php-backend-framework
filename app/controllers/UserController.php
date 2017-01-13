<?php

class UserController extends BaseController {
	function __construct () {
		// call parent constructor
		parent::__construct();

		// set model used by controller
		$this->model = new User();
	}

	function index () {
		// app user must be signed in & have perm
		$this->protect('signed_in', 'User_read');

		// render view
		$this->page_title = 'Users';
		$this->view('user_index');
	}

	function datatable () {
		// app user must be signed in & have perm
		$this->protect('signed_in', 'User_read');

		// set datatable properties
		$this->datatable = new Datatable();
		$this->datatable->display_columns = ['username', 'email_address', 'timezone', 'level'];
		$this->datatable->search_columns = ['username', 'email_address', 'timezone', 'level'];
		$this->datatable->query = "select * from `user`";
		$this->datatable->set_array();

		// render view
		$this->view('user_datatable');
	}

	function create () {
		// app user must be signed in & have perm
		$this->protect('signed_in', 'User_create');

		// set validator rules
		$validator = new Validator();
		$validator->rules[] = ['Username', 'username', 'required|unique:user'];
		$validator->rules[] = ['Email Address', 'email_address', 'required|email_address|unique:user'];
		$validator->rules[] = ['Password', 'password', 'required'];
		$validator->rules[] = ['Timezone', 'timezone', 'required|in_list:'.implode(',', timezone_identifiers_list())];
		$validator->rules[] = ['Level', 'level', 'required|in_list:Admin,Standard'];

		// process form submission
		if (!empty($_POST)) {
			// set object properties with desanitized data & validate
			$this->model->set_properties_by_array($this->desanitize($_POST));
			$validator->model = $this->model;

			if ($validator->rules_passed()) {
				// rules passed, create and redirect to index
				$this->model->create();
				AppUser::create_action('Created user', (array)$this->model);
				$this->set_alert('success', 'User created successfully');
				$this->redirect('user');
			}
			else {
				// rules invalid, set error alert
				$this->set_alert('danger', $validator->errors);
			}
		}

		// render view
		$this->page_title = 'Create User';
		$this->view('user_create');
	}

	function update ($id) {
		// app user must be signed in & have perm
		$this->protect('signed_in', 'User_update');
		$this->model->id = $id;

		if ($this->model->exists_by_id()) {
			// model exists, set properties
			$this->model->set_properties_by_id();
		}
		else {
			// model does not exist, redirect to index
			$this->redirect('user');
		}

		// set validator rules
		$validator = new Validator();
		$validator->rules[] = ['Username', 'username', 'required|unique:user:'.$this->model->username];
		$validator->rules[] = ['Email Address', 'email_address', 'required|email_address|unique:user:'.$this->model->email_address];
		$validator->rules[] = ['Timezone', 'timezone', 'required|in_list:'.implode(',', timezone_identifiers_list())];
		$validator->rules[] = ['Level', 'level', 'required|in_list:Admin,Standard'];

		// process form submission
		if (!empty($_POST)) {
			// set object properties with desanitized data & validate
			$this->model->set_properties_by_array($this->desanitize($_POST));
			$validator->model = $this->model;

			if ($validator->rules_passed()) {
				// rules passed, update and redirect to index
				$this->model->update();

				// change password & delete toens if new one entered
				if (!empty($this->model->password)) {
					$this->model->update_password();
					$this->model->delete_tokens();
				}

				AppUser::create_action('Updated user', (array)$this->model);
				$this->set_alert('success', 'User updated successfully');
				$this->redirect('user');
			}
			else {
				// rules invalid, set error alert
				$this->set_alert('danger', $validator->errors);
			}
		}

		// render view
		$this->page_title = 'Update User';
		$this->view('user_update');
	}

	function delete ($id) {
		// app user must be signed in & have perm
		$this->protect('signed_in', 'User_delete');
		$this->model->id = $id;

		if ($this->model->exists_by_id()) {
			// model exists, delete it
			$this->model->set_properties_by_id();
			$this->model->delete();
			AppUser::create_action('Deleted user', (array)$this->model);
			$this->set_alert('success', 'User deleted successfully');
		}

		// redirect to index
		$this->redirect('user');
	}

	function action ($id) {
		// app user must be signed in & have perm
		$this->protect('signed_in', 'User_read');
		$this->model->id = $id;

		if (!$this->model->exists_by_id()) {
			// model does not exist, redirect to index
			$this->redirect('user');
		}

		// render view
		$this->page_title = 'User Actions';
		$this->view('user_action');
	}

	function action_datatable ($id) {
		// app user must be signed in & have perm
		$this->protect('signed_in', 'User_read');
		$this->model->id = $id;

		if ($this->model->exists_by_id()) {
			// model exists, set datatable properties
			$this->datatable = new Datatable();
			$this->datatable->display_columns = ['action', 'date_acted'];
			$this->datatable->search_columns = ['action', 'data'];
			$this->datatable->query = "select * from `user_action` where `user_id`={$id}";
			$this->datatable->set_array();

			// render view
			$this->view('user_action_datatable');
		}
	}

	function action_data ($id) {
		// app user must be signed in & have perm
		$this->protect('signed_in', 'User_read');

		if ($this->model->action_exists_by_id($id)) {
			// action data exists, set view data
			$data = $this->model->fetch_action_data($id);

			// render view
			$this->view('user_action_data', $data);
		}
	}
}