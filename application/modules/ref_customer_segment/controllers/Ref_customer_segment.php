<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_customer_segment extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('customer_segment_model', 'customer_segment');
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
        response($this->customer_segment->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->customer_segment->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->customer_segment->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->customer_segment->load($data));
    }

}
