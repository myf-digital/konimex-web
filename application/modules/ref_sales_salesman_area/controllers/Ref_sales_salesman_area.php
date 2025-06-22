<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_sales_salesman_area extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('sales_salesman_area_model', 'sales_salesman_area');
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
        response($this->sales_salesman_area->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->sales_salesman_area->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->sales_salesman_area->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->sales_salesman_area->load($data));
    }

}
