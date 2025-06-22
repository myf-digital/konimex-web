<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_area_areasite extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('area_areasite_model', 'area_areasite');
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
        response($this->area_areasite->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->area_areasite->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->area_areasite->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->area_areasite->load($data));
    }

}
