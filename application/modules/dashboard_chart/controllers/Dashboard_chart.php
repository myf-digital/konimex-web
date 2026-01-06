<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_chart extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Dashboard_chart_model', 'dashboard_chart');
    }

    public function index()
    {
        $this->template->show($this, 'content');
    }

    public function load()
    {
        $data = param_input();
        responseJSON($this->dashboard_chart->load($data));
    }
}