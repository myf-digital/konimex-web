<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_aspek extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('aspek_model', 'aspek');
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
        response($this->aspek->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->aspek->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->aspek->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->aspek->load($data));
    }

    public function load_tipe_pertanyaan()
    {
        $data = param_input();
        responseJSON($this->aspek->load_tipe_pertanyaan($data));
    }

}
