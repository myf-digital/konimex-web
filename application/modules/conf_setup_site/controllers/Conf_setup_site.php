<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Conf_setup_site extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('setup_site_model', 'setup_site');
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
        response($this->setup_site->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->setup_site->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->setup_site->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->setup_site->load($data));
    }

}
