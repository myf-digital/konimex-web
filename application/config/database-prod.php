<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(
	'dsn'	=> '',
	'hostname' => '154.26.137.74:3306',
	'username' => 'flamingo',
	'password' => '2pqXcoGMMWMmxyVZJVFU20iW',
	// 'database' => 'dbdrcs',
	'database' => 'dbdrc',
	// 'hostname' => 'localhost:3306',
	// 'username' => 'apps',
	// 'password' => 'c3BoZXJlYXBwcwo=',
	// 'database' => 'dbdrc_dummy',	
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

$db['slave'] = array(
	'dsn'	=> '',
	'hostname' => '154.26.137.172:3306',
	'username' => 'flamingo',
	'password' => '2pqXcoGMMWMmxyVZJVFU20iW',
	// 'database' => 'dbdrcs',
	'database' => 'dbdrc',
	// 'hostname' => 'localhost:3306',
	// 'username' => 'apps',
	// 'password' => 'c3BoZXJlYXBwcwo=',
	// 'database' => 'dbdrc_dummy',	
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
