<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_role extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('role_model', 'role');
    }

    public function index()
    {
        $this->template->show($this, 'content');
    }

    public function form()
    {
        $this->template->show($this, 'form');
    }

    public function create()
    {
        $data = param_input();
        $result = $this->role->create($data);
        if ($result['code'] == 200) {
            return response($result['data']);
        }
        return response(null, $result['code'], $result['message']);
    }

    public function update()
    {
        $data = param_input();
        $result = $this->role->update($data);
        if ($result['code'] == 200) {
            return response($result['data']);
        }
        return response(null, $result['code'], $result['message']);
    }

    public function delete()
    {
        $data = param_input();
        response($this->role->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->role->load($data));
    }

    public function detail()
    {
        $data = param_input();
        responseJSON($this->role->detail($data));
    }
}
