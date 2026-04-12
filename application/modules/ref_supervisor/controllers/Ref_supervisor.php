<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_sales_salesman extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('sales_salesman_model', 'sales_salesman');
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
        response($this->sales_salesman->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->sales_salesman->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->sales_salesman->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->sales_salesman->load($data));
    }

}
