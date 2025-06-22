<?php

	require_once('conf_conn.php');
 	$time = getDatetimeNow('[d/M/Y H:i:s]');
    $pathdate = getDatetimeNow('Ymd');
	
	$path = 'log_trans/'.$pathdate;
    $pathimage = 'uploads/'.$pathdate;
	
	$tglsynch=getDatetimeNow('Y-m-d h:i:s');
	//$tglsynch=getDatetimeNow('Y-m-d');
    
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	// Path to move uploaded files
	$target_path = "uploads/";
	if(!is_dir($target_path)) //create the folder if it's not already exists
	{
	  mkdir($target_path,0755,TRUE);
	} 
	
	if(!is_dir($path)) //create the folder if it's not already exists
    {
      mkdir($path,0755,TRUE);
    } 

	if(!is_dir($pathimage)) //create the folder if it's not already exists
    {
      mkdir($pathimage,0755,TRUE);
    } 

	$filelog = $path.'/log_api_'.getDatetimeNow('d.m.Y').'.txt';
	 
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
    $image_checkin = @$_POST['image_checkin'];
    $image_before = @$_POST['image_before'];
    $image_after = @$_POST['image_after'];
    $image_act_comp = @$_POST['image_act_comp'];
	
    $name = preg_replace("/\r\n|\r|\n/",'',@$_POST['name']);
    $name_checkin = preg_replace("/\r\n|\r|\n/",'',@$_POST['name_checkin']);
    $name_before = preg_replace("/\r\n|\r|\n/",'',@$_POST['name_before']);
    $name_after = preg_replace("/\r\n|\r|\n/",'',@$_POST['name_after']);
    $name_act_comp = preg_replace("/\r\n|\r|\n/",'',@$_POST['name_act_comp']);
	
	$keterangan = @$_POST['keterangan'];
	$periode = $_POST['periode'];
	$customerid = $_POST['customerid'];
	$salesmanid = $_POST['salesmanid'];
	$nameimg = getDatetimeNow('Ym').'_'.$salesmanid.'_'.$customerid.'.jpg';
	$nameimg_checkin = 'checkin_'.getDatetimeNow('Ym').'_'.$salesmanid.'_'.$customerid.'.jpg';
	$nameimg_before = 'before_'.getDatetimeNow('Ym').'_'.$salesmanid.'_'.$customerid.'.jpg';
	$nameimg_after = 'after_'.getDatetimeNow('Ym').'_'.$salesmanid.'_'.$customerid.'.jpg';
	$nameimg_act_comp = 'act_comp_'.getDatetimeNow('Ym').'_'.$salesmanid.'_'.$customerid.'.jpg';
	$siteid = $_POST['siteid'];
	
    $path = "$pathimage/$nameimg";
    $pathcheckin = "$pathimage/$nameimg_checkin";
    $pathbefore = "$pathimage/$nameimg_before";
    $pathafter = "$pathimage/$nameimg_after";
    $pathactcomp = "$pathimage/$nameimg_act_comp";

    //if(!file_put_contents($path, base64_decode($image))){
	//	file_put_contents($filelog, $time.$ip." export image failed \n",FILE_APPEND | LOCK_EX);
	//}
	
	
	$conn = mysqli_connect($host, $uname, $pwd, $db);
	if (!$conn) {
		die(file_put_contents($filelog, $time.$ip." Connection failed: " . mysqli_connect_error()."\n",FILE_APPEND | LOCK_EX));
	}else{
		//$sqlgetimageold="select image from m_customer_image where siteid='".$siteid."' and salesmanid='".$salesmanid."' and customerid='".$customerid."' and datecreate='".$tglsynch."'";
		//echo $sqlgetimageold;
		//$result = mysqli_query($conn,$sqlgetimageold);
		//$msg ="";
		/*while ($a_row = mysqli_fetch_array ($result,MYSQL_ASSOC) )
		{
		 if (!@unlink("uploads/".$a_row["image"])){$msg .= "Error deleting ".$a_row["image"].";";} else {$msg .="Deleted ".$a_row["image"].";";}
		}*/

		##Transfer image##
		if ($image){
			$binary=base64_decode($image);
			header('Content-Type: bitmap; charset=utf-8');
			$file = fopen($path, 'wb');
			fwrite($file, $binary);
			fclose($file);
			file_put_contents($filelog, $time.$ip."Image outlet upload complete!! \n",FILE_APPEND | LOCK_EX);

			$sql = "REPLACE INTO m_customer_image
					(siteid,salesmanid,customerid,image,datecreate) 
					VALUES ('".$siteid."','".$salesmanid."','".$customerid."','".$pathdate.'/'.$nameimg."','".$tglsynch."')";
			mysqli_query($conn, $sql) or die(file_put_contents($filelog, $time.$ip." insert image gagal: ".mysqli_error($conn)."\n", FILE_APPEND | LOCK_EX));;
		}
		
		if($image_checkin)
		{
			$binary=base64_decode($image_checkin);
			header('Content-Type: bitmap; charset=utf-8');
			$file = fopen($pathcheckin, 'wb');
			fwrite($file, $binary);
			fclose($file);
			file_put_contents($filelog, $time.$ip."Image checkin upload complete!! \n",FILE_APPEND | LOCK_EX);

			$sql = "update t_sales_rrk_trans set img_checkin = '".$pathdate.'/'.$nameimg_checkin."'
					where periode = '".$periode."' and siteid = '".$siteid."' and salesmanid = '".$salesmanid."' and customerid = '".$customerid."'; ";
			mysqli_query($conn, $sql) or die(file_put_contents($filelog, $time.$ip." insert image checkin gagal: ".mysqli_error($conn)."\n", FILE_APPEND | LOCK_EX));;
			
		}

		if($image_before)
		{
			$binary=base64_decode($image_before);
			header('Content-Type: bitmap; charset=utf-8');
			$file = fopen($pathbefore, 'wb');
			fwrite($file, $binary);
			fclose($file);
			file_put_contents($filelog, $time.$ip."Image before upload complete!! \n",FILE_APPEND | LOCK_EX);

			$sql = "update t_sales_rrk_trans set img_before = '".$pathdate.'/'.$nameimg_before."' where periode = '".$periode."' and siteid = '".$siteid."' and salesmanid = '".$salesmanid."' and customerid = '".$customerid."'; ";
			mysqli_query($conn, $sql) or die(file_put_contents($filelog, $time.$ip." insert image before gagal: ".mysqli_error($conn)."\n", FILE_APPEND | LOCK_EX));;
			
		}

		if($image_after)
		{
			$binary=base64_decode($image_after);
			header('Content-Type: bitmap; charset=utf-8');
			$file = fopen($pathafter, 'wb');
			fwrite($file, $binary);
			fclose($file);
			file_put_contents($filelog, $time.$ip."Image after upload complete!! \n",FILE_APPEND | LOCK_EX);

			$sql = "update t_sales_rrk_trans set img_after = '".$pathdate.'/'.$nameimg_after."' where periode = '".$periode."' and siteid = '".$siteid."' and salesmanid = '".$salesmanid."' and customerid = '".$customerid."'; ";
			mysqli_query($conn, $sql) or die(file_put_contents($filelog, $time.$ip." insert image after gagal: ".mysqli_error($conn)."\n", FILE_APPEND | LOCK_EX));;
			
		}

		if($image_act_comp)
		{
			$binary=base64_decode($image_act_comp);
			header('Content-Type: bitmap; charset=utf-8');
			$file = fopen($pathactcomp, 'wb');
			fwrite($file, $binary);
			fclose($file);
			file_put_contents($filelog, $time.$ip."Image act competitor upload complete!! \n",FILE_APPEND | LOCK_EX);

			$sql = "REPLACE INTO t_activity_competitor
					(periode,salesmanid,customerid,img_competitor,keterangan,datecreate)
					VALUES ('".$periode."','".$salesmanid."','".$customerid."','".$pathdate.'/'.$nameimg_act_comp."','".$keterangan."','".$tglsynch."')";
			mysqli_query($conn, $sql) or die(file_put_contents($filelog, $time.$ip." insert image act competitor gagal: ".mysqli_error($conn)."\n", FILE_APPEND | LOCK_EX));;
			
		}
	}
	
	// File successfully uploaded
	$response['customerid'] = $customerid;
	$response['image_name'] = $name;
	$response['status'] = '1';
	$response['msg'] = $msg;
	//$response['error'] = false;
	//$response['file_path'] = $file_upload_url . basename($_FILES['image']['name']);
	file_put_contents($filelog, $time.$ip." Hit Json Upload IMG Success : uploaded \n", FILE_APPEND | LOCK_EX);
    
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