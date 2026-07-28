<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'helpers/env_helper.php';

$config['onesignal_url'] = env('ONESIGNAL_URL', 'https://onesignal.com/api/v1/notifications');
$config['onesignal_url_to'] = env('ONESIGNAL_URL_TO', 'http://127.0.0.1:5555/');

// onesignal web
$config['onesignal_app_id'] = env('ONESIGNAL_APP_ID', '');
$config['onesignal_rest_api_key'] = env('ONESIGNAL_REST_API_KEY', '');

// onesignal api
$config['api_onesignal_app_id'] = env('API_ONESIGNAL_APP_ID', '');
$config['api_onesignal_rest_api_key'] = env('API_ONESIGNAL_REST_API_KEY', '');