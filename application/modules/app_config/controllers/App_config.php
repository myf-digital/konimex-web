<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_config extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('config_model', 'model');
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
        response($this->model->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->model->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->model->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->model->load($data));
    }

}
