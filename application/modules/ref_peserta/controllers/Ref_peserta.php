<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_rdg extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('rdg_model', 'rdg_home');
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
        response($this->rdg->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->rdg->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->rdg->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->rdg->load($data));
    }

}
