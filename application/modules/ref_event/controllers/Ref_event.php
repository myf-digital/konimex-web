<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_event extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('event_model', 'event');
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
        response($this->event->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->event->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->event->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->event->load($data));
    }

}
