<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['email'] = [
    'protocol'      => 'smtp',
	'smtp_host'     => env('SMTP_HOST', ''),
	'smtp_port'     => env('SMTP_PORT', ''),
	'smtp_user'     => env('SMTP_USER', ''),
	'smtp_pass'     => env('SMTP_PASS', ''),
	'smtp_crypto'   => env('SMTP_CRYPTO', 'tls'),
    'cc'            => env('SMTP_CC', []),
    'mailtype'      => 'html',
    'charset'       => 'utf-8',
    'crlf'          => "\r\n",
    'newline'       => "\r\n",
    'wordwrap'      => TRUE,
];