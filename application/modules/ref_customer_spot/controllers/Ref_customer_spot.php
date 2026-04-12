<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_customer_spot extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('customer_spot_model', 'customer_spot');
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
        response($this->customer_spot->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->customer_spot->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->customer_spot->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->customer_spot->load($data));
    }

}
