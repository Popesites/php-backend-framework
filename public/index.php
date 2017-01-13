<?php

// start session && change to root dir
session_start();
chdir('../');

// composer autoload
include 'vendor/autoload.php';

// initialize singletons used in models, controllers, & views
DB::set_pdo();
Config::set_value();
AppUser::init();

// route based on url path
$router = new Router();
$router->route();