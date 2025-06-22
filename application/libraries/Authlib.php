<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name			: AuthLib
* Created By	: Indra Hasan
* Created Date	: 27-9-2015
* Modified By	:
* Modified Date	:
* Description	: Libraries For Auth user  
**/

class Authlib {
	
	public function __construct() {
		//user login_model as a model		
		$CI =& get_instance();
		$CI->load->model('login_model');
	}
	
	//function destroy session
	function destroy_session() {
		$CI =& get_instance();
		$CI->login_model->session_destroy();
	}
	
	
	//cek timeout session
	function cek_timeout() {
		$CI =& get_instance();
		$username = "";
		$username = $CI->session->userdata('username');
		if ($username == "") {
			$cek = 0;
			
		} else {
			$cek = $CI->login_model->cek_timeout_session($username);
				
		}
		return $cek;
	}
	
	//login 
	function userAuth($username,$password) {		
		
		$CI =& get_instance();
		//cek session exsist 
		$cek = $CI->login_model->cek_session_exsis($username);
		
		//cek userlock		
		
		if ($cek == 1) {
			$cek = $CI->login_model->cek_user_status($username);		
		}
		//echo $cek; die();
		//cek user/pass
		
		if ($cek == 1) {
			$cek = $CI->login_model->cek_user_pass($username,$password);				
		} 
		
		
		//echo $cek; die();
		return $cek;
 		
	}	
}