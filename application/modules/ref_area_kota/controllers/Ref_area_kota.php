<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_area_kota extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('area_kota_model', 'area_kota');
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
        response($this->area_kota->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->area_kota->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->area_kota->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->area_kota->load($data));
    }

}
