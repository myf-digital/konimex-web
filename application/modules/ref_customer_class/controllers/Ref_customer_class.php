<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_customer_class extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('customer_class_model', 'customer_class');
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
        response($this->customer_class->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->customer_class->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->customer_class->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->customer_class->load($data));
    }

    public function load_class()
    {
        $data = param_input();
        responseJSON($this->customer_class->loadClass($data));
    }

    public function load_subchannel()
    {
        $data = param_input();
        responseJSON($this->customer_class->load_subchannel($data));
    }

}
