<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$active_group = 'default';
$query_builder = TRUE;

$db['default'] = array(
	'dsn'	=> '',
	// 'hostname' => 'localhost',
	// 'username' => 'root',
	// 'password' => '',
    //'hostname' => '194.59.165.183:3306',
//    	'hostname' => '5.181.217.248:3306',
//    	'username' => 'apps',
//    	'password' => 'Ap@yalup4#L@gi',
	'hostname' => 'localhost:3306',
	'username' => 'useradmin',
	'password' => 'useradmin@2024',
	'database' => 'dbpar',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'development'),
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
