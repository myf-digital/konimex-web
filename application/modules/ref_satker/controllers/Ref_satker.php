<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_satker extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('satker_model', 'satker');
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
        response($this->satker->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->satker->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->satker->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->satker->load($data));
    }

}
