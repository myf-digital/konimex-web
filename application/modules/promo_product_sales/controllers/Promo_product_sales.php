<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class promo_product_sales extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('promo_product_model', 'promo_product');
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
        response($this->promo_product->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->promo_product->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->promo_product->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->promo_product->load($data));
    }

}
