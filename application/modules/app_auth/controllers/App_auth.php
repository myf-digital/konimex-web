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
        $session = isset($_SESSION) ? $_SESSION : [];
        if (isset($session['user_credential']) && !empty($session['user_credential'])) {
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

        if (isset($result->status_login) && $result->status_login == 200) {
            $message = $data['username'] . ' login pada tanggal ' . date('Y-m-d H:i:s');
            if (isset($data['player_id']) && !empty($data['player_id'])) {
                $resOnesignal = send_onesignal([
                    'player_ids' => $data['player_id'],
                    'external_ids' => $data['username'],
                    'title' => 'Berhasil Login',
                    'message' => $message,
                    'data' => [
                        'type' => 'Login',
                        'nip' => $result->session['nip'] ?? '',
                        'name' => $result->session['name'] ?? '',
                        'telepon' => $result->session['salesmanid'] ?? '',
                        'user_type' => $result->session['type'] ?? '',
                    ],
                ]);
                $result->res_onesignal = $resOnesignal['data'] ?? false;
            }
            if (isset($result->session) && isset($result->session['telepon'])) {
                $nomor = format_phone($result->session['telepon']);
                if ($nomor) {
                    $resWA = send_wa(['phone' => $nomor, 'type' => 'text', 'text' => $message]);
                    $result->res_wa = $resWA['data'] ?? false;
                } else {
                    log_message('error', "Nomor telepon tidak valid: " . $result->session['telepon']);
                }
            }
        }

        response($result, 200, "process success");
    }

    public function logout()
    {
        $response = logoutUserLogin();
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}
