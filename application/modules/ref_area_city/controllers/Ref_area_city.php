<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_area_city extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('area_city_model', 'area_city');
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
        response($this->area_city->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->area_city->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->area_city->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->area_city->load($data));
    }

}
