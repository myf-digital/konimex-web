<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_regional extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('regional_model', 'regional');
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
        response($this->regional->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->regional->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->regional->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->regional->load($data));
    }

}
