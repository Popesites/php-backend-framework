<?php

class User {
	public $id;
	public $username;
	public $email_address;
	public $password;
	public $password_hash;
	public $timezone;
	public $level;
	public $permissions = [];

	function exists_by_id () {
		// check if object exists by id
		$stm = DB::$pdo->prepare("select count(*) from `user` where `id`=:id");
		$stm->bindParam(':id', $this->id);
		$stm->execute();
		$res = $stm->fetchColumn();

		if ($res > 0) {
			return true;
		}
		else {
			return false;
		}
	}

	function exists_by_email_address () {
		// check if object exists by id
		$stm = DB::$pdo->prepare("select count(*) from `user` where `email_address`=:email_address");
		$stm->bindParam(':email_address', $this->email_address);
		$stm->execute();
		$res = $stm->fetchColumn();

		if ($res > 0) {
			return true;
		}
		else {
			return false;
		}
	}

	function set_properties_by_id () {
		// set object properties by id
		$stm = DB::$pdo->prepare("select * from `user` where `id`=:id");
		$stm->bindParam(':id', $this->id);
		$stm->execute();
		$res = $stm->fetch();

		$this->set_properties_by_array($res);
	}

	function set_properties_by_username () {
		// set object properties by username
		$stm = DB::$pdo->prepare("select * from `user` where `username`=:username");
		$stm->bindParam(':username', $this->username);
		$stm->execute();
		$res = $stm->fetch();

		$this->set_properties_by_array($res);
	}

	function set_properties_by_array ($array) {
		foreach ($array as $key => $value) {
			// set property by array element if it exists
			if (property_exists($this, $key)) {
				$this->$key = $value;
			}

			// turn permissions into array if not already
			if ($key == 'permissions' && !is_array($this->$key)) {
				$this->$key = json_decode($this->$key, true);
			}
		}
	}

	function create () {
		// create object and set id
		$this->password_hash = password_hash($this->password, PASSWORD_DEFAULT);
		$permissions_json = json_encode($this->permissions);

		$stm = DB::$pdo->prepare("insert into `user` (`username`, `email_address`, `password_hash`, `timezone`, `level`, `permissions`) 
								  values (:username, :email_address, :password_hash, :timezone, :level, :permissions_json)");
		$stm->bindParam(':username', $this->username);
		$stm->bindParam(':email_address', $this->email_address);
		$stm->bindParam(':password_hash', $this->password_hash);
		$stm->bindParam(':timezone', $this->timezone);
		$stm->bindParam(':level', $this->level);
		$stm->bindParam(':permissions_json', $permissions_json);
		$stm->execute();

		$this->id = DB::$pdo->lastInsertId();
	}

	function update () {
		// update object by id
		$permissions_json = json_encode($this->permissions);

		$stm = DB::$pdo->prepare("update `user` 
								  set `username`=:username, `email_address`=:email_address, `timezone`=:timezone, `level`=:level, `permissions`=:permissions_json 
								  where `id`=:id");
		$stm->bindParam(':username', $this->username);
		$stm->bindParam(':email_address', $this->email_address);
		$stm->bindParam(':timezone', $this->timezone);
		$stm->bindParam(':level', $this->level);
		$stm->bindParam(':permissions_json', $permissions_json);
		$stm->bindParam(':id', $this->id);
		$stm->execute();
	}

	function update_password () {
		// update password
		$this->password_hash = password_hash($this->password, PASSWORD_DEFAULT);

		$stm = DB::$pdo->prepare('update `user` set `password_hash`=:password_hash where `id`=:id');
		$stm->bindParam(':password_hash', $this->password_hash);
		$stm->bindParam(':id', $this->id);
		$stm->execute();
	}

	function delete () {
		// delete object by id
		$stm = DB::$pdo->prepare("delete from `user` where `id`=:id");
		$stm->bindParam(':id', $this->id);
		$stm->execute();
	}

	function delete_tokens () {
		// delete all user tokens
		$stm = DB::$pdo->prepare("delete from `user_token` where `user_id`=:id");
		$stm->bindParam(':id', $this->id);
		$stm->execute();
	}

	function action_exists_by_id ($id) {
		// check if action exists by id
		$stm = DB::$pdo->prepare("select count(*) from `user_action` where `id`=:id");
		$stm->bindParam(':id', $id);
		$stm->execute();
		$res = $stm->fetchColumn();

		if ($res > 0) {
			return true;
		}
		else {
			return false;
		}
	}

	function fetch_action_data ($id) {
		// fetch action data by id
		$stm = DB::$pdo->prepare("select `data` from `user_action` where `id`=:id");
		$stm->bindParam(':id', $id);
		$stm->execute();
		$res = $stm->fetchColumn();

		return $res;
	}
}