<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_role_menu extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('role_menu_model', 'role_menu');
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
        response($this->role_menu->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->role_menu->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->role_menu->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->role_menu->load($data));
    }

}
