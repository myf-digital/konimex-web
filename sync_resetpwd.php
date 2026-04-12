<?php

	include "conf_conn.php";



	$json = file_get_contents('php://input');

	$obj = json_decode($json,true);

	$cntobj = count($obj["reset"]);



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

	

	file_put_contents($filelog, $time.$ip." Reset Password; Hit Json : ".json_encode($obj)."\n", FILE_APPEND | LOCK_EX);



	if ($cntobj>0)

	{

		$siteid = strtoupper($obj["reset"][0]['siteid']);

		$companyid = strtoupper($obj["reset"][0]['companyId']);

		$connhost = mysqli_connect($host, $uname, $pwd, $db);

		if (!$connhost) {

			die(file_put_contents($filelog, $time.$ip." Connection host failed: " . mysqli_connect_error()."\n",FILE_APPEND | LOCK_EX));

		}else {

			$sqlconn="select * from m_setupsite_db where siteid='".$siteid."'";
			//' and companyid='".$companyid."'";
            //
			$stringconn = mysqli_query($connhost, $sqlconn);
            //
			$row=mysqli_fetch_array($stringconn,MYSQLI_ASSOC);



			//$conn = mysqli_connect($row["hostname"], $row["user"], $row["pwd"], $row["dbname"]);
			$conn = mysqli_connect('localhost:3306', 'root', '', 'monitoring');

			if (!$conn){

				die(file_put_contents($filelog, $time.$ip." Connection site failed: " . mysqli_connect_error()."\n",FILE_APPEND | LOCK_EX));

			}

		}

	

		$sql = "update m_sales_salesman set password='".md5($obj["reset"][0]['password'])."', aktif='1', status='1' where siteid='".$siteid."' and salesmanid='".$obj["reset"][0]['salesmanid']."'";

		$result = mysqli_query($connhost, $sql) or die(file_put_contents($filelog, $time.$ip."select username dan password tabel m_sales_salesman gagal: ".mysqli_error($conn)."\n", FILE_APPEND | LOCK_EX));

		mysqli_close($connhost);



		if (!$result)

		{

			print ('{"respon":[{"status":"2","msg":"Reset password failed"}]}');

		}

		else 

		{

			print ('{"respon":[{"status":"1","msg":"Reset password success"}]}');

		}

		//mysqli_close($conn);

		file_put_contents($filelog, $time.$ip." Success reset password ".$obj["reset"][0]['salesmanid']."\n", FILE_APPEND | LOCK_EX);

	}else{

		file_put_contents($filelog, $time.$ip." Empty request \n", FILE_APPEND | LOCK_EX);

	}

	



?>