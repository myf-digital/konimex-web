<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Conf_setup_rrk extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('setup_rrk_model', 'setup_rrk');
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
        response($this->setup_rrk->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->setup_rrk->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->setup_rrk->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->setup_rrk->load($data));
    }

}
