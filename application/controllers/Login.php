<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	var $gparam = array();
	
	public function __construct() {
        parent::__construct();
		//$this->session->sess_destroy();
		//get class name
		$this->gparam['controller'] = $this->router->fetch_class();
		//initiate mode
		$this->load->model('login_model', 'model');
		//load auth library
		$this->load->library('authlib');
    }
	
	public function index()	{

		$username = '';
		//cek session
		$username = $this->session->userdata("username");		
		if ($username != "") {
			redirect('home#mon_sales', 'refresh');			
		}
		$action = base_url()."index.php/login/cek_auth";	
		$data = array(
			"action" => $action,
			"error"  => ""
		); 	
		$this->load->view('login_view',$data);
	}
	
	
	function cek_auth() {
		
		$username = $this->input->post('username');
		$password = $this->input->post('password');		
		$validate = true;//$this->authlib->userAuth($username,$password);		
		//echo "validate".$validate; 
		if ($validate != 1) {
			
			$data['action'] = base_url().'index.php/login/cek_auth'; 
			$data['error'] = '
				<div class="alert alert-danger fade in">
					<button class="close" data-dismiss="alert">x</button>
					'.$validate.'
				</div>
			';
			
			
			$this->load->view('login_view',$data);
			
		} else {
			
			
			//create session
			$session = $this->login_model->create_sess($username); 
			
			if ($session == 1) {
				//get first module
				redirect('home#mon_sales', 'refresh');
			} else {
				echo 'Failed to create session'; die();
			}
		}
	}
	
	function cek_user() {
		$username = $this->input->post('username');
		$cek = $this->model->cek_user_valid($username);
		
		if ($cek > 0) {
			$json = true;
		} else {
			$json = false;
		}		
		echo json_encode($json);
	}
	
	function logout() {
		$username = $this->session->userdata("username");
		$log_out = $this->model->logout_process($username);
		
		if ($log_out) {
			redirect('login','refresh');
		}
	}
	
}
