<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_matrix_table extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('matrix_table_model', 'matrix_table');
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
        response($this->matrix_table->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->matrix_table->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->matrix_table->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->matrix_table->load($data));
    }

}
