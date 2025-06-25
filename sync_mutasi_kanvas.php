<?php 

include "conf_conn.php";

$today = getDatetimeNow('Y-m-d');

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
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
	
	file_put_contents($filelog, $time.$ip." Hit Json mutasi kanvas : ".json_encode($obj)."\n", FILE_APPEND | LOCK_EX);

	//if (!empty($obj->tanggal))
	//{
		@$siteid = strtoupper(@$obj->siteid);
		@$salesmanid = @$obj->salesmanid;
		$tglsynch=getDatetimeNow("Y-m-d H:i:s");

		$connhost = @mysqli_connect($host, $uname, $pwd, $db);
		if (!$connhost) {
			print ('{"response_code":"2","response_message":"Koneksi ke database server local bermasalah","data":[]}');
			die(file_put_contents($filelog, $time.$ip." Connection host failed: " . mysqli_connect_error()."\n",FILE_APPEND | LOCK_EX));
		}else {
			$sqlconn="select * from m_setupsite_db where siteid='".$siteid."' limit 1;";
			$stringconn = mysqli_query($connhost, $sqlconn);
			$row=mysqli_fetch_array($stringconn,MYSQLI_ASSOC);
			mysqli_close($connhost);

			$conn = @mysqli_connect($row["hostname"], $row["user"], $row["pwd"], $row["dbname"]);
			if (!$conn){
				print ('{"response_code":"2","response_message":"Koneksi ke database server bermasalah","data":[]}');
				die(file_put_contents($filelog, $time.$ip." Connection site failed: " .mysqli_connect_error()."\n",FILE_APPEND | LOCK_EX));
			}
		}

		$sqltgl="select tanggal from m_setup_site where siteid='".$siteid."' limit 1";
		$result=$conn->query($sqltgl);
		if($result == false) {
			print ('{"response_code":"2","response_message":"Query error "'.$sqltgl.',"data":[]}');
			die(file_put_contents($filelog, $time.$ip. " Table setup site masih kosong : Wrong SQL: " . $sqltgl . ' Error: ' . $conn->error."\n",FILE_APPEND | LOCK_EX));
		}
		
		$resultfetch = $result->fetch_assoc();
		$resultsetup=$resultfetch["tanggal"];
		
		$sql = "select * from t_mutasi_kanvas where siteid='".$siteid."' and salesmanid='".$salesmanid."' ";
		
		$result = $conn->query($sql);
		$array = array();
			
			$rowsmutasi = array();
			while($row=$result->fetch_assoc()){
					
				$rowsmutasi["siteid"] = htmlentities(trim($row["siteid"]));
				$rowsmutasi["salesmanid"] = htmlentities(trim($row["salesmanid"]));
				$rowsmutasi["nama_salesman"] = htmlentities(trim($row["nama_salesman"]));
				$rowsmutasi["productid"] = htmlentities(trim($row["productid"]));
				$rowsmutasi["product_desc"] = htmlentities(trim($row["product_desc"]));
				$rowsmutasi["terima"] = htmlentities(trim($row["terima"]));
				$rowsmutasi["keluar"] = htmlentities(trim($row["keluar"]));
				$rowsmutasi["jual"] = htmlentities(trim($row["jual"]));
				$rowsmutasi["order_sales"] = htmlentities(trim($row["order_sales"]));
				$rowsmutasi["akhir"] = htmlentities(trim($row["akhir"]));
				$array[] = $rowsmutasi; 
			
			}

			//while($row=$result->fetch_assoc()){ $array[] = $row; }
			$respon = array("response_code" => "200",
							"response_message" => "success",
							"data"=>@$array
							);
							
			echo json_encode($respon);
			file_put_contents($filelog, $time.$ip." Synchronize data mutasi kanvas success \n".json_encode($respon), FILE_APPEND | LOCK_EX);
		/*}else{

			$respon = array("response_code" => "2",
							"response_message" => "Request failed",
							"data"=>array()
							);

			echo json_encode($respon);
			
			file_put_contents($filelog, $time.$ip." Synchronize data mutasi kanvas Error \n".json_encode($respon)."\n", FILE_APPEND | LOCK_EX);
		}*/
		
?>
