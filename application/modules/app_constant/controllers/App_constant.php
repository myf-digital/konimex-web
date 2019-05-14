<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_constant extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('constant_model', 'constant');
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
        response($this->constant->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->constant->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->constant->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->constant->load($data));
    }

}
