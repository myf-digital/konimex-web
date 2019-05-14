<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_menu extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('menu_model', 'menu');
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
        response($this->menu->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->menu->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->menu->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->menu->load($data));
    }

}
