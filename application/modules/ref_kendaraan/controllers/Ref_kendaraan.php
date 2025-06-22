<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_kendaraan extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('kendaraan_model', 'kendaraan');
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
        response($this->kendaraan->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->kendaraan->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->kendaraan->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->kendaraan->load($data));
    }

}
