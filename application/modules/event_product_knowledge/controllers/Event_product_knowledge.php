<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Event_product_knowledge extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('event_product_knowledge_model', 'product_knowledge');
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
        response($this->product_knowledge->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->product_knowledge->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->product_knowledge->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->product_knowledge->load($data));
    }

}
