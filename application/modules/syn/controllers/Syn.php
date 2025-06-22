<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Syn extends CI_Controller {

		

	public function __construct() {

       parent::__construct();

	    $this->load->model('syn_model', 'syn');
    }

	function writeLog($json,$res) {
		//log
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {$ip = $_SERVER['HTTP_CLIENT_IP'];} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 
		{$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];} else {$ip = $_SERVER['REMOTE_ADDR'];}
		$time = @date('[d/M/Y:H:i:s]');
		$path = 'log_trans_spv/'.date("Yn");
		if(!is_dir($path)) {mkdir($path,0755,TRUE);} 
		$filelog = $path.'/log_api_spv_'.date("j.n.Y").'.txt';

		file_put_contents($filelog, $time.$ip." ".$res." Json : ".json_encode($json)."\n", FILE_APPEND | LOCK_EX);
		//end log
	}

	function index() {
	}

	function get_list_salesman() {
		//recive post json
		$json = file_get_contents('php://input');

		//decode json
		$post 	= json_decode($json,true);
		$siteid = $post['siteid'];

		$this->writeLog($json,'Request');
		
		$return = $this->syn->get_data_salesman($siteid);
		echo json_encode($return);
		$this->writeLog($return,'Response');
	}	

	function get_list_order() {
		//recive post json
		$json = file_get_contents('php://input');

		//decode json
		$post 	= json_decode($json,true);
		$siteid = $post['siteid'];
		$salesmanid = $post['salesmanid'];
		
		$this->writeLog($json,'Request');
		
		$return = $this->syn->get_data_list_order($siteid,$salesmanid);
		echo json_encode($return);
		$this->writeLog($return,'Response');
	}		
	
	function get_list_order_customer() {
		//recive post json
		$json = file_get_contents('php://input');

		//decode json
		$post 	= json_decode($json,true);
		$siteid = $post['siteid'];
		$salesmanid = $post['salesmanid'];
		$customerid = $post['customerid'];
		
		$this->writeLog($json,'Request');
		
		$return = $this->syn->get_data_list_order_customer($siteid,$salesmanid,$customerid);
		echo json_encode($return);
		$this->writeLog($return,'Response');
	}
	
	function get_view_detail() {
		//recive post json
		$json = file_get_contents('php://input');

		//decode json
		$post 	= json_decode($json,true);
		$siteid = $post['siteid'];
		$salesmanid = $post['salesmanid'];
		$customerid = $post['customerid'];
		$no_sales = $post['no_sales'];
		$status = $post['no_sales'];
		
		$this->writeLog($json,'Request');
		
		$return = $this->syn->get_data_view_detail($siteid,$salesmanid,$customerid,$no_sales);
		echo json_encode($return);
		$this->writeLog($return,'Response');
	}		
	
	function get_act_approved() {
		//recive post json
		$json = file_get_contents('php://input');

		//decode json
		$post 	= json_decode($json,true);
		$spvid = $post['spvid'];
		$no_sales = $post['no_sales'];
		
		$this->writeLog($json,'Request');
		
		$return = $this->syn->get_approved($spvid,$no_sales);
		echo json_encode($return);
		$this->writeLog($return,'Response');
	}	

	function get_productivity_sales() {
		//recive post json
		$json = file_get_contents('php://input');

		//decode json
		$post 	= json_decode($json,true);
		$siteid = $post['siteid'];
		
		$this->writeLog($json,'Request');
		$return = $this->syn->get_data_productivity($siteid);
		
		echo json_encode($return);
		$this->writeLog($return,'Response');
	}	

	function get_target_per_prinsipal() {
		//recive post json
		$json = file_get_contents('php://input');

		//decode json
		$post 	= json_decode($json,true);
		$siteid = $post['siteid'];
		
		$this->writeLog($json,'Request');
		$return = $this->syn->get_data_target_per_prinsipal($siteid);
		
		echo json_encode($return);
		$this->writeLog($return,'Response');
	}		

	function get_target_per_group() {
		//recive post json
		$json = file_get_contents('php://input');

		//decode json
		$post 	= json_decode($json,true);
		$siteid = $post['siteid'];
		
		$this->writeLog($json,'Request');
		$return = $this->syn->get_data_target_per_group($siteid);
		
		echo json_encode($return);
		$this->writeLog($return,'Response');
	}		

	function get_scheduler_actual() {
		//recive post json
		$json = file_get_contents('php://input');

		//decode json
		$post 	= json_decode($json,true);
		$siteid = $post['siteid'];
		
		$this->writeLog($json,'Request');
		$return = $this->syn->get_data_schedule_actual($siteid);
		
		echo json_encode($return);
		$this->writeLog($return,'Response');
	}		

	function get_list_order_by_prinsipal() {
		//recive post json
		$json = file_get_contents('php://input');

		//decode json
		$post 	= json_decode($json,true);
		$siteid = $post['siteid'];

		$this->writeLog($json,'Request');
		
		$return = $this->syn->get_data_list_order_by_prinsipal($siteid);
		echo json_encode($return);
		$this->writeLog($return,'Response');
	}
	
	function get_view_detail_by_prinsipal() {
		//recive post json
		$json = file_get_contents('php://input');

		//decode json
		$post 	= json_decode($json,true);
		$siteid = $post['siteid'];
		$prinsipalid = $post['prinsipalid'];
		
		$this->writeLog($json,'Request');
		
		$return = $this->syn->get_data_detail_order_by_prinsipal($siteid,$prinsipalid);
		echo json_encode($return);
		$this->writeLog($return,'Response');
	}		


	function get_point_maps() {
		//recive post json
		$json = file_get_contents('php://input');

		//decode json
		$post 	= json_decode($json,true);
		$siteid = $post['siteid'];
		$sid = $post['salesmanid'];
		
		$this->writeLog($json,'Request');
		
		$return['point_sales_customer'] = $this->syn->get_point_maps($siteid,$sid);
		$return['point_sales_tracking'] = $this->syn->get_tracking($siteid,$sid);
		
		echo json_encode($return);
		$this->writeLog($return,'Response');
	}
	
	function get_data_dashboard_daily() {
		//recive post json
		$json = file_get_contents('php://input');

		//decode json
		$post 	= json_decode($json,true);
		
		$companyid = $post['companyid'];
		$siteid = $post['siteid'];
		$periode = $post['periode'];
		//$companyid = $_POST['companyid'];
		//$siteid = $_POST['siteid'];
		//$periode = $_POST['periode'];
		
		$this->writeLog($json,'Request');
		$return['t_daily_order'] = $this->syn->get_data_daily_order($companyid,$siteid,$periode);
		$return['t_daily_sales'] = $this->syn->get_data_daily_sales($companyid,$siteid,$periode);
		$return['t_monthly_sales'] = $this->syn->get_data_monthly_sales($companyid,$siteid,$periode);
		echo json_encode($return);
		$this->writeLog($return,'Response');
	}

	function upload_image()
	{
		$dcreate = @date('Ymd');
		$date = @date('Y-m-d H:i:s');
		
		$image = base64_decode($this->input->post("image"));
		$name = preg_replace("/\r\n|\r|\n/",'',$this->input->post("name"));
		$siteid = $this->input->post("siteid");
		$periode = $this->input->post("periode");
		$image_type = $this->input->post("image_type");
		$customerid = $this->input->post("customerid");
		$salesmanid = $this->input->post("salesmanid");

		if ($image_type == 'IMG_OUTLET'){
			$image_name = $dcreate.'_'.$salesmanid.'_'.$customerid.'.jpg';
		}elseif ($image_type == 'IMG_CHECKIN'){
			$image_name = 'checkin_'.$dcreate.'_'.$salesmanid.'_'.$customerid.'.jpg';
		}elseif ($image_type == 'IMG_CHECKIN_CRC'){
			$image_name = 'before_'.$dcreate.'_'.$salesmanid.'_'.$customerid.'.jpg';
		}elseif ($image_type == 'IMG_CHECKOUT_CRC'){
			$image_name = 'after_'.$dcreate.'_'.$salesmanid.'_'.$customerid.'.jpg';
		}
		
		$filename = $image_name;
		//rename file name
		$path = "uploads/".$dcreate;
		if(!is_dir($path)) { mkdir($path,0755,TRUE); } 

		$pathfile = $path."/".$filename;
		//image uploading folder path
		file_put_contents($pathfile, $image);
		// image is bind and upload to respective folder

		$data_insert = array('periode'=>$periode, 'siteid'=>$siteid, 'salesmanid'=>$salesmanid, 'customerid'=>$customerid, 'image'=>$dcreate."/".$filename, 'datecreate'=>$date, 'image_type'=>$image_type);

		$success = $this->syn->insert_image($data_insert);
		if($success){
			$response['customerid'] = $customerid;
			$response['image_name'] = $name;
			$response['image_type'] = $image_type;
			$response['status'] = '1';
			$response['msg'] = "Image Successfully..";
		}
		else
		{
			$response['customerid'] = $customerid;
			$response['image_name'] = $name;
			$response['image_type'] = $image_type;
			$response['status'] = '0';
			$response['msg'] = "Some Error Occured. Please Try Again..";
		}
		echo json_encode($response);
		$this->writeLog($response,'Response');
	}
}

