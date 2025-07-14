<?php 

include "conf_conn.php";

$today = getDatetimeNow('Y-m-d');

//header("Access-Control-Allow-Origin: *");
//header("Content-Type: application/json; charset=UTF-8");
//$jsonpar=$_POST['jsonpar'];
$json = file_get_contents('php://input');
$obj = json_decode($json);
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

	$filelog = $path.'/log_api_'.getDatetimeNow('d.m.Y').'.txt';
	
	file_put_contents($filelog, $time.$ip." Hit Json RRK Next Days : ".json_encode($obj)."\n", FILE_APPEND | LOCK_EX);

	//$reqSalesRrk = count($obj['req_rrk_nextdays']); //status_send = 0
	
	if (!empty($obj->tanggal))
	{
		$obj->tanggal=(empty($obj->tanggal)?"":$obj->tanggal);
		$siteid = strtoupper($obj->siteid);
		$salesmanid = $obj->salesmanid;
		$tglsynch=getDatetimeNow("Y-m-d H:i:s");
		// trim if has length >10
		$obj->tanggal=(empty($obj->tanggal)?'':$obj->tanggal);
		if( strlen($obj->tanggal) > 10) {
			$obj->tanggal=substr($obj->tanggal, 0, -9);
		}
		$tglsetup=$obj->tanggal;

		$connhost = @mysqli_connect($host, $uname, $pwd, $db);
		if (!$connhost) {
			print ('{"respon":[{"status":"0","msg":"Koneksi ke database server local bermasalah"}]}');
			die(file_put_contents($filelog, $time.$ip." Connection host failed: " . mysqli_connect_error()."\n",FILE_APPEND | LOCK_EX));
		}else {
			$sqlconn="select * from m_setupsite_db where siteid='".$siteid."' limit 1";
			$stringconn = mysqli_query($connhost, $sqlconn);
			$row=mysqli_fetch_array($stringconn,MYSQLI_ASSOC);
			mysqli_close($connhost);

			$conn = @mysqli_connect($row["hostname"], $row["user"], $row["pwd"], $row["dbname"]);
			if (!$conn){
				print ('{"respon":[{"status":"0","msg":"Koneksi ke database server bermasalah"}]}');
				die(file_put_contents($filelog, $time.$ip." Connection site failed: " .mysqli_connect_error()."\n",FILE_APPEND | LOCK_EX));
				//printf("Connect failed: %s\n", $conn->connect_error());
				//exit();
			}
		}

		$sqltgl="select DATE_ADD(tanggal, INTERVAL 1 DAY) tanggal from m_setup_site where siteid='".$siteid."' limit 1";
		$result=$conn->query($sqltgl);
		if($result === false) {
			print ('{"respon":[{"status":"0","msg":"Query error"}]}');
			die(file_put_contents($filelog, $time.$ip. " Table setup site masih kosong : Wrong SQL: " . $sqltgl . ' Error: ' . $conn->error."\n",FILE_APPEND | LOCK_EX));
		}
		$resultfetch = $result->fetch_assoc();
		
		$resultsetup=$resultfetch["tanggal"];

		$sql = "select * from t_sales_rrk where siteid='".$siteid."' and salesmanid='".$salesmanid."' and periode='".$resultsetup."'";

		$result = $conn->query($sql);

			$array = array();

			while($row=$result->fetch_assoc()){ $array[] = $row; }

			$respon = array(
							"t_sales_rrk" => $array
							);

			echo json_encode($respon);
			file_put_contents($filelog, $time.$ip." Synchronize data RRK Next days success \n", FILE_APPEND | LOCK_EX);
		}else{

			$respon = array("respon" => array(
											array(
											"status" => "2",
											"msg" => "Request failed"
											)
										)
							);

			echo json_encode($respon);
			
			file_put_contents($filelog, $time.$ip." Synchronize data RRK Next days Error \n".json_encode($respon)."\n", FILE_APPEND | LOCK_EX);
		}

		
?>
