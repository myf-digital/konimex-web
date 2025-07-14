<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_area_kecamatan extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('area_kecamatan_model', 'area_kecamatan');
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
        response($this->area_kecamatan->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->area_kecamatan->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->area_kecamatan->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->area_kecamatan->load($data));
    }

}
