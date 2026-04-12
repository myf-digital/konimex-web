<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_area_subarea extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('area_subarea_model', 'area_subarea');
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
        response($this->area_subarea->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->area_subarea->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->area_subarea->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->area_subarea->load($data));
    }

}
