<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rdg extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('aspek_model', 'aspek');
    }

    public function index()
    {
        $this->load->view('index');
    }

    public function materi()
    {
        $this->load->view('materi');
    }

    public function question()
    {
        $this->load->view('question');
    }

    public function quality()
    {
        $this->load->view('quality');
    }

    public function create()
    {
        $data = param_input();
        response($this->aspek->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->aspek->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->aspek->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->aspek->load($data));
    }

}
