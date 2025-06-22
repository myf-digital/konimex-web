<?php

	require_once('conf_conn.php');
 	$time = getDatetimeNow('[d/M/Y H:i:s]');
    $pathlog = 'log_trans/'.getDatetimeNow('Ym');
    $datenow = getDatetimeNow('Ymd');
	$tglsynch=getDatetimeNow('Y-m-d h:i:s');
    
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	
	if(!is_dir($pathlog)) //create the folder if it's not already exists
    {
      mkdir($pathlog,0755,TRUE);
    } 

	$filelog = $pathlog.'/log_api_'.getDatetimeNow('d.m.Y').'.txt';
	
	// Path to move uploaded files
	$target_path = "uploads/";
	if(!is_dir($target_path)) //create the folder if it's not already exists
	{
	  mkdir($target_path,0755,TRUE);
	} 
	 
// array for final json respone
$response = array();
 
// getting server ip address
$server_ip = $_SERVER['SERVER_ADDR']; //gethostbyname(gethostname());

//$server_ip = '192.168.43.134';
// final file url that is being uploaded
$file_upload_url = 'http://' . $server_ip . '/' . $pathurl . '/' . $target_path;
//echo $server_ip;



  if($_SERVER['REQUEST_METHOD']=='POST'){
	
    $image = @$_POST['image'];
    $name = preg_replace("/\r\n|\r|\n/",'',$_POST['name']);

	$siteid = $_POST['siteid'];
	$periode = $_POST['periode'];
	$image_type = $_POST['image_type'];
	$customerid = $_POST['customerid'];
	$salesmanid = $_POST['salesmanid'];
	
	if ($image_type == 'IMG_OUTLET'){
		$nameimg = getDatetimeNow('Ym').'_'.$salesmanid.'_'.$customerid.'.jpg';
	}elseif ($image_type == 'IMG_CHECKIN'){
		$nameimg = 'checkin_'.getDatetimeNow('Ym').'_'.$salesmanid.'_'.$customerid.'.jpg';
	}elseif ($image_type == 'IMG_CHECKIN_CRC'){
		$nameimg = 'before_'.getDatetimeNow('Ym').'_'.$salesmanid.'_'.$customerid.'.jpg';
	}elseif ($image_type == 'IMG_CHECKOUT_CRC'){
		$nameimg = 'after_'.getDatetimeNow('Ym').'_'.$salesmanid.'_'.$customerid.'.jpg';
	}
    $pathimg = 'uploads/'.$datenow.'/'.$nameimg;

    //if(!file_put_contents($path, base64_decode($image))){
	//	file_put_contents($filelog, $time.$ip." export image failed \n",FILE_APPEND | LOCK_EX);
	//}
	
	
	$conn = mysqli_connect($host, $uname, $pwd, $db);
	if (!$conn) {
		die(file_put_contents($filelog, $time.$ip." Connection failed: " . mysqli_connect_error()."\n",FILE_APPEND | LOCK_EX));
	}else{
		$sqlgetimageold="select image from m_customer_image where siteid='".$siteid."' and salesmanid='".$salesmanid."' and customerid='".$customerid."'";
		//echo $sqlgetimageold;
		$result = mysqli_query($conn,$sqlgetimageold);
		$msg ="";
		/*while ($a_row = mysqli_fetch_array ($result,MYSQL_ASSOC) )
		{
		 if (!@unlink("uploads/".$a_row["image"])){$msg .= "Error deleting ".$a_row["image"].";";} else {$msg .="Deleted ".$a_row["image"].";";}
		}*/

		##Transfer image##
		
		$binary=base64_decode($image);
		header('Content-Type: bitmap; charset=utf-8');
		$file = fopen($pathimg, 'wb');
		fwrite($file, $binary);
		fclose($file);
		file_put_contents($filelog, $time.$ip."Image upload ".$pathimg." complete!! \n",FILE_APPEND | LOCK_EX);

		if ($image_type == 'IMG_OUTLET'){
			$sql = "REPLACE INTO m_customer_image
					(periode,siteid,salesmanid,customerid,image,datecreate,image_type) 
					VALUES ('".$periode."','".$siteid."','".$salesmanid."','".$customerid."','".$datenow."/".$nameimg."','".$tglsynch."','".$image_type."')";
		}elseif ($image_type == 'IMG_CHECKIN'){
			$sql = "update t_sales_rrk_trans set img_checkin = '".$datenow."/".$nameimg."'
					where periode = '".$periode."' and siteid = '".$siteid."' and salesmanid = '".$salesmanid."' and customerid = '".$customerid."'; ";
		}elseif ($image_type == 'IMG_CHECKIN_CRC'){
			$sql = "update t_sales_rrk_trans set img_before = '".$datenow."/".$nameimg."' where periode = '".$periode."' and siteid = '".$siteid."' and salesmanid = '".$salesmanid."' and customerid = '".$customerid."'; ";
		}elseif ($image_type == 'IMG_CHECKOUT_CRC'){
			$sql = "update t_sales_rrk_trans set img_after = '".$datenow."/".$nameimg."' where periode = '".$periode."' and siteid = '".$siteid."' and salesmanid = '".$salesmanid."' and customerid = '".$customerid."'; ";
		}
		
		mysqli_query($conn, $sql) or die(file_put_contents($filelog, $time.$ip." Insert image ".$nameimg." gagal: ".mysqli_error($conn)."\n", FILE_APPEND | LOCK_EX));;
	}
	
	// File successfully uploaded
	$response['customerid'] = $customerid;
	$response['image_name'] = $name;
	$response['status'] = '1';
	$response['msg'] = $msg;
	//$response['error'] = false;
	//$response['file_path'] = $file_upload_url . basename($_FILES['image']['name']);
	file_put_contents($filelog, $time.$ip." Hit Json Upload IMG Success : ".$pathimg." uploaded \n", FILE_APPEND | LOCK_EX);
    
  }else{
	// make error flag true
	$response['status'] = '0';
    //$response['error'] = true;
	file_put_contents($filelog, $time.$ip." Error upload image \n", FILE_APPEND | LOCK_EX);
  }
 
