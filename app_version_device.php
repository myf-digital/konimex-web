<?php

	include "conf_conn.php";

	$json = file_get_contents('php://input');
	$obj = json_decode($json,true);
	$cntobj = count($obj["version"]);
	//print "json count :".$cntobj;

	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	$time = getDatetimeNow('[d/M/Y H:i:s]');
    $path = 'log_trans/'.getDatetimeNow("Ym");

    if(!is_dir($path)) //create the folder if it's not already exists
    {
      mkdir($path,0755,TRUE);
    } 

	$filelog = $path.'/log_api_'.getDatetimeNow("d.m.Y").'.txt';

	file_put_contents($filelog, $time.$ip." Hit Json : ".json_encode($obj)."\n", FILE_APPEND | LOCK_EX);

	if ($cntobj>0)
	{
		$siteid = strtoupper($obj['siteid']);
		$companyid = $obj['companyid'];
		$salesmanid = $obj['salesmanid'];
		$version = $obj['version'];
		$deviceid = $obj['deviceid'];
		$device = $obj['device'];
		$msisdn1 = $obj['msisdn1'];
		$msisdn2 = $obj['msisdn2'];
		$imei = $obj['imei'];
		$versioncode = $obj['versioncode'];
		$operator = $obj['operator'];

		$connhost = mysqli_connect($host, $uname, $pwd, $db);
		if (!$connhost) {
			die(file_put_contents($filelog, $time.$ip." Connection host failed: " . mysqli_connect_error()."\n",FILE_APPEND | LOCK_EX));
		}else {
			if ($siteid!='')
			{
				$sqlexec="update m_sales_salesman set app_version='".$version."',deviceid='".$deviceid."',device='".$device."',
						  msisdn1='".$msisdn1."',msisdn2='".$msisdn2."',imei='".$imei."',last_sync=now(),versioncode='".$versioncode."',operator='".$operator."' 
						  where siteid='".$siteid."' and salesmanid='".$salesmanid."'";
				$result = mysqli_query($connhost, $sqlexec);
				$sqlgetversion="select latest_version,versioncode from version_android";
				$resultversion = mysqli_query($connhost, $sqlgetversion);
				$row=mysqli_fetch_array($resultversion,MYSQLI_ASSOC);
				
				if(!$result)
				{
					print ('{"respon":[{"status":"2","msg":"Execute Error"}]}');
					die(file_put_contents($filelog, $time.$ip." Execute Error: " . mysqli_connect_error()."\n",FILE_APPEND | LOCK_EX));
					exit();
				}
				print json_encode($row);
				//print ('{"version":"latest version"}]}');
				
				
			} else die(file_put_contents($filelog, $time.$ip." koneksi ke site local gagal karena siteid kosong \n",FILE_APPEND | LOCK_EX));

		}

	

		mysqli_close($conn);

		file_put_contents($filelog, $time.$ip." Success appversion ".$salesmanid." \n"."Response : ".$json, FILE_APPEND | LOCK_EX);

	} 

	else

	{

		file_put_contents($filelog, $time.$ip." appversion failed, jsoon req null \n"."Response : ".$json, FILE_APPEND | LOCK_EX);

	}

?>