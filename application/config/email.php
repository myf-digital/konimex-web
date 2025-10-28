<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['email'] = [
    'protocol'      => 'smtp',
    'smtp_host'     => 'mail.alphaciptatech.com',
    'smtp_port'     => 465,
    'smtp_user'     => 'noreply@alphaciptatech.com',
    'smtp_pass'     => 'BRHcBc5+sWa5TrWZrtMVeF1n',
    'smtp_crypto'   => 'ssl',
    'mailtype'      => 'html',
    'charset'       => 'utf-8',
    'crlf'          => "\r\n",
    'newline'       => "\r\n",
    'wordwrap'      => TRUE,
    'cc'            => [
        'yudi.wahyudi154@gmail.com',
        'BAJI@kenvue.com',
    ],
];