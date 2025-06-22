<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Visibility_program_active extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('visibility_program_model', 'visibility_program');
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
        response($this->visibility_program->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->visibility_program->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->visibility_program->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->visibility_program->load($data));
    }

}
