<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Conf_setup_pjp extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('setup_pjp_model', 'setup_pjp');
    }

    public function index()
    {
        $this->template->show($this, 'content');
    }

    public function form()
    {
        $this->template->show($this, 'form');
    }

    public function form_addpjp()
    {
        $this->template->show($this, 'form_addpjp');
    }

    public function form_download()
    {
        $this->template->show($this, 'form_download');
    }

    public function form_upload()
    {
        $this->template->show($this, 'form_upload');
    }

    public function create()
    {
        $data = param_input();
        response($this->setup_pjp->create($data));
    }

    public function update()
    {
        $data = param_input();
        response($this->setup_pjp->update($data));
    }

    public function delete()
    {
        $data = param_input();
        response($this->setup_pjp->delete($data));
    }

    public function load()
    {   
        //$cookieData = get_cookie("session");
        $data = param_input();
        responseJSON($this->setup_pjp->load($data));
    }

    public function savetoxlsx()
    {
        $salesmanid = $this->uri->segment('3');
        $data = array("salesmanid" => $salesmanid);
        responseJSON($this->setup_pjp->savetoxls($data));
    }

}
