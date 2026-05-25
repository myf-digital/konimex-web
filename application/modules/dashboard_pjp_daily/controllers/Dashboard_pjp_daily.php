<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_pjp_daily extends BaseController
{
    private $api_base = 'https://konimex-api.product-act.com/api_v1/';

    public function __construct()
    {
        parent::__construct();
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

        $ch = curl_init($this->api_base . 'dash_pjp_daily_history');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => [
                'start_date' => $param['start_date'] ?? date('Y-m-01'),
                'end_date'   => $param['end_date']   ?? date('Y-m-d'),
            ],
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
