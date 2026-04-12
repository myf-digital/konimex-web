<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Conf_setupsite_db extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('setupsite_db_model', 'setupsite_db');
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
        response($this->setupsite_db->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->setupsite_db->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->setupsite_db->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->setupsite_db->load($data));
    }

}
