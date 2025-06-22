<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_product extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('product_model', 'product');
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
        response($this->product->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->product->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->product->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->product->load($data));
    }

}
