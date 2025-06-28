<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('send_onesignal')) {
    function send_onesignal($player_ids, $message, $data = []) {
        $CI =& get_instance();
        $CI->load->config('onesignal');
        $CI->load->library('Http_client');
        
        $url = $CI->config->item('onesignal_url');
        $app_id = $CI->config->item('onesignal_app_id');
        $rest_api_key = $CI->config->item('onesignal_rest_api_key');
        
        if (empty($url) || empty($app_id) || empty($rest_api_key)) {
            log_message('error', 'OneSignal configuration is incomplete.');
            return [
                'status' => false,
                'message' => 'OneSignal configuration is incomplete.'
            ];
        }

        $fields = [
            'app_id' => $app_id,
            'include_player_ids' => is_array($player_ids) ? $player_ids : [$player_ids],
            'contents' => ['en' => $message],
            'data' => $data
        ];
        $response = $CI->http_client->request('POST', $url, [
            'headers' => [
                'Content-Type' => 'application/json; charset=utf-8',
                'Authorization' => 'Basic ' . $rest_api_key
            ],
            'json' => $fields,
        ]);
        
        return $response;
    }
}