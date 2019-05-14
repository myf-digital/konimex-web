<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_dashboard extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        //$this->load->model('menu_model', 'menu');
    }

    public function index()
    {
        $this->template->show($this, 'content');
    }

}
