<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_auth extends CI_Controller
{
    var $cparam = array();

    public function __construct()
    {
        parent::__construct();
        $this->cparam['controller'] = $this->router->fetch_class();
        $this->cparam['content'] = site_url($this->router->fetch_class() . '/login_lte_template');
        $this->load->config('onesignal');
    }

    public function index()
    {
        $session = isset($_SESSION) ? $_SESSION : array();
        if (isset($session['user_login']) && !empty($session['user_login'])) {
            redirect(base_url('app_dashboard'));
        }

        $this->cparam['title'] = 'Login';
        $this->cparam['form_auth'] = site_url($this->cparam['controller'] . '/verify');
        $this->cparam['onesignal_app_id'] = $this->config->item('onesignal_app_id');
        $this->template->show_login($this->cparam, 'login_lte_template', $this->cparam);
    }

    public function verify()
    {
        $data = param_input();
        $result = authUserApplication($data);

        $response = send_onesignal($data['player_id'], $data['username'] . ' Berhasil login pada tanggal ' . date('Y-m-d H:i:s'), $result);
        $result->res_onesignal = json_decode($response, true);

        response($result, 200, "process success");
    }

    public function logout()
    {
        header('Content-Type: application/json');
        echo json_encode(logoutUserLogin());
    }
}
