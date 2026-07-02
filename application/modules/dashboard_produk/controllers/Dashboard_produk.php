<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_produk extends BaseController
{
    private $api_base = '';

    public function __construct()
    {
        parent::__construct();

        $this->api_base = $this->config->item('url_api') . '/api_v1/';
    }

    public function index()
    {
        $this->template->show($this, 'content');
    }

    public function load()
    {
        $param = param_input();

        $ci_session = $_COOKIE['ci_session'] ?? '';
        $token      = $this->session->userdata('token') ?: 'expired';

        $postData = [
            'periode' => $param['periode'] ?? date('Y-m'),
        ];
        foreach ($param as $key => $value) {
            if ($value !== '') {
                $postData[$key] = $value;
            }
        }

        $ch = curl_init($this->api_base . 'dashboard_produk_target');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_HTTPHEADER     => [
                'X-Token: ' . $token,
                'Cookie: ci_session=' . $ci_session,
            ],
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        responseJSON(json_decode($response, true));
    }
}
