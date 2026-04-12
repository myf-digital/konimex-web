<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Smpl_from_generator extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('from_generator_model', 'from_generator');
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
        response($this->from_generator->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->from_generator->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->from_generator->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->from_generator->load($data));
    }

}
