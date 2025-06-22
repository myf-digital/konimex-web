<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_area_kelurahan extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('area_kelurahan_model', 'area_kelurahan');
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
        response($this->area_kelurahan->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->area_kelurahan->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->area_kelurahan->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->area_kelurahan->load($data));
    }

}
