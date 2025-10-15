<?php
defined('BASEPATH') OR exit('No direct script access allowed');


$config['email'] = [
    'protocol'      => 'smtp',
    'smtp_host'     => 'sandbox.smtp.mailtrap.io',
    'smtp_port'     => 2525,
    'smtp_user'     => 'fce06934e4832d',
    'smtp_pass'     => '27ceb283c382c4',
    'smtp_crypto'   => 'tls',
    'mailtype'      => 'html',
    'charset'       => 'utf-8',
    'crlf'          => "\r\n",
    'newline'       => "\r\n",
    'wordwrap'      => TRUE,
];