/*if (isset($_FILES['image']['name'])) {

	$target_path = $target_path . basename($_FILES['image']['name']);
 
    // reading other post parameters
    $siteid = isset($_POST['siteid']) ? $_POST['siteid'] : '';
    $salesmanid = isset($_POST['salesmanid']) ? $_POST['salesmanid'] : '';
    $customerid = isset($_POST['customerid']) ? $_POST['salesmanid'] : '';
 
    $response['file_name'] = basename($_FILES['image']['name']);
    $response['siteid'] = $siteid;
    $response['customerid'] = $customerid;
	
    try {
        // Throws exception incase file is not being moved
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $response['error'] = true;
            $response['message'] = 'Could not move the file!';
        }
 
        // File successfully uploaded
        $response['message'] = 'File uploaded successfully!';
        $response['error'] = false;
        $response['file_path'] = $file_upload_url . basename($_FILES['image']['name']);
    
	} catch (Exception $e) {
        // Exception occurred. Make error flag true
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    }
		include "conf_conn.php";
		$conn = mysqli_connect($host, $uname, $pwd, $db);
		if (!$conn) {
			die(file_put_contents($filelog, $time.$ip." Connection failed: " . mysqli_connect_error()."\n",FILE_APPEND | LOCK_EX));
		}else{
			$sql = "INSERT INTO m_customer_image
					(siteid,customerid,image,datecreate) 
					VALUES ('".$siteid."','".$customerid."','".$response['file_name']."','".$tglsynch."')";
			mysqli_query($conn, $sql) or die(file_put_contents($filelog, $time.$ip." insert image gagal: ".mysqli_error($conn)."\n", FILE_APPEND | LOCK_EX));;
		}
		
} else {
    // File parameter is missing
    $response['error'] = true;
    $response['message'] = 'Not received any file!F';
}
*/ 
// Echo final json response to client
echo json_encode($response);

file_put_contents($filelog, $time.$ip." Response Json : ".json_encode($response)."\n", FILE_APPEND | LOCK_EX);

?>