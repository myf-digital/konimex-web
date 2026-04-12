<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_resource extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('resource_model', 'resource');
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
        response($this->resource->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->resource->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->resource->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->resource->load($data));
    }

}
