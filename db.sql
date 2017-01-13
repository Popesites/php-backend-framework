SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE `config` (
	`key` varchar(255) NOT NULL,
	`value` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `config` (`key`, `value`) VALUES
	('title', 'PHP Backend Framework'),
	('remember_time', '1 year'),
	('default_timezone', 'America/Toronto'),
	('lockout_time', '15 minutes'),
	('allowed_attempts', '3'),
	('password_reset_expire', '1 day'),
	('date_format', 'd/m/Y g:i A');

CREATE TABLE `user` (
	`id` int(11) NOT NULL,
	`username` varchar(255) NOT NULL,
	`email_address` varchar(255) NOT NULL,
	`password_hash` varchar(255) NOT NULL,
	`password_reset_token` varchar(255) DEFAULT NULL,
	`password_reset_expire` int(11) DEFAULT NULL,
	`timezone` varchar(255) NOT NULL,
	`level` enum('Admin','Standard') NOT NULL,
	`permissions` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `user` (`id`, `username`, `email_address`, `password_hash`, `password_reset_token`, `password_reset_expire`, `timezone`, `level`, `permissions`) VALUES
	(1, 'Admin', 'admin@example.com', '$2y$10$el6FOfZVihfEXnCFKSh40u55sg1sIStf1ygNVkfskAfNnUunRMY26', NULL, NULL, 'America/Toronto', 'Admin', '[]');

CREATE TABLE `user_action` (
	`id` int(11) NOT NULL,
	`user_id` int(11) NOT NULL,
	`action` varchar(255) NOT NULL,
	`data` text,
	`date_acted` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `user_attempt` (
	`id` int(11) NOT NULL,
	`ip` varchar(255) NOT NULL,
	`method` varchar(255) NOT NULL,
	`date_attempted` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `user_token` (
	`id` int(11) NOT NULL,
	`user_id` int(11) NOT NULL,
	`token` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


ALTER TABLE `config`
	ADD PRIMARY KEY (`key`);

ALTER TABLE `user`
	ADD PRIMARY KEY (`id`),
	ADD KEY `username` (`username`),
	ADD KEY `email_address` (`email_address`),
	ADD KEY `timezone` (`timezone`),
	ADD KEY `level` (`level`);

ALTER TABLE `user_action`
	ADD PRIMARY KEY (`id`),
	ADD KEY `user_id` (`user_id`),
	ADD KEY `action` (`action`),
	ADD KEY `date_acted` (`date_acted`);

ALTER TABLE `user_attempt`
	ADD PRIMARY KEY (`id`),
	ADD KEY `ip` (`ip`),
	ADD KEY `method` (`method`),
	ADD KEY `date_attempted` (`date_attempted`);

ALTER TABLE `user_token`
	ADD PRIMARY KEY (`id`),
	ADD KEY `user_id` (`user_id`),
	ADD KEY `token` (`token`);


ALTER TABLE `user`
	MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `user_action`
	MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `user_attempt`
	MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `user_token`
	MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;