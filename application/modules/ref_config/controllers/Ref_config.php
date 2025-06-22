<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_config extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('config_model', 'config');
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
        response($this->config->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->config->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->config->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->config->load($data));
    }

}
