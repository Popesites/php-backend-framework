<?php

if (file_exists('config.env')) {
	// environment file exists, use it for settings
	include 'config.env';
}
else {
	// production db settings
	define('DATABASE_HOST', 'localhost');
	define('DATABASE_NAME', '');
	define('DATABASE_USERNAME', '');
	define('DATABASE_PASSWORD', '');

	// production system email settings
	define('EMAIL_ADDRESS', '');
	define('EMAIL_USERNAME', '');
	define('EMAIL_PASSWORD', '');
	define('EMAIL_HOST', '');
	define('EMAIL_PORT', '');
}

// non-environment based constants
define('VERSION', '1.00');

// available user permissions
define('USER_PERMISSIONS', json_encode([
	'App' => ['config'],
	'User' => ['create', 'read', 'update', 'delete'],
	// generator user permission hook
]));