<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_gl_coa extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('gl_coa_model', 'gl_coa');
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
        response($this->gl_coa->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->gl_coa->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->gl_coa->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->gl_coa->load($data));
    }

}
