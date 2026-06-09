<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'helpers/env_helper.php';

$config['wa_zawa_url'] = env('WA_ZAWA_URL', 'https://api-zawa.azickri.com/message');
$config['wa_zawa_id'] = env('WA_ZAWA_ID', '');
$config['wa_zawa_session_id'] = env('WA_ZAWA_SESSION_ID', '');