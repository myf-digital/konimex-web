<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_salesman_relationship extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('salesman_relationship_model', 'salesman_relationship');
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
        response($this->salesman_relationship->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->salesman_relationship->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->salesman_relationship->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->salesman_relationship->load($data));
    }
}
