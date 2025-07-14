<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_brand extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('brand_model', 'brand');
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
        response($this->brand->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->brand->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->brand->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->brand->load($data));
    }

}
