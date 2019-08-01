<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_group_peserta extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('group_peserta_model', 'group_peserta');
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
        response($this->group_peserta->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->group_peserta->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->group_peserta->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->group_peserta->load($data));
    }

    public function get_responden()
    {
        $data = param_input();
        responseJSON($this->group_peserta->responden($data));
    }

}
