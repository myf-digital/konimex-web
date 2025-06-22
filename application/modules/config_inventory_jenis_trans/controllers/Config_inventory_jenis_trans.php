<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Config_inventory_jenis_trans extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('inventory_jenis_trans_model', 'inventory_jenis_trans');
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
        response($this->inventory_jenis_trans->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->inventory_jenis_trans->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->inventory_jenis_trans->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->inventory_jenis_trans->load($data));
    }

}
