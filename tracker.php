<?php

//Request :  {"t_tracker_salesman":[{"periode":"2016-05-21","salesmanid":"1401100008","siteid":"CRB002","latitude_cell":"123","longitude_cell":"321"}]}
//Response : success -> {"respon":"1"} ; error -> {"respon":[{"status":"0","msg":"Koneksi ke database server local bermasalah"}]}
	include "conf_conn.php";

	$json = file_get_contents('php://input');
	$obj = json_decode($json,true);
	$cntobj = count($obj["t_tracker_salesman"]);
	//print "json count :".$cntobj;

	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
 	$time = getDatetimeNow('[d/M/Y H:i:s]');
    $path = 'log_trans/'.getDatetimeNow('Ym');
	$tglsynch=getDatetimeNow('Y-m-d h:i:s');

    if(!is_dir($path)) //create the folder if it's not already exists
    {
      mkdir($path,0755,TRUE);
    } 

	$filelog = $path.'/log_api_'.getDatetimeNow('d.m.Y').'.txt';
	
	file_put_contents($filelog, $time.$ip." Hit Json : ".json_encode($obj)."\n", FILE_APPEND | LOCK_EX);
	
	if ($cntobj>0)
	{
		
		$siteid = strtoupper($obj["t_tracker_salesman"][0]['siteid']);
		$periode = $obj["t_tracker_salesman"][0]['periode'];
		if( strlen($obj['t_tracker_salesman'][0]['periode']) > 10) {
			$obj['t_tracker_salesman'][0]['periode']=substr($obj['t_tracker_salesman'][0]['periode'], 0, -9);
		}
		$salesmanid = $obj["t_tracker_salesman"][0]['salesmanid'];
		$connhost = @mysqli_connect($host, $uname, $pwd, $db);
		if (!$connhost) {
			print ('{"respon":[{"status":"0","msg":"Koneksi ke database server local bermasalah"}]}');
			die(file_put_contents($filelog, $time.$ip." Connection host failed: " . mysqli_connect_error()."\n",FILE_APPEND | LOCK_EX));
		}else {
			if ($siteid!='')
			{
				$sqlconn="select * from m_setupsite_db where siteid='".$siteid."'";
				$stringconn = mysqli_query($connhost, $sqlconn);
				$rowcon=mysqli_num_rows($stringconn);
				if($rowcon==0)
				{
					print ('{"respon":[{"status":"0","msg":"Koneksi ke database server local bermasalah"}]}');
					die(file_put_contents($filelog, $time.$ip.'" Koneksi ke Site = '.$siteid.' belum ter configure failed: "' . mysqli_connect_error()."\n",FILE_APPEND | LOCK_EX));
				}
				else
				{
					$row=mysqli_fetch_array($stringconn,MYSQLI_ASSOC);
					$conn = @mysqli_connect($row["hostname"], $row["user"], $row["pwd"], $row["dbname"]);
				}
				if (!$conn){
					print ('{"respon":[{"status":"0","msg":"Koneksi ke database server local bermasalah"}]}');
					die(file_put_contents($filelog, $time.$ip." Connection site failed: " . mysqli_connect_error()."\n",FILE_APPEND | LOCK_EX));
				}
			} else {
				die(file_put_contents($filelog, $time.$ip." koneksi ke site local gagal karena siteid kosong \n",FILE_APPEND | LOCK_EX));
			}
		}
		for($i=0; $i<$cntobj; $i++){
		$sql = "insert into t_tracker_salesman (periode,siteid,salesmanid,latitude_cell,longitude_cell,createdate) 
				values ('".$periode."',
						'".$siteid."',
						'".$salesmanid."',
						'".$obj['t_tracker_salesman'][$i]['latitude_cell']."',
						'".$obj['t_tracker_salesman'][$i]['longitude_cell']."',
						'".$obj['t_tracker_salesman'][$i]['createdate']."')";
		$result = mysqli_query($conn, $sql) or die(
					file_put_contents($filelog, $time.$ip." Tracker salesman(".$salesmanid.") gagal : ".$sql." - ".mysqli_error($conn)."\n", FILE_APPEND | LOCK_EX)
					);

		}
		
		mysqli_close($connhost);
		mysqli_close($conn);
		//file_put_contents($filelog, $time.$ip." Success tracker ".$salesmanid." \n", FILE_APPEND | LOCK_EX);
			$respons = array(
						"respon" => "1"
						);
		echo json_encode($respons);
	} 
	else
	{
		file_put_contents($filelog, $time.$ip." Tracker failed, jsoon req null\n", FILE_APPEND | LOCK_EX);
	}

?>