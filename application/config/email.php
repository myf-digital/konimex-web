<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['email'] = [
    'protocol'      => 'smtp',
    // 'smtp_host'     => 'mail.alphaciptatech.com',
    // 'smtp_port'     => 465,
    // 'smtp_user'     => 'noreply@alphaciptatech.com',
    // 'smtp_pass'     => 'BRHcBc5+sWa5TrWZrtMVeF1n',
    // 'smtp_crypto'   => 'ssl',
    'smtp_host' => 'sandbox.smtp.mailtrap.io',
    'smtp_port' => 2525,
    'smtp_user' => 'fce06934e4832d',
    'smtp_pass' => '27ceb283c382c4',
    'smtp_crypto' => 'tls',
    'mailtype'      => 'html',
    'charset'       => 'utf-8',
    'crlf'          => "\r\n",
    'newline'       => "\r\n",
    'wordwrap'      => TRUE,
    'cc'            => [
        'arthadedex8@gmail.com',
        'yusuf.hartanto69@gmail.com',
    ],
];