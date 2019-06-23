<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_matrix_aspek extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('matrix_aspek_model', 'matrix_aspek');
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
        response($this->matrix_aspek->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->matrix_aspek->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->matrix_aspek->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->matrix_aspek->load($data));
    }

}
