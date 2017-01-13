<?php

class GeneratedObject {
	// generator property hook

	function exists_by_id () {
		// check if object exists by id
		$stm = DB::$pdo->prepare("select count(*) from `generated_object` where `id`=:id");
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

	function set_properties_by_id () {
		// set object properties by id
		$stm = DB::$pdo->prepare("select * from `generated_object` where `id`=:id");
		$stm->bindParam(':id', $this->id);
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
		}
	}

	function create () {
		// create object and set id
		$stm = DB::$pdo->prepare("insert into `generated_object` ({$generator_insert_columns}) 
								  values ({$generator_insert_values})");
		// generator bind hook
		$stm->execute();

		$this->id = DB::$pdo->lastInsertId();
	}

	function update () {
		// update object by id
		$stm = DB::$pdo->prepare("update `generated_object` 
								  set {$generator_update_values} 
								  where `id`=:id");
		// generator bind hook
		$stm->bindParam(':id', $this->id);
		$stm->execute();
	}

	function delete () {
		// delete object by id
		$stm = DB::$pdo->prepare("delete from `generated_object` where `id`=:id");
		$stm->bindParam(':id', $this->id);
		$stm->execute();
	}
}