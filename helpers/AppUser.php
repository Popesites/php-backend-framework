<?php

class AppUser {
	/** @var $user User */
	public static $user;
	public static $token;
	public static $signed_in = false;

	static function init () {
		// set user object
		self::$user = new User();

		// authenticate
		self::authenticate();

		// set timezone
		self::set_timezone();
	}

	static function authenticate () {
		// use cookie or session values
		if (isset($_COOKIE['id']) && isset($_COOKIE['token'])) {
			self::$user->id = $_COOKIE['id'];
			self::$token = $_COOKIE['token'];
		}
		else if (isset($_SESSION['id']) && isset($_SESSION['token'])) {
			self::$user->id = $_SESSION['id'];
			self::$token = $_SESSION['token'];
		}

		if (self::$user->exists_by_id() && self::exists_by_token()) {
			// user & token exist, set properties
			self::$user->set_properties_by_id();
			self::$signed_in = true;
		}
		else {
			// user/token invalid, ensure session/cookies are clear
			self::sign_out();
		}
	}

	static function exists_by_token () {
		// check if user token exists
		$stm = DB::$pdo->prepare("select count(*) from `user_token` where `user_id`=:id and `token`=:token");
		$stm->bindParam(':id', self::$user->id);
		$stm->bindParam(':token', self::$token);
		$stm->execute();
		$res = $stm->fetchColumn();

		if ($res > 0) {
			return true;
		}
		else {
			return false;
		}
	}

	static function sign_out () {
		// delete current user token
		self::delete_token();

		// expire cookies if set
		if (isset($_COOKIE['id']) && isset($_COOKIE['token'])) {
			setcookie('id', null, 1, '/');
			setcookie('token', null, 1, '/');
		}

		// unset session values if set
		if (isset($_SESSION['id']) && isset($_SESSION['token'])) {
			unset($_SESSION['id']);
			unset($_SESSION['token']);
		}
	}

	static function delete_token () {
		// delete current user token
		$stm = DB::$pdo->prepare("delete from `user_token` where `user_id`=:id and `token`=:token");
		$stm->bindParam(':id', self::$user->id);
		$stm->bindParam(':token', self::$token);
		$stm->execute();
	}

	static function locked_out ($method) {
		$lockout_time = strtotime('-'.Config::$value['lockout_time']);

		// fetch total attempt count
		$stm = DB::$pdo->prepare('select count(*) from `user_attempt` where `ip`=:ip and `method`=:method and `date_attempted` > :lockout_time');
		$stm->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
		$stm->bindParam(':method', $method);
		$stm->bindParam(':lockout_time', $lockout_time);
		$stm->execute();
		$res = $stm->fetchColumn();

		if ($res >= Config::$value['allowed_attempts']) {
			// user has failed too many times
			return true;
		}
		else {
			// user is allowed more attempts
			return false;
		}
	}

	static function credentials_valid () {
		// fetch password hash by username
		$stm = DB::$pdo->prepare("select `password_hash` from `user` where `username`=:username");
		$stm->bindParam(':username', self::$user->username);
		$stm->execute();
		$res = $stm->fetchColumn();

		if (password_verify(self::$user->password, $res)) {
			// user found and hashes match, credentials are valid
			return true;
		}
		else {
			// username/password invalid
			return false;
		}
	}

	static function create_attempt ($method) {
		// create attempt used for lockouts
		$date_attempted = time();

		$stm = DB::$pdo->prepare('insert into `user_attempt` (`ip`, `method`, `date_attempted`) values (:ip, :method, :date_attempted)');
		$stm->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
		$stm->bindParam(':method', $method);
		$stm->bindParam(':date_attempted', $date_attempted);
		$stm->execute();
	}

	static function sign_in ($remember_me) {
		// set app user properties & create token
		self::$user->set_properties_by_username();
		self::create_token();

		// set cookie values if remember me is checked
		if (!empty($remember_me)) {
			setcookie('id', self::$user->id, strtotime('+'.Config::$value['remember_time']), '/');
			setcookie('token', self::$token, strtotime('+'.Config::$value['remember_time']), '/');
		}

		// set session values
		$_SESSION['id'] = self::$user->id;
		$_SESSION['token'] = self::$token;
	}

	static function create_token () {
		// create app user token
		self::$token = bin2hex(openssl_random_pseudo_bytes(16));

		$stm = DB::$pdo->prepare("insert into `user_token` (`user_id`, `token`) values (:id, :token)");
		$stm->bindParam(':id', self::$user->id);
		$stm->bindParam(':token', self::$token);
		$stm->execute();
	}

