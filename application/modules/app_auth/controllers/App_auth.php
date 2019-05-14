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
    }

    public function index()
    {
        //direct_to_home();
        $this->cparam['title'] = 'Login';
        $this->cparam['form_auth'] = site_url($this->cparam['controller'] . '/verify');;
        $this->template->show_login($this->cparam, 'login_lte_template', $this->cparam);
    }

    public function verify()
    {
        $data = param_input();
        $result = authUserApplication($data);
        response($result, 200, "process success");
    }

    public function logout()
    {
        header('Content-Type: application/json');
        echo json_encode(logoutUserLogin());
    }
}
