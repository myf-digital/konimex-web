<?php
	include "conf_conn.php";

	$json = file_get_contents('php://input');
	$obj = json_decode($json,true);
	$cntobj = count($obj["login"]);
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
		$siteid = strtoupper($obj["login"][0]['siteid']);
		$companyid = $obj["login"][0]['companyId'];
		$salesmanid = $obj["login"][0]['salesmanid'];
		$password = $obj["login"][0]['password'];
		$connhost = mysqli_connect($host, $uname, $pwd, $db);
		if (!$connhost) {
			die(file_put_contents($filelog, $time.$ip." Connection host failed: " . mysqli_connect_error()."\n",FILE_APPEND | LOCK_EX));
		}else {
			if ($siteid!='')
			{
				$sqlconn="select * from m_setupsite_db where siteid='".$siteid."' and companyid='".$companyid."'";
				$stringconn = mysqli_query($connhost, $sqlconn);
				$rowcon=mysqli_num_rows($stringconn);
				if($rowcon==0)
				{
					print ('{"respon":[{"status":"2","msg":"Koneksi ke Site = '.$siteid.' tidak terkoneksi"}]}');
					exit();
				}
				else
				{
					$row=mysqli_fetch_array($stringconn,MYSQLI_ASSOC);
					$conn = mysqli_connect($row["hostname"], $row["user"], $row["pwd"], $row["dbname"]);
				}
				if (!$conn){
					print ('{"respon":[{"status":"2","msg":"Koneksi ke Site = '.$siteid.' tidak terkoneksi"}]}');
					die(file_put_contents($filelog, $time.$ip." Connection site failed: " . mysqli_connect_error()."\n",FILE_APPEND | LOCK_EX));
				}
			} else die(file_put_contents($filelog, $time.$ip." koneksi ke site local gagal karena siteid kosong \n",FILE_APPEND | LOCK_EX));
		}
	
		$sql = "select siteid,salesmanid,password
				from m_sales_salesman
				where siteid='".$siteid."' and salesmanid='".$salesmanid."' and password='".md5($password)."' and companyid='".$companyid."'";
		$result = mysqli_query($conn, $sql) or die(file_put_contents($filelog, $time.$ip." select username dan password tabel m_sales_salesman gagal: ".mysqli_error($conn)."\n", FILE_APPEND | LOCK_EX));
		
		$sql1 = "select a.siteid,a.nama_site,'' tanggal, b.companyid, b.company
				from m_setup_site a, m_company b
				where a.companyid=b.companyid and a.siteid='".$siteid."' and a.companyid='".$companyid."'";
		$array1 = array();
		$result1 = mysqli_query($conn, $sql1) or die(file_put_contents($filelog, $time.$ip."select tabel m_setup_site gagal: ".mysqli_error($conn)."\n", FILE_APPEND | LOCK_EX));
		
		$sql2 = "select siteid,salesmanid,gudangid,nama_salesman,supervisorid,tipe_db,aktif,password,tipe_sales,tipe_trans,
						categoryid,nilai_sales,last_sync,'0' status
				from m_sales_salesman 
				where salesmanid='".$salesmanid."' and siteid='".$siteid."' and companyid='".$companyid."' and aktif='0'";
		$array2 = array();
		$result2 = mysqli_query($conn, $sql2) or die(file_put_contents($filelog, $time.$ip." select cek aktif status gagal: ".mysqli_error($conn)."\n", FILE_APPEND | LOCK_EX));

		$sql3 = "select siteid,api_name,ip,port,url,api_kategori
				from  m_setup_api
				where siteid='".$siteid."' and companyid='".$companyid."'";
		$array3 = array();
		$result3 = mysqli_query($connhost, $sql3) or die(file_put_contents($filelog, $time.$ip." Setup Api gagal : ".mysqli_error($connhost)."\n", FILE_APPEND | LOCK_EX));
		$sql4 = "select siteid,salesmanid,gudangid,nama_salesman,supervisorid,tipe_db,aktif,password,tipe_sales,tipe_trans,
						categoryid,nilai_sales,last_sync,'0' status
				from m_sales_salesman 
				where salesmanid='".$salesmanid."' and siteid='".$siteid."' and companyid='".$companyid."'";
		$array4 = array();
		$result4 = mysqli_query($conn, $sql4) or die(file_put_contents($filelog, $time.$ip." select cek salesman register status gagal: ".mysqli_error($conn)."\n", FILE_APPEND | LOCK_EX));
		
		$rowcount=mysqli_num_rows($result);
		$rowcount1=mysqli_num_rows($result1);
		$rowcount2=mysqli_num_rows($result2);
		$rowcount3=mysqli_num_rows($result3);
		$rowcount4=mysqli_num_rows($result4);
		if ($rowcount4==0)
		{
			print ('{"respon":[{"status":"2","msg":"Salesman belum terdaftar"}]}');
		}
		else 
		if ($rowcount==0)
		{
			print ('{"respon":[{"status":"2","msg":"Password yang anda masukan salah"}]}');
		}
		else 
		if ($rowcount1==0)
		{
			print ('{"respon":[{"status":"2","msg":"Site atau Company tidak terdaftar"}]}');
		}
		else 
		if ($rowcount2==0)
		{
			print ('{"respon":[{"status":"2","msg":"Salesman '.$salesmanid.' sudah terinstal aplikasi sphere"}]}');
		}
		else 
		if ($rowcount3==0)
		{
			print ('{"respon":[{"status":"2","msg":"Invalid config API"}]}');
		}
		else 
		{
			 if ($siteid!="")
			{
				while($row1=mysqli_fetch_assoc($result1)){ $array1[] = $row1; }
				while($row2=mysqli_fetch_assoc($result2)){ $array2[] = $row2; }
				while($row=mysqli_fetch_assoc($result3)){ $array3[] = $row; }
				$respons = array(
							"respon" => array(
											array(
											"status" => "1",
											"msg" => "Login berhasil",
											"companyid" => $companyid
											)
										),
							"m_setup_site" => $array1, 
							"m_sales_salesman" => $array2,
							"m_setup_api" => $array3
							);
				
			echo json_encode($respons);
			}
			else if ($siteid==""){print ('{"respon":[{"status":"2","msg":"SiteId dan CompanyId harus di isi"}]}');}
		}
		
		mysqli_close($connhost);
		mysqli_close($conn);
		file_put_contents($filelog, $time.$ip." Success login ".$salesmanid." \n", FILE_APPEND | LOCK_EX);
	} 
	else
	{
		file_put_contents($filelog, $time.$ip." login failed, jsoon req null\n", FILE_APPEND | LOCK_EX);
	}
	//file_put_contents($filelog, $time.$ip."; Response : ".." \n", FILE_APPEND | LOCK_EX);

?>