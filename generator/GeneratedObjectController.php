<?php

class GeneratedObjectController extends BaseController {
	function __construct () {
		// call parent constructor
		parent::__construct();

		// set model used by controller
		$this->model = new GeneratedObject();
	}

	function index () {
		// app user must be signed in & have perm
		$this->protect('signed_in', 'GeneratedObject_read');

		// render view
		$this->page_title = 'Generated Objects';
		$this->view('generated_object_index');
	}

	function datatable () {
		// app user must be signed in & have perm
		$this->protect('signed_in', 'GeneratedObject_read');

		// set datatable properties
		$this->datatable = new Datatable();
		// generator display columns
		// generator search columns
		$this->datatable->query = "select * from `generated_object`";
		$this->datatable->set_array();

		// render view
		$this->view('generated_object_datatable');
	}

	function create () {
		// app user must be signed in & have perm
		$this->protect('signed_in', 'GeneratedObject_create');

		// set validator rules
		$validator = new Validator();
		// generator rules

		// process form submission
		if (!empty($_POST)) {
			// set generated_object properties with desanitized data & validate
			$this->model->set_properties_by_array($this->desanitize($_POST));
			$validator->model = $this->model;

			if ($validator->rules_passed()) {
				// rules passed, create and redirect to index
				$this->model->create();
				AppUser::create_action('Created Generated Object', (array)$this->model);
				$this->set_alert('success', 'Generated Object created successfully');
				$this->redirect('generated_object');
			}
			else {
				// rules invalid, set error alert
				$this->set_alert('danger', $validator->errors);
			}
		}

		// render view
		$this->page_title = 'Create Generated Object';
		$this->view('generated_object_create');
	}

	function update ($id) {
		// app user must be signed in & have perm
		$this->protect('signed_in', 'GeneratedObject_update');
		$this->model->id = $id;

		if ($this->model->exists_by_id()) {
			// model exists, set properties
			$this->model->set_properties_by_id();
		}
		else {
			// model does not exist, redirect to index
			$this->redirect('generated_object');
		}

		// set validator rules
		$validator = new Validator();
		// generator rules

		// process form submission
		if (!empty($_POST)) {
			// set generated_object properties with desanitized data & validate
			$this->model->set_properties_by_array($this->desanitize($_POST));
			$validator->model = $this->model;

			if ($validator->rules_passed()) {
				// rules passed, update and redirect to index
				$this->model->update();
				AppUser::create_action('Updated Generated Object', (array)$this->model);
				$this->set_alert('success', 'Generated Object updated successfully');
				$this->redirect('generated_object');
			}
			else {
				// rules invalid, set error alert
				$this->set_alert('danger', $validator->errors);
			}
		}

		// render view
		$this->page_title = 'Update Generated Object';
		$this->view('generated_object_update');
	}

	function delete ($id) {
		// app user must be signed in & have perm
		$this->protect('signed_in', 'GeneratedObject_delete');
		$this->model->id = $id;

		if ($this->model->exists_by_id()) {
			// model exists, delete it
			$this->model->set_properties_by_id();
			$this->model->delete();
			AppUser::create_action('Deleted Generated Object', (array)$this->model);
			$this->set_alert('success', 'Generated Object deleted successfully');
		}

		// redirect to index
		$this->redirect('generated_object');
	}
}