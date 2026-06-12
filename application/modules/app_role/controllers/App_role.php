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
        response($this->role->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->role->update($data));
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
}
