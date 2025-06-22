<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_product_competitor extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('product_competitor_model', 'product_competitor');
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
        response($this->product_competitor->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->product_competitor->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->product_competitor->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->product_competitor->load($data));
    }

}
