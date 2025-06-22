<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_restrict_location extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('restrict_location_model', 'restrict_location');
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
        response($this->restrict_location->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->restrict_location->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->restrict_location->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->restrict_location->load($data));
    }

}
