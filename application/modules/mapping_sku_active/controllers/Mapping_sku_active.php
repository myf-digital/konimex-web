<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mapping_sku_active extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('sku_active_model', 'sku_active');
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
        response($this->sku_active->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->sku_active->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->sku_active->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->sku_active->load($data));
    }

}
