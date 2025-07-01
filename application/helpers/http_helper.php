<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('send_onesignal')) {
    function send_onesignal($payload) {
        if (!$payload || !isset($payload['player_ids'])) {
            log_message('error', 'Player Ids OneSignal configuration is incomplete.');
            return [
                'status' => false,
                'message' => 'Player Ids OneSignal is incomplete.'
            ];
        }

        $CI =& get_instance();
        $CI->load->config('onesignal');
        $CI->load->library('Http_client');
        
        $url = $CI->config->item('onesignal_url');
        $url_to = $CI->config->item('onesignal_url_to');
        $app_id = $CI->config->item('onesignal_app_id');
        $rest_api_key = $CI->config->item('onesignal_rest_api_key');
        
        if (empty($url) || empty($app_id) || empty($rest_api_key)) {
            log_message('error', 'OneSignal configuration is incomplete.');
            return [
                'status' => false,
                'message' => 'OneSignal configuration is incomplete.'
            ];
        }

        $headers = [
            'Content-Type' => 'application/json; charset=utf-8',
            'Authorization' => 'Basic ' . $rest_api_key
        ];
        $fields = [
            'app_id' => $app_id,
            'include_player_ids' => is_array($payload['player_ids']) ? $payload['player_ids'] : [$payload['player_ids']],
            'headings' => ['en' => $payload['title']],
            'contents' => ['en' => $payload['message']],
            'url' => $url_to . $payload['url'],
            'data' => $payload['data'],
        ];
        $response = $CI->http_client->request('POST', $url, ['headers' => $headers, 'json' => $fields]);

        log_http([
            'url' => $url,
            'service' => 'onesignal',
            'method' => 'POST',
            'headers' => json_encode($headers),
            'request' => json_encode($fields),
            'response' => json_encode($response),
        ]);
        
        return $response;
    }
}

if (!function_exists('send_wa')) {
    function send_wa($payload) {
        $CI =& get_instance();
        $CI->load->config('wa_zawa');
        $CI->load->library('Http_client');
        
        $url = $CI->config->item('wa_zawa_url');
        $app_id = $CI->config->item('wa_zawa_id');
        $session_id = $CI->config->item('wa_zawa_session_id');
        
        if (empty($url) || empty($app_id) || empty($session_id)) {
            log_message('error', 'WA Zawa configuration is incomplete.');
            return [
                'status' => false,
                'message' => 'WA Zawa configuration is incomplete.'
            ];
        }

        $headers = [
            'Content-Type' => 'application/json; charset=utf-8',
            'id' => $app_id,
            'session-id' => $session_id,
        ];
        $response = $CI->http_client->request('POST', $url, ['headers' => $headers, 'json' => $payload]);

        log_http([
            'url' => $url,
            'service' => 'wa_zawa',
            'method' => 'POST',
            'headers' => json_encode($headers),
            'request' => json_encode($payload),
            'response' => json_encode($response),
        ]);
        
        return $response;
    }
}

if (!function_exists('format_phone')) {
    function format_phone($phone) {
        $nomor = preg_replace('/[^0-9]/', '', $phone);
        if (empty($nomor)) return false;

        if (substr($nomor, 0, 2) == '62') {
            $nomor = substr($nomor, 2);
        } elseif (substr($nomor, 0, 1) == '0') {
            $nomor = substr($nomor, 1);
        }
        
        $nomor = '62' . $nomor;
        if (strlen($nomor) < 12) return false;
        
        return $nomor;
    }
}

if (!function_exists('log_http')) {
    function log_http($data) {
        $CI =& get_instance();
        return $CI->db->insert('log_http', $data);
    }
}