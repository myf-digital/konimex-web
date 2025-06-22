<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mapping_promo_active extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('promo_active_model', 'promo_active');
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
        response($this->promo_active->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->promo_active->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->promo_active->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->promo_active->load($data));
    }

}
