<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Change_pass extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('change_pass_model', 'change_pass');
    }

    public function index()
    {
        $this->template->show($this, 'form');
    }

    public function form()
    {
        $this->template->show($this, 'form');
    }

    public function update()
    {
        $data = param_input();
        response($this->change_pass->update($data));
    }


}
