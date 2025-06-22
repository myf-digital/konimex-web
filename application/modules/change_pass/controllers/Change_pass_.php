<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Change_pass extends CI_Controller {
	
	var $gparam = array();
	
	public function __construct() {
        parent::__construct();
		$this->load->library('authlib');
		
		$username = "";		
		
		$cek = $this->authlib->cek_timeout();
		if ($cek == 1) {
			$username = $this->session->userdata('username');
		}
		
		//echo $username;
		if ($username == "") {
			//redirect them to the login page
			echo '<h4 style="margin-top:10px; display:block; text-align:left"><i class="fa fa-warning txt-color-orangeDark"></i> Your login is expired, click <a href="'.base_url().'">here</a> to relogin.</h4>';
			die;
		}
		
		$this->gparam['controller'] = $this->router->fetch_class();
		$this->load->model('change_pass_model', 'updatePass');
		$this->gparam['privilage'] = getPrivilage($this->gparam['controller']);
		$this->gparam['restrict'] = 'You Cannot Access This Menu';
		
		/* //cek timeout
		$this->load->library('authlib');
		$username = $this->session->userdata('username');
		$cek = $this->authlib->cek_timeout($username);
		
		if ($cek =! 1) {
			//redirect them to the login page
			echo '<h4 style="margin-top:10px; display:block; text-align:left"><i class="fa fa-warning txt-color-orangeDark"></i> Your login is expired, click <a href="'.base_url().'">here</a> to relogin.</h4>';
			die;
		} */
    }
	
	public function cek_old_pass() {
		
		$old_pass = $this->input->post("old_pass");
		$cek = $this->updatePass->cekOldPass($old_pass);			
		if ($cek > 0) {
			$json = true;
		} else {
			$json = false; 
		}	
		
		echo json_encode($json);	
		
	}
	
	public function index()
	{
		
		$user = "";
		$user = $this->session->userdata('username');
		
		
		if ($user != "") {			
			$data['controller'] 	= $this->gparam['controller'];				
			$data['action'] 		= base_url()."index.php/".$this->gparam['controller']."/crud";
			$data['header'] 		= 'Change Passwod';
			$data['submit'] 		= 'create';	
			
			$this->load->view('crud_change_pass',$data);
			
		} else {
			echo $this->gparam['restrict'];
		}
	}	
	
	
	public function crud() {
		
		$user = "";
		$user = $this->session->userdata('username');
		if ($user == "") {
			echo $this->gparam['restrict']; die();
		} else {
			$crud = $this->input->post('submit');
			if ($crud == 'create') {		
				//print_r($_POST); die();
				$insert = $this->updatePass->insert_data($user);
				if ($insert) {
					$json = true;
				}
				
			}
			//$this->session->set_flashdata("success", "ok"); 
			echo json_encode($json);
		}
		
	}	
	
	function successPage() {
		$this->load->view("success_page");
	}
	
}
