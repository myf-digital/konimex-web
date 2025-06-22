<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Change_mypassword extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('change_mypassword_model', 'change_mypassword');
    }

    public function index()
    {
        $this->template->show($this, 'form');
    }

    public function update()
    {
        $data = param_input();
        response($this->change_mypassword->update($data));
    }


}