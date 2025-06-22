<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_bank extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('bank_model', 'bank');
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
        response($this->bank->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->bank->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->bank->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->bank->load($data));
    }

}
