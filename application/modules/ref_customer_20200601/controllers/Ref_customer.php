<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ref_customer extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('customer_model', 'customer');
    }

    public function index()
    {
        $this->template->show($this, 'content');
    }

    public function form()
    {
        $this->template->show($this, 'form');
    }

    public function form_download()
    {
        $this->template->show($this, 'form_download');
    }

    public function create()
    {
        $data = param_input();
        response($this->customer->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->customer->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->customer->delete($data));
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->customer->load($data));
    }

    public function savetoxls()
    {
        $account = $this->uri->segment('3');
        $username = $this->uri->segment('4');
        $jabatan = $this->uri->segment('5');
        $filtername = $this->uri->segment('6');
        $data = array("account" => $account , "username" => $username, "jabatan" => $jabatan, "filename" => $filtername);
        $this->customer->savetoxls($data);
    }


}
