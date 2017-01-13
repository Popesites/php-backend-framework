<?php

class Config {
	public static $value = [];

	static function set_value () {
		// fetch dynamic config values from db
		$stm = DB::$pdo->prepare("select * from `config`");
		$stm->execute();
		$res = $stm->fetchAll();

		// set $value array property with results
		foreach ($res as $r) {
			self::$value[$r['key']] = $r['value'];
		}
	}

	static function update () {
		// update each config row in db
		foreach (self::$value as $key => $value) {
			$stm = DB::$pdo->prepare('update `config` set `value`=:value where `key`=:key');
			$stm->bindParam(':value', $value);
			$stm->bindParam(':key', $key);
			$stm->execute();
		}
	}
}