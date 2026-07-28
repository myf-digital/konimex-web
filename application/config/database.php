<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'helpers/env_helper.php';

$active_group = 'default';
$query_builder = TRUE;

$db = array();
$db['default'] = array(
	'dsn'	=> '',
	'hostname' => env('DB_HOST', 'localhost'),
	'username' => env('DB_USER', 'root'),
	'password' => env('DB_PASS', ''),
	'database' => env('DB_NAME', 'dbhimalaya'),
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
