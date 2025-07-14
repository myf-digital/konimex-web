<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_customer_type extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('customer_type_model', 'customer_type');
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
        response($this->customer_type->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->customer_type->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->customer_type->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->customer_type->load($data));
    }

}
