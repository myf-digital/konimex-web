<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_sales_salesman_category extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('sales_salesman_category_model', 'sales_salesman_category');
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
        response($this->sales_salesman_category->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->sales_salesman_category->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->sales_salesman_category->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->sales_salesman_category->load($data));
    }

}
