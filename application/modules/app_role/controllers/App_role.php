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
            return response($result['data'] ?? null, $result['code'] ?? 200, $result['message'] ?? 'Success');
        }
        return response(null, $result['code'], $result['message']);
    }

    public function update()
    {
        $data = param_input();
        $result = $this->role->update($data);
        if ($result['code'] == 200) {
            return response($result['data'] ?? null, $result['code'] ?? 200, $result['message'] ?? 'Success');
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

    public function spesialisasi()
    {
        $data = param_input();
        responseJSON($this->role->spesialisasi($data));
    }

    public function products()
    {
        $data = param_input();
        responseJSON($this->role->products($data));
    }

    public function get_spesialisasi_targets()
    {
        $data = param_input();
        responseJSON($this->role->get_spesialisasi_targets($data));
    }

    public function get_product_targets()
    {
        $data = param_input();
        responseJSON($this->role->get_product_targets($data));
    }
}
