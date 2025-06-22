<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_customer_image extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('customer_image_model', 'customer_image');
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
        response($this->customer_image->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->customer_image->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->customer_image->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->customer_image->load($data));
    }

}
