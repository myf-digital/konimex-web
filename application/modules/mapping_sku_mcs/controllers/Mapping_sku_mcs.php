<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mapping_sku_mcs extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('sku_mcs_model', 'sku_mcs');
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
        response($this->sku_mcs->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->sku_mcs->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->sku_mcs->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->sku_mcs->load($data));
    }

}
