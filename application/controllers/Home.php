<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	
	function __construct() {
		
		parent::__construct();
		
		$this->load->library('authlib');
		
		//$cek = $this->authlib->cek_timeout();
		
		$username = "";
		$username = $this->session->userdata('username');
		//echo "Username - ".$username; die();
		if ($username == "") {
			//redirect them to the login page
			echo '<h4 style="margin-top:10px; display:block; text-align:left"><i class="fa fa-warning txt-color-orangeDark"></i> Your login is expired, click <a href="'.base_url().'">here</a> to relogin.</h4>';
			die;
		}		
	}
	
	public function index()
	{
		$username = $this->session->userdata('username');
		$data = array(
			"username" => $username,
			"error"  => ""
		); 	
		//echo base_url();
		$this->load->view('main_page_view',$data);
		//redirect('main_page','refresh');
	}
}