	static function has_permission ($permission_required) {
		if (self::$user->level == 'Admin' || in_array($permission_required, self::$user->permissions)) {
			// user has permission
			return true;
		}
		else {
			// user does not have permission
			return false;
		}
	}

	static function set_timezone () {
		if (self::$signed_in) {
			$timezone = self::$user->timezone;
		}
		else {
			$timezone = Config::$value['default_timezone'];
		}

		date_default_timezone_set($timezone);
	}

	static function delete_attempts ($method) {
		$stm = DB::$pdo->prepare("delete from `user_attempt` where `ip`=:ip and `method`=:method");
		$stm->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
		$stm->bindParam(':method', $method);
		$stm->execute();
	}

	static function send_password_reset_link ($url) {
		// create reset token
		$token = bin2hex(openssl_random_pseudo_bytes(16));

		// update db entry
		self::update_password_reset_token($token);

		// deliver reset link email
		$mail = new PHPMailer();
		$mail->isSMTP();
		$mail->SMTPOptions = ['ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true]];
		$mail->SMTPAuth = true;
		$mail->Host = EMAIL_HOST;
		$mail->Port = EMAIL_PORT;
		$mail->Username = EMAIL_USERNAME;
		$mail->Password = EMAIL_PASSWORD;
		$mail->setFrom(EMAIL_ADDRESS, Config::$value['title']);
		$mail->addAddress(self::$user->email_address);
		$mail->Subject = 'Password Reset Link';
		$mail->Body = 'Click here to reset your password: '.$url.'/password_reset/'.urlencode(self::$user->email_address).'/'.$token;

		if (!$mail->send()) {
			echo 'Mailer Error: '.$mail->ErrorInfo;
		}
	}

	static function update_password_reset_token ($token) {
		// update reset token columns in db for user
		$expire = strtotime('+'.Config::$value['password_reset_expire']);

		$stm = DB::$pdo->prepare('update `user` set `password_reset_token`=:token, `password_reset_expire`=:expire where `email_address`=:email_address');
		$stm->bindParam(':token', $token);
		$stm->bindParam(':expire', $expire);
		$stm->bindParam(':email_address', self::$user->email_address);
		$stm->execute();
	}

	static function password_reset_token_exists ($token) {
		// ensure token exists and hasn't expired
		$time = time();

		$stm = DB::$pdo->prepare('select count(*) from `user` where `email_address`=:email_address and `password_reset_token`=:token and `password_reset_expire` > :time');
		$stm->bindParam(':email_address', self::$user->email_address);
		$stm->bindParam(':token', $token);
		$stm->bindParam(':time', $time);
		$stm->execute();
		$res = $stm->fetchColumn();

		if ($res > 0) {
			// reset token exists and hasn't expired
			return true;
		}
		else {
			// reset token does not exist or has expired
			return false;
		}
	}

	static function reset_password () {
		// reset users password
		$password_hash = password_hash(self::$user->password, PASSWORD_DEFAULT);

		$stm = DB::$pdo->prepare('update `user` set `password_hash`=:password_hash where `email_address`=:email_address');
		$stm->bindParam(':password_hash', $password_hash);
		$stm->bindParam(':email_address', self::$user->email_address);
		$stm->execute();
	}

	static function update_profile () {
		// update current user profile
		$stm = DB::$pdo->prepare('update `user` set `email_address`=:email_address, `timezone`=:timezone where `id`=:id');
		$stm->bindParam(':email_address', self::$user->email_address);
		$stm->bindParam(':timezone', self::$user->timezone);
		$stm->bindParam(':id', self::$user->id);
		$stm->execute();
	}

	static function create_action ($action, $data = null) {
		// log user action
		$date_acted = time();

		// hide passwords and json encode data if not null
		if ($data != null) {
			if (isset($data['password']) && !empty($data['password'])) {
				$data['password'] = '(hidden)';
			}

			$data = json_encode($data);
		}

		$stm = DB::$pdo->prepare('insert into `user_action` (`user_id`, `action`, `data`, `date_acted`) values (:id, :action, :data, :date_acted)');
		$stm->bindParam(':id', self::$user->id);
		$stm->bindParam(':action', $action);
		$stm->bindParam(':data', $data);
		$stm->bindParam(':date_acted', $date_acted);
		$stm->execute();
	}
}