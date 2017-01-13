<?php

class Validator {
	public $rules = [];
	public $model;
	public $errors = [];

	function rules_passed () {
		// ensure each rule passed
		$rules_passed = true;

		foreach ($this->rules as $rule) {
			if (!$this->rule_passed($rule)) {
				// rule did not pass
				$rules_passed = false;
			}
		}

		// all rules passed
		return $rules_passed;
	}

	function rule_passed ($rule) {
		// ensure each condition passed
		$conditions = explode('|', $rule[2]);

		foreach ($conditions as $condition) {
			if (!$this->condition_passed($rule[0], $rule[1], $condition)) {
				// condition did not pass
				return false;
			}
		}

		// all conditions passed
		return true;
	}

	function condition_passed ($label, $property, $condition) {
		// set parameters
		$parameters = explode(':', $condition);

		switch ($parameters[0]) {
			case 'required' :
				// value is required
				if (strlen($this->model->$property) > 0) {
					return true;
				}
				else {
					$this->errors[] = $label.' is required';
					return false;
				}
				break;
			case 'not_empty' :
				// value must not be empty
				if (!empty($this->model->$property)) {
					return true;
				}
				else {
					$this->errors[] = $label.' must not be empty';
					return false;
				}
				break;
			case 'in_list' :
				// value must be in comma-seperated list
				$list = array_map('trim', explode(',', $parameters[1]));

				if (in_array($this->model->$property, $list)) {
					return true;
				}
				else {
					$this->errors[] = $label.' must be in '.implode(', ', $list);
					return false;
				}
				break;
			case 'not_in_list' :
				// value must not be in comma-seperated list
				$list = array_map('trim', explode(',', $parameters[1]));

				if (!in_array($this->model->$property, $list)) {
					return true;
				}
				else {
					$this->errors[] = $label.' must not be in '.implode(', ', $list);
					return false;
				}
				break;
			case 'email_address' :
				// value must be a valid email address
				if (filter_var($this->model->$property, FILTER_VALIDATE_EMAIL)) {
					return true;
				}
				else {
					$this->errors[] = $label.' must be a valid email address';
					return false;
				}
				break;
			case 'number' :
				// value must be a number
				if (ctype_digit($this->model->$property)) {
					return true;
				}
				else {
					$this->errors[] = $label.' must be a number';
					return false;
				}
				break;
			case 'unique' :
				// value must be unique to db table
				if (isset($parameters[2])) {
					// exception set
					$stm = DB::$pdo->prepare("select count(*) from {$parameters[1]} where {$property}=:property and {$property} != :exception");
					$stm->bindParam(':exception', $parameters[2]);
				}
				else {
					// exception not set
					$stm = DB::$pdo->prepare("select count(*) from {$parameters[1]} where {$property}=:property");
				}

				$stm->bindParam(':property', $this->model->$property);
				$stm->execute();
				$res = $stm->fetchColumn();

				if ($res == 0) {
					return true;
				}
				else {
					$this->errors[] = $label.' must be unique';
					return false;
				}
				break;
		}

		return false;
	}
}