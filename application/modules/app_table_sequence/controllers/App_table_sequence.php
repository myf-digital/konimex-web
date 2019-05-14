<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_table_sequence extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('table_sequence_model', 'table_sequence');
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
        response($this->table_sequence->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->table_sequence->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->table_sequence->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->table_sequence->load($data));
    }

}
