<?php 
/********** Version 2.0.2 rev 4 build 11.45 ***********/

/*
    protected static $_messages = array(
        JSON_ERROR_NONE => 'No error has occurred',
        JSON_ERROR_DEPTH => 'The maximum stack depth has been exceeded',
        JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON',
        JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
        JSON_ERROR_SYNTAX => 'Syntax error',
        JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded'
    );

    public static function encode($value, $options = 0) {
        $result = json_encode($value, $options);

        if($result)  {
            return $result;
        }

        throw new RuntimeException(static::$_messages[json_last_error()]);
    }

    public static function decode($json, $assoc = false) {
        $result = json_decode($json, $assoc);

        if($result) {
            return $result;
        }

        throw new RuntimeException(static::$_messages[json_last_error()]);
    }

*/
include "conf_conn.php";


$today = getDatetimeNow('Y-m-d');
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
//header('Content-Type: application/json; charset=utf-8', true,200);
//header("Content-Type: application/json; charset=UTF-8");
//$jsonpar=$_POST['jsonpar'];
$json = file_get_contents('php://input');
$obj = json_decode($json,true);
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

    $path = 'log_trans/'.getDatetimeNow("Ym");

    if(!is_dir($path)) //create the folder if it's not already exists
    {
      mkdir($path,0755,TRUE);
    } 

	$filelog = $path.'/log_api_'.getDatetimeNow('d.m.Y').'.txt';
	
 	$time = getDatetimeNow('[d/M/Y H:i:s]');
	file_put_contents($filelog, $time.$ip." Hit Json : ".json_encode($obj)."\n", FILE_APPEND | LOCK_EX);

$setupSiteCount = count($obj['m_setup_site']);
$custCount = count($obj['m_customer']); // customerid is null and customerid_m is not null
$salesMasterCount = count($obj['t_sales_master']); // status_send = 0
$salesDetailCount = count($obj['t_sales_detail']); // mengacu ke sales master status_send=0 
$arInkDetail = count($obj['t_ar_ink_detail']); // status_send = 0
$salesRrkTrans = count($obj['t_sales_rrk_trans']); //status_send = 0
$salesCrcTrans = count($obj['t_sales_crc']); //status_send = 0

$actsos = count($obj['t_activity_sos']); //status_send = 0
$actcompetitor = count($obj['t_activity_competitor']); //status_send = 0
$actnpd = count($obj['t_activity_npd_competitor']); //status_send = 0
$actpromogsk = count($obj['t_activity_promo_gsk']); //status_send = 0

//echo $json;
    //do something with $json. It's ready to use
		//Save
	if ($setupSiteCount>0)
	{
		$obj['m_setup_site'][0]['tanggal']=(empty($obj['m_setup_site'][0]['tanggal'])?"":$obj['m_setup_site'][0]['tanggal']);
		$siteid = strtoupper($obj["m_setup_site"][0]['siteid']);
		$salesmanid = $obj["m_setup_site"][0]['salesmanid'];
		$tglsynch=getDatetimeNow("Y-m-d H:i:s");
		// trim if has length >10
		$obj['m_setup_site'][0]['tanggal']=(empty($obj['m_setup_site'][0]['tanggal'])?'':$obj['m_setup_site'][0]['tanggal']);
		if( strlen($obj['m_setup_site'][0]['tanggal']) > 10) {
			$obj['m_setup_site'][0]['tanggal']=substr($obj['m_setup_site'][0]['tanggal'], 0, -9);
		}
		$tglsetup=$obj['m_setup_site'][0]['tanggal'];
	
		$connhost = @mysqli_connect($host, $uname, $pwd, $db);
		if (!$connhost) {
			print ('{"respon":[{"status":"0","msg":"Koneksi ke database server local bermasalah"}]}');
			die(file_put_contents($filelog, $time.$ip." Connection host failed: " . mysqli_connect_error()."\n",FILE_APPEND | LOCK_EX));
		}else {
			$sqlconn="select * from m_setupsite_db where siteid='".$siteid."'";
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
		  /* grab the posts from the db */
		if ($custCount>0) 
		{

			for($i=0; $i<$custCount; $i++){
			/*$sqlcustdel = "delete from m_customer where siteid='".$siteid."' and salesmanid='".$salesmanid."'
						and customerid='".$obj['m_customer'][$i]['customerid']."' and customerid_m='".$obj['m_customer'][$i]['customerid_m']."';";
						
			if($conn->query($sqlcustdel) === false) {
			  trigger_error(file_put_contents($filelog, $time.$ip." Delete m_customer gagal "."Wrong SQL: " . $sqlcustdel . ' Error: ' . $conn->error."\n", FILE_APPEND | LOCK_EX), E_USER_ERROR);
			}*/
			
			$obj['m_customer'][$i]['customerid']=(empty($obj['m_customer'][$i]['customerid'])?"":$obj['m_customer'][$i]['customerid']);
			$obj['m_customer'][$i]['customerid_m']=(empty($obj['m_customer'][$i]['customerid_m'])?"":$obj['m_customer'][$i]['customerid_m']);
			$obj['m_customer'][$i]['mcc']=(empty($obj['m_customer'][$i]['mcc'])?"":$obj['m_customer'][$i]['mcc']);
			                        
						
			$sqlcustins = 'REPLACE INTO m_customer (siteid,customerid_m,customerid,kode_outlet,nama_customer,alamat,kelurahanid,kecamatanid,
					kotaid,propinsiid,kodepos,telp,email,segmentid,typeid,classid,regionalid,areaid,subareaid,createdate,latitude,
					longitude,nilai_sales,salesmanid,mcc) 
					VALUES ("'.$obj['m_customer'][$i]['siteid'].'", 
							"'.$obj['m_customer'][$i]['customerid_m'].'", 
							"'.$obj['m_customer'][$i]['customerid'].'", 
							"'.@$obj['m_customer'][$i]['kode_outlet'].'", 
							"'.mysql_real_escape_string($obj['m_customer'][$i]['nama_customer']).'", 
							"'.mysql_real_escape_string($obj['m_customer'][$i]['alamat']).'", 
							"'.$obj['m_customer'][$i]['kelurahanid'].'", 
							"'.$obj['m_customer'][$i]['kecamatanid'].'", 
							"'.$obj['m_customer'][$i]['kotaid'].'", 
							"'.$obj['m_customer'][$i]['propinsiid'].'", 
							"'.$obj['m_customer'][$i]['kodepos'].'", 
							"'.mysql_real_escape_string($obj['m_customer'][$i]['telp']).'", 
							"'.mysql_real_escape_string($obj['m_customer'][$i]['email']).'", 
							"'.@$obj['m_customer'][$i]['segmentid'].'",
							"'.@$obj['m_customer'][$i]['typeid'].'", 
							"'.@$obj['m_customer'][$i]['classid'].'", 
							"'.@$obj['m_customer'][$i]['regionalid'].'", 
							"'.@$obj['m_customer'][$i]['areaid'].'", 
							"'.@$obj['m_customer'][$i]['subareaid'].'", 
							"'.$tglsynch.'",
							"'.$obj['m_customer'][$i]['latitude'].'", 
							"'.$obj['m_customer'][$i]['longitude'].'", 
							"'.$obj['m_customer'][$i]['nilai_sales'].'", 
							"'.$obj['m_customer'][$i]['salesmanid'].'", 
							"'.$obj['m_customer'][$i]['mcc'].'" 
							);';
 
					if($conn->query($sqlcustins) === false) {
					  trigger_error(file_put_contents($filelog, $time.$ip." Insert m_customer gagal "."Wrong SQL: " . $sqlcustins . ' Error: ' . $conn->error."\n", FILE_APPEND | LOCK_EX), E_USER_ERROR);
					}
			}
		}
		
		if ($salesMasterCount>0)
		{

			for($i=0; $i<$salesMasterCount; $i++){
				$nosales = $obj['t_sales_master'][$i]['no_sales'];
			if ($nosales == '') {
				$tglcreated = $obj['t_sales_master'][$i]['tgl_created'];
				$tglcreated = str_replace("-","",$tglcreated);
				$tglcreated = str_replace(" ","",$tglcreated);
				$tglcreated = str_replace(":","",$tglcreated);
				$nosales=$obj['t_sales_master'][$i]['salesmanid'].$obj['t_sales_master'][$i]['customerid'].$tglcreated;
			}

			//$obj['t_sales_master'][$i]['status_send']=(empty($obj['t_sales_master'][$i]['status_send'])?"":$obj['t_sales_master'][$i]['status_send']);
			$obj['t_sales_master'][$i]['remark']=(empty($obj['t_sales_master'][$i]['remark'])?"":$obj['t_sales_master'][$i]['remark']);
			$obj['t_sales_master'][$i]['new_cust']=(empty($obj['t_sales_master'][$i]['new_cust'])?'':$obj['t_sales_master'][$i]['new_cust']);
			$obj['t_sales_master'][$i]['netto']=(empty($obj['t_sales_master'][$i]['netto'])?'':$obj['t_sales_master'][$i]['netto']);
			$obj['t_sales_master'][$i]['latitude']=(empty($obj['t_sales_master'][$i]['latitude'])?'':$obj['t_sales_master'][$i]['latitude']);
			$obj['t_sales_master'][$i]['longitude']=(empty($obj['t_sales_master'][$i]['longitude'])?'':$obj['t_sales_master'][$i]['longitude']);
			$obj['t_sales_master'][$i]['bruto']=(empty($obj['t_sales_master'][$i]['bruto'])?'':$obj['t_sales_master'][$i]['bruto']);
			
			/*$sqlgetsetupsite="Select case when tanggal < '".$obj['t_sales_master'][$i]['tanggal']."' 
										then tanggal else '".$obj['t_sales_master'][$i]['tanggal']."' end tanggal 
							 From m_setup_site";
							 			
			$resulttanggal=$conn->query($sqlgetsetupsite);
			if($resulttanggal === false) {
			  trigger_error(file_put_contents($filelog, $time.$ip." get tanggal setup site masih kosong : Wrong SQL: " . $sqltgl . ' Error: ' . $conn->error. "\n", FILE_APPEND | LOCK_EX), E_USER_ERROR);
			}
			$resultfetchtanggal = $resulttanggal->fetch_assoc();
			$tanggalsetupsite=$resultfetchtanggal["tanggal"];
			*/
			$sqlmasterins = 'REPLACE INTO t_sales_master
					(siteid,no_sales,tanggal,salesmanid,customerid,
					bruto,netto,categoryid,
					tanggalsave,remark,latitude,longitude,new_cust,status_send) 
					VALUES ("'.$obj['t_sales_master'][$i]['siteid'].'","'.$nosales.'","'.$obj['t_sales_master'][$i]['tanggal'].'",
							"'.$obj['t_sales_master'][$i]['salesmanid'].'","'.$obj['t_sales_master'][$i]['customerid'].'",
							"'.$obj['t_sales_master'][$i]['bruto'].'",
							"'.$obj['t_sales_master'][$i]['netto'].'",
							"'.$obj['t_sales_master'][$i]['categoryid'].'",
							"'.$obj['t_sales_master'][$i]['tanggalsave'].'","'.$obj['t_sales_master'][$i]['remark'].'",
							"'.$obj['t_sales_master'][$i]['latitude'].'","'.$obj['t_sales_master'][$i]['longitude'].'",
							"'.$obj['t_sales_master'][$i]['new_cust'].'","0");';

				if($conn->query($sqlmasterins) === false) {
				  trigger_error(file_put_contents($filelog, $time.$ip." Insert t_sales_master gagal "."Wrong SQL: " . $sqlmasterins . ' Error: ' . $conn->error."\n", FILE_APPEND | LOCK_EX), E_USER_ERROR);
				}
			}
		}
		
		if ($salesDetailCount>0)
		{

			for($i=0; $i<$salesDetailCount; $i++){
			
			$obj['t_sales_detail'][$i]['sat_bonus']=(empty($obj['t_sales_detail'][$i]['sat_bonus'])?'':$obj['t_sales_detail'][$i]['sat_bonus']);
			$obj['t_sales_detail'][$i]['bonus']=(empty($obj['t_sales_detail'][$i]['bonus'])?'':$obj['t_sales_detail'][$i]['bonus']);
			
			$nourutprod=$i+1;
			$sqlslsdetins = 'REPLACE INTO t_sales_detail
					(siteid,no_sales,productid,nourut,type_gudang,sat_besar,sat_sedang,sat_kecil,isi_besar,isi_sedang,isi_kecil,
					qty1,qty2,qty3,qty_kecil,sat_bonus,qty_bonus,flag_bonus,h_jual,bruto,netto,disc_cabang,disc_prinsipal,disc_xtra,
					disc_cod,rp_cabang,rp_prinsipal,rp_xtra,rp_cod,flag_xtra,flag_cod,bonus,net_prinsipal,net_xtra,net_cod,status_send) 
					VALUES ("'.$obj['t_sales_detail'][$i]['siteid'].'","'.$obj['t_sales_detail'][$i]['no_sales'].'","'.$obj['t_sales_detail'][$i]['productid'].'",
					"'.$nourutprod.'","'.$obj['t_sales_detail'][$i]['type_gudang'].'","'.$obj['t_sales_detail'][$i]['sat_besar'].'",
					"'.$obj['t_sales_detail'][$i]['sat_sedang'].'","'.$obj['t_sales_detail'][$i]['sat_kecil'].'","'.$obj['t_sales_detail'][$i]['isi_besar'].'",
					"'.$obj['t_sales_detail'][$i]['isi_sedang'].'","'.$obj['t_sales_detail'][$i]['isi_kecil'].'","'.$obj['t_sales_detail'][$i]['qty1'].'",
					"'.$obj['t_sales_detail'][$i]['qty2'].'","'.$obj['t_sales_detail'][$i]['qty3'].'","'.$obj['t_sales_detail'][$i]['qty_kecil'].'",
					"'.$obj['t_sales_detail'][$i]['sat_bonus'].'","'.$obj['t_sales_detail'][$i]['qty_bonus'].'","'.$obj['t_sales_detail'][$i]['flag_bonus'].'",
					"'.$obj['t_sales_detail'][$i]['h_jual'].'","'.$obj['t_sales_detail'][$i]['bruto'].'","'.$obj['t_sales_detail'][$i]['netto'].'",
					"'.$obj['t_sales_detail'][$i]['disc_cabang'].'","'.$obj['t_sales_detail'][$i]['disc_prinsipal'].'","'.$obj['t_sales_detail'][$i]['disc_xtra'].'",
					"'.$obj['t_sales_detail'][$i]['disc_cod'].'","'.$obj['t_sales_detail'][$i]['rp_cabang'].'","'.$obj['t_sales_detail'][$i]['rp_prinsipal'].'",
					"'.$obj['t_sales_detail'][$i]['rp_xtra'].'","'.$obj['t_sales_detail'][$i]['rp_cod'].'","'.$obj['t_sales_detail'][$i]['flag_xtra'].'",
					"'.$obj['t_sales_detail'][$i]['flag_cod'].'","'.$obj['t_sales_detail'][$i]['bonus'].'","'.$obj['t_sales_detail'][$i]['net_prinsipal'].'",
					"'.$obj['t_sales_detail'][$i]['net_xtra'].'","'.$obj['t_sales_detail'][$i]['net_cod'].'","'.$obj['t_sales_detail'][$i]['status_send'].'");';

				if($conn->query($sqlslsdetins) === false) {
				  trigger_error(file_put_contents($filelog, $time.$ip." Insert t_sales_detail gagal "."Wrong SQL: " . $sqlslsdetins . ' Error: ' . $conn->error."\n", FILE_APPEND | LOCK_EX), E_USER_ERROR);
				}
			}
		}

		if ($arInkDetail>0)
		{
			for($i=0; $i<$arInkDetail; $i++){
			$sql = "UPDATE t_ar_ink_detail SET
					retur='".$obj['t_ar_ink_detail'][$i]['retur']."',
					bayar='".$obj['t_ar_ink_detail'][$i]['bayar']."',
					no_transfer='".$obj['t_ar_ink_detail'][$i]['no_transfer']."',
					tgl_transfer='".$obj['t_ar_ink_detail'][$i]['tgl_transfer']."',
					no_giro='".$obj['t_ar_ink_detail'][$i]['no_giro']."',
					tgl_jt_giro='".$obj['t_ar_ink_detail'][$i]['tgl_jt_giro']."',
					tgl_terima_giro='".$obj['t_ar_ink_detail'][$i]['tgl_terima_giro']."',
					AC_id_giro='".$obj['t_ar_ink_detail'][$i]['AC_id_giro']."',
					status_giro='".$obj['t_ar_ink_detail'][$i]['status_giro']."',
					bank_id='".$obj['t_ar_ink_detail'][$i]['bank_id']."',
					bank_name='".$obj['t_ar_ink_detail'][$i]['bank_name']."',
					bayar_tunai='".$obj['t_ar_ink_detail'][$i]['bayar_tunai']."',
					bayar_transfer='".$obj['t_ar_ink_detail'][$i]['bayar_transfer']."',
					bayar_giro='".$obj['t_ar_ink_detail'][$i]['bayar_giro']."',
					status_send='1'
					WHERE no_sales = '".$obj['t_ar_ink_detail'][$i]['no_sales']."' ";
			if($conn->query($sql) === false) {
			  trigger_error(file_put_contents($filelog, $time.$ip." Update t_ar_ink_detail gagal "."Wrong SQL: " . $sql . ' Error: ' . $conn->error."\n", FILE_APPEND | LOCK_EX), E_USER_ERROR);
			}
			}
		}
		
		if ($salesRrkTrans>0)
		{
			for($i=0; $i<$salesRrkTrans; $i++){
			$obj['t_sales_rrk_trans'][$i]['crc_time']=(empty($obj['t_sales_rrk_trans'][$i]['crc_time'])?'':$obj['t_sales_rrk_trans'][$i]['crc_time']);
			$obj['t_sales_rrk_trans'][$i]['ink_time']=(empty($obj['t_sales_rrk_trans'][$i]['ink_time'])?'':$obj['t_sales_rrk_trans'][$i]['ink_time']);
			$obj['t_sales_rrk_trans'][$i]['order_time']=(empty($obj['t_sales_rrk_trans'][$i]['order_time'])?'':$obj['t_sales_rrk_trans'][$i]['order_time']);
			$obj['t_sales_rrk_trans'][$i]['call_reasonid']=(empty($obj['t_sales_rrk_trans'][$i]['call_reasonid'])?'':$obj['t_sales_rrk_trans'][$i]['call_reasonid']);
			$obj['t_sales_rrk_trans'][$i]['reason']=(empty($obj['t_sales_rrk_trans'][$i]['reason'])?'':$obj['t_sales_rrk_trans'][$i]['reason']);
			$obj['t_sales_rrk_trans'][$i]['status_send']=(empty($obj['t_sales_rrk_trans'][$i]['status_send'])?'':$obj['t_sales_rrk_trans'][$i]['status_send']);
			$obj['t_sales_rrk_trans'][$i]['mnc']=(empty($obj['t_sales_rrk_trans'][$i]['mnc'])?'':$obj['t_sales_rrk_trans'][$i]['mnc']);
			$obj['t_sales_rrk_trans'][$i]['lac']=(empty($obj['t_sales_rrk_trans'][$i]['lac'])?'':$obj['t_sales_rrk_trans'][$i]['lac']);
			$obj['t_sales_rrk_trans'][$i]['cid']=(empty($obj['t_sales_rrk_trans'][$i]['cid'])?'':$obj['t_sales_rrk_trans'][$i]['cid']);
			$obj['t_sales_rrk_trans'][$i]['jenis']=(empty($obj['t_sales_rrk_trans'][$i]['jenis'])?'':$obj['t_sales_rrk_trans'][$i]['jenis']);
			$obj['t_sales_rrk_trans'][$i]['deskripsi']=(empty($obj['t_sales_rrk_trans'][$i]['deskripsi'])?'':$obj['t_sales_rrk_trans'][$i]['deskripsi']);
			$obj['t_sales_rrk_trans'][$i]['alasan']=(empty($obj['t_sales_rrk_trans'][$i]['alasan'])?'':$obj['t_sales_rrk_trans'][$i]['alasan']);
			$obj['t_sales_rrk_trans'][$i]['check_out']=(empty($obj['t_sales_rrk_trans'][$i]['check_out'])?'':$obj['t_sales_rrk_trans'][$i]['check_out']);
			$obj['t_sales_rrk_trans'][$i]['date_update']=(empty($obj['t_sales_rrk_trans'][$i]['date_update'])?'':$obj['t_sales_rrk_trans'][$i]['date_update']);			
			
			$check_in=$obj['t_sales_rrk_trans'][$i]['check_in'];
			if ($check_in=='') {$check_in='NULL';}else{$check_in='\''.$check_in.'\'';}
			$crc_time=$obj['t_sales_rrk_trans'][$i]['crc_time'];
			if ($crc_time=='') {$crc_time='NULL';}else{$crc_time='\''.$crc_time.'\'';}
			$ink_time=$obj['t_sales_rrk_trans'][$i]['ink_time'];
			if ($ink_time=='') {$ink_time='NULL';}else{$ink_time='\''.$ink_time.'\'';}
			$order_time=$obj['t_sales_rrk_trans'][$i]['order_time'];
			if ($order_time=='') {$order_time='NULL';}else{$order_time='\''.$order_time.'\'';}
			$check_out=$obj['t_sales_rrk_trans'][$i]['check_out'];
			if ($check_out=='') {$check_out='NULL';}else{$check_out='\''.$check_out.'\'';}
			$date_update=$obj['t_sales_rrk_trans'][$i]['date_update'];
			if ($date_update=='') {$date_update='NULL';}else{$date_update='\''.$date_update.'\'';}
			
			$obj['t_sales_rrk_trans'][$i]['crc_time']=(empty($obj['t_sales_rrk_trans'][$i]['crc_time'])?'':$obj['t_sales_rrk_trans'][$i]['crc_time']);
			$obj['t_sales_rrk_trans'][$i]['ink_time']=(empty($obj['t_sales_rrk_trans'][$i]['ink_time'])?'':$obj['t_sales_rrk_trans'][$i]['ink_time']);
			$obj['t_sales_rrk_trans'][$i]['order_time']=(empty($obj['t_sales_rrk_trans'][$i]['order_time'])?'':$obj['t_sales_rrk_trans'][$i]['order_time']);
			$obj['t_sales_rrk_trans'][$i]['call_reasonid']=(empty($obj['t_sales_rrk_trans'][$i]['call_reasonid'])?'':$obj['t_sales_rrk_trans'][$i]['call_reasonid']);
			$obj['t_sales_rrk_trans'][$i]['reason']=(empty($obj['t_sales_rrk_trans'][$i]['reason'])?'':$obj['t_sales_rrk_trans'][$i]['reason']);
			$obj['t_sales_rrk_trans'][$i]['status_send']=(empty($obj['t_sales_rrk_trans'][$i]['status_send'])?'':$obj['t_sales_rrk_trans'][$i]['status_send']);
			$obj['t_sales_rrk_trans'][$i]['mnc']=(empty($obj['t_sales_rrk_trans'][$i]['mnc'])?'':$obj['t_sales_rrk_trans'][$i]['mnc']);
			$obj['t_sales_rrk_trans'][$i]['lac']=(empty($obj['t_sales_rrk_trans'][$i]['lac'])?'':$obj['t_sales_rrk_trans'][$i]['lac']);
			$obj['t_sales_rrk_trans'][$i]['cid']=(empty($obj['t_sales_rrk_trans'][$i]['cid'])?'':$obj['t_sales_rrk_trans'][$i]['cid']);
			$obj['t_sales_rrk_trans'][$i]['latitude_cell']=(empty($obj['t_sales_rrk_trans'][$i]['latitude_cell'])?'0.0':$obj['t_sales_rrk_trans'][$i]['latitude_cell']);
			$obj['t_sales_rrk_trans'][$i]['longitude_cell']=(empty($obj['t_sales_rrk_trans'][$i]['longitude_cell'])?'0.0':$obj['t_sales_rrk_trans'][$i]['longitude_cell']);
			$obj['t_sales_rrk_trans'][$i]['jenis']=(empty($obj['t_sales_rrk_trans'][$i]['jenis'])?'':$obj['t_sales_rrk_trans'][$i]['jenis']);
			$obj['t_sales_rrk_trans'][$i]['deskripsi']=(empty($obj['t_sales_rrk_trans'][$i]['deskripsi'])?'':$obj['t_sales_rrk_trans'][$i]['deskripsi']);
			$obj['t_sales_rrk_trans'][$i]['alasan']=(empty($obj['t_sales_rrk_trans'][$i]['alasan'])?'':$obj['t_sales_rrk_trans'][$i]['alasan']);
			$obj['t_sales_rrk_trans'][$i]['check_out']=(empty($obj['t_sales_rrk_trans'][$i]['check_out'])?'':$obj['t_sales_rrk_trans'][$i]['check_out']);
			$obj['t_sales_rrk_trans'][$i]['user_update']=(empty($obj['t_sales_rrk_trans'][$i]['user_update'])?'':$obj['t_sales_rrk_trans'][$i]['user_update']);
			
			
			$sqlrrktransins = 'REPLACE INTO t_sales_rrk_trans
					(periode,siteid,salesmanid,nama_salesman,customerid,flag_proses,tgl_proses,user_create,date_create,user_update,date_update,minggu,
					call_reasonid,reason,status_send,check_in,crc_time,ink_time,tipe,mnc,order_time,lac,cid,latitude_cell,longitude_cell,jenis,deskripsi,alasan,check_out) 
					VALUES ("'.$obj['t_sales_rrk_trans'][$i]['periode'].'","'.$obj['t_sales_rrk_trans'][$i]['siteid'].'","'.$obj['t_sales_rrk_trans'][$i]['salesmanid'].'"
					,"'.$obj['t_sales_rrk_trans'][$i]['nama_salesman'].'","'.$obj['t_sales_rrk_trans'][$i]['customerid'].'","'.$obj['t_sales_rrk_trans'][$i]['flag_proses'].'"
					,"'.$obj['t_sales_rrk_trans'][$i]['tgl_proses'].'","'.$obj['t_sales_rrk_trans'][$i]['user_create'].'"
					,"'.$obj['t_sales_rrk_trans'][$i]['date_create'].'","'.$obj['t_sales_rrk_trans'][$i]['user_update'].'",'.$date_update.'
					,"'.$obj['t_sales_rrk_trans'][$i]['minggu'].'","'.$obj['t_sales_rrk_trans'][$i]['call_reasonid'].'","'.$obj['t_sales_rrk_trans'][$i]['reason'].'"
					,"'.$obj['t_sales_rrk_trans'][$i]['status_send'].'",'.$check_in.','.$crc_time.','.$ink_time.'
					,"'.$obj['t_sales_rrk_trans'][$i]['tipe'].'","'.$obj['t_sales_rrk_trans'][$i]['mnc'].'",'.$order_time.'
					,"'.$obj['t_sales_rrk_trans'][$i]['lac'].'","'.$obj['t_sales_rrk_trans'][$i]['cid'].'","'.$obj['t_sales_rrk_trans'][$i]['latitude_cell'].'"
					,"'.$obj['t_sales_rrk_trans'][$i]['longitude_cell'].'","'.$obj['t_sales_rrk_trans'][$i]['jenis'].'","'.$obj['t_sales_rrk_trans'][$i]['deskripsi'].'"
					,"'.$obj['t_sales_rrk_trans'][$i]['alasan'].'",'.$check_out.');';

				if($conn->query($sqlrrktransins) === false) {
				  trigger_error(file_put_contents($filelog, $time.$ip." Insert t_sales_rrk_trans gagal "."Wrong SQL: " . $sqlrrktransins . ' Error: ' . $conn->error."\n", FILE_APPEND | LOCK_EX), E_USER_ERROR);
				}
			}
		}
		
		if ($salesCrcTrans>0)
		{
			for($i=0; $i<$salesCrcTrans; $i++){
				/*$sql = "REPLACE INTO t_sales_crc 
						(periode,siteid,salesmanid,customerid,productid,
						qty_akhir,qty_saran_order,qty_fix_order,date_update,target_value,target_qty,
						total_target_value,total_sales_qty,target_vs_sales,status_send
						)
						VALUES (
						STR_TO_DATE('".$obj['t_sales_crc'][$i]['periode']."', '%Y-%m-%d'),'".$obj['t_sales_crc'][$i]['siteid']."',
						'".$obj['t_sales_crc'][$i]['salesmanid']."','".$obj['t_sales_crc'][$i]['customerid']."','".$obj['t_sales_crc'][$i]['productid']."',
						'".$obj['t_sales_crc'][$i]['qty_akhir']."','".$obj['t_sales_crc'][$i]['qty_saran_order']."','".$obj['t_sales_crc'][$i]['date_update']."',
						'".$obj['t_sales_crc'][$i]['target_value']."','".$obj['t_sales_crc'][$i]['target_qty']."',
						'".$obj['t_sales_crc'][$i]['total_target_value']."','".$obj['t_sales_crc'][$i]['total_target_qty']."',
						'".$obj['t_sales_crc'][$i]['target_vs_sales']."','1'
						)";*/
			$sql = "UPDATE t_sales_crc SET
					qty_akhir='".$obj['t_sales_crc'][$i]['qty_akhir']."',
					qty_saran_order='".$obj['t_sales_crc'][$i]['qty_saran_order']."',
					qty_fix_order='".$obj['t_sales_crc'][$i]['qty_fix_order']."',
					date_update='".$obj['t_sales_crc'][$i]['date_update']."',
					status_send='1',
					target_value='".$obj['t_sales_crc'][$i]['target_value']."',
					target_qty='".$obj['t_sales_crc'][$i]['target_qty']."',
					total_target_value='".$obj['t_sales_crc'][$i]['total_target_value']."',
					total_target_qty='".$obj['t_sales_crc'][$i]['total_target_qty']."',
					total_sales_value='".$obj['t_sales_crc'][$i]['total_sales_value']."',
					total_sales_qty='".$obj['t_sales_crc'][$i]['total_sales_qty']."',
					target_vs_sales='".$obj['t_sales_crc'][$i]['target_vs_sales']."',
					exp_date = '".@$obj['t_sales_crc'][$i]['exp_date']."',
					price = '".@$obj['t_sales_crc'][$i]['price']."'
					WHERE periode = STR_TO_DATE('".$obj['t_sales_crc'][$i]['periode']."', '%Y-%m-%d')
					and siteid = '".$obj['t_sales_crc'][$i]['siteid']."'
					and salesmanid = '".$obj['t_sales_crc'][$i]['salesmanid']."'
					and customerid = '".$obj['t_sales_crc'][$i]['customerid']."'
					and productid = '".$obj['t_sales_crc'][$i]['productid']."' ";
			
				if($conn->query($sql) === false) {
				  trigger_error(file_put_contents($filelog, $time.$ip." Update t_sales_crc gagal "."Wrong SQL: " . $sql . ' Error: ' . $conn->error."\n", FILE_APPEND | LOCK_EX), E_USER_ERROR);
				}
			}
		}
		
		if ($actsos>0)
		{

			$obj['t_activity_sos'][$i]['qty_sos_gsk']=(empty($obj['t_activity_sos'][$i]['qty_sos_gsk'])?'0':$obj['t_activity_sos'][$i]['qty_sos_gsk']);
			$obj['t_activity_sos'][$i]['qty_sos_competitor']=(empty($obj['t_activity_sos'][$i]['qty_sos_competitor'])?'0':$obj['t_activity_sos'][$i]['qty_sos_competitor']);
			$obj['t_activity_sos'][$i]['sos']=(empty($obj['t_activity_sos'][$i]['sos'])?'0':$obj['t_activity_sos'][$i]['sos']);
			$obj['t_activity_sos'][$i]['created_by']=(empty($obj['t_activity_sos'][$i]['created_by'])?'':$obj['t_activity_sos'][$i]['created_by']);
			$obj['t_activity_sos'][$i]['modified_by']=(empty($obj['t_activity_sos'][$i]['modified_by'])?'':$obj['t_activity_sos'][$i]['modified_by']);
			
			for($i=0; $i<$actsos; $i++){
			$sqlsos = 'REPLACE INTO t_activity_sos
					(siteid, periode, salesmanid, customerid, qty_sos_gsk, qty_sos_competitor, sos, datecreate, created_by, created_date, modified_by, modified_date, transaction_id) 
					VALUES ("'.@$obj['t_activity_sos'][$i]['siteid'].'", 
							STR_TO_DATE("'.@$obj['t_activity_sos'][$i]['periode'].'", "%Y-%m-%d"),
							"'.@$obj['t_activity_sos'][$i]['salesmanid'].'",
							"'.@$obj['t_activity_sos'][$i]['customerid'].'",
							"'.@$obj['t_activity_sos'][$i]['qty_sos_gsk'].'",
							"'.@$obj['t_activity_sos'][$i]['qty_sos_competitor'].'",
							"'.@$obj['t_activity_sos'][$i]['sos'].'",
							"'.@$obj['t_activity_sos'][$i]['datecreate'].'",
							"'.@$obj['t_activity_sos'][$i]['created_by'].'",
							"'.@$obj['t_activity_sos'][$i]['created_date'].'",
							"'.@$obj['t_activity_sos'][$i]['modified_by'].'",
							"'.@$obj['t_activity_sos'][$i]['modified_date'].'",
							"'.@$obj['t_activity_sos'][$i]['transaction_id'].'");
							';

				if($conn->query($sqlsos) === false) {
				  trigger_error(file_put_contents($filelog, $time.$ip." Insert t_activity_sos gagal "."Wrong SQL: " . $sqlsos . ' Error: ' . $conn->error."\n", FILE_APPEND | LOCK_EX), E_USER_ERROR);
				}
			}		
		}

 		if ($actcompetitor>0)
		{

			$obj['t_activity_competitor'][$i]['harga_normal']=(empty($obj['t_activity_competitor'][$i]['harga_normal'])?"0":$obj['t_activity_competitor'][$i]['harga_normal']);
			$obj['t_activity_competitor'][$i]['harga_promo']=(empty($obj['t_activity_competitor'][$i]['harga_promo'])?"0":$obj['t_activity_competitor'][$i]['harga_promo']);
			$obj['t_activity_competitor'][$i]['sewa']=(empty($obj['t_activity_competitor'][$i]['sewa'])?'':$obj['t_activity_competitor'][$i]['sewa']);
			$obj['t_activity_competitor'][$i]['tipesewa']=(empty($obj['t_activity_competitor'][$i]['tipesewa'])?'':$obj['t_activity_competitor'][$i]['tipesewa']);
			$obj['t_activity_competitor'][$i]['description']=(empty($obj['t_activity_competitor'][$i]['description'])?'':$obj['t_activity_competitor'][$i]['description']);
			$obj['t_activity_competitor'][$i]['created_by']=(empty($obj['t_activity_competitor'][$i]['created_by'])?'':$obj['t_activity_competitor'][$i]['created_by']);
			$obj['t_activity_competitor'][$i]['modified_by']=(empty($obj['t_activity_competitor'][$i]['modified_by'])?'':$obj['t_activity_competitor'][$i]['modified_by']);
			$obj['t_activity_competitor'][$i]['transaction_id']=(empty($obj['t_activity_competitor'][$i]['transaction_id'])?'':$obj['t_activity_competitor'][$i]['transaction_id']);
			
			for($i=0; $i<$actcompetitor; $i++){
			$sqlcompetitor = 'REPLACE INTO t_activity_competitor
					(siteid, periode, salesmanid, customerid, productid, image, harga_normal, harga_promo, sewa, tipesewa, description, datecreate, created_by, created_date, modified_by, modified_date, transaction_id) 
					VALUES ("'.@$obj['t_activity_competitor'][$i]['siteid'].'",
							"'.@$obj['t_activity_competitor'][$i]['periode'].'",
							"'.@$obj['t_activity_competitor'][$i]['salesmanid'].'",
							"'.@$obj['t_activity_competitor'][$i]['customerid'].'",
							"'.@$obj['t_activity_competitor'][$i]['productid'].'",
							"'.@$obj['t_activity_competitor'][$i]['harga_normal'].'",
							"'.@$obj['t_activity_competitor'][$i]['harga_promo'].'",
							"'.@$obj['t_activity_competitor'][$i]['sewa'].'",
							"'.@$obj['t_activity_competitor'][$i]['tipesewa'].'",
							"'.@$obj['t_activity_competitor'][$i]['description'].'",
							"'.@$obj['t_activity_competitor'][$i]['datecreate'].'",
							"'.@$obj['t_activity_competitor'][$i]['created_by'].'",
							"'.@$obj['t_activity_competitor'][$i]['created_date'].'",
							"'.@$obj['t_activity_competitor'][$i]['modified_by'].'",
							"'.@$obj['t_activity_competitor'][$i]['modified_date'].'",
							"'.@$obj['t_activity_competitor'][$i]['transaction_id'].'");
							';

				if($conn->query($sqlcompetitor) === false) {
				  trigger_error(file_put_contents($filelog, $time.$ip." Insert t_activity_competitor gagal "."Wrong SQL: " . $sqlcompetitor . ' Error: ' . $conn->error."\n", FILE_APPEND | LOCK_EX), E_USER_ERROR);
				}
			}
		}

		if ($actnpd>0)
		{

			$obj['t_activity_npd_competitor'][$i]['product_name']=(empty($obj['t_activity_npd_competitor'][$i]['product_name'])?'':$obj['t_activity_npd_competitor'][$i]['product_name']);
			$obj['t_activity_npd_competitor'][$i]['harga_normal']=(empty($obj['t_activity_npd_competitor'][$i]['harga_normal'])?"0":$obj['t_activity_npd_competitor'][$i]['harga_normal']);
			$obj['t_activity_npd_competitor'][$i]['description']=(empty($obj['t_activity_npd_competitor'][$i]['description'])?'':$obj['t_activity_npd_competitor'][$i]['description']);
			$obj['t_activity_npd_competitor'][$i]['created_by']=(empty($obj['t_activity_npd_competitor'][$i]['created_by'])?'':$obj['t_activity_npd_competitor'][$i]['created_by']);
			$obj['t_activity_npd_competitor'][$i]['modified_by']=(empty($obj['t_activity_npd_competitor'][$i]['modified_by'])?'':$obj['t_activity_npd_competitor'][$i]['modified_by']);
			$obj['t_activity_npd_competitor'][$i]['transaction_id']=(empty($obj['t_activity_npd_competitor'][$i]['transaction_id'])?'':$obj['t_activity_npd_competitor'][$i]['transaction_id']);
						
			for($i=0; $i<$actnpd; $i++){
			$sqlnpd = 'REPLACE INTO t_activity_npd_competitor
					(siteid, periode, salesmanid, customerid, product_name, image, harga_normal, description, datecreate, created_by, created_date, modified_by, modified_date, transaction_id) 
					VALUES ("'.@$obj['t_activity_npd_competitor'][$i]['siteid'].'",
							"'.@$obj['t_activity_npd_competitor'][$i]['periode'].'",
							"'.@$obj['t_activity_npd_competitor'][$i]['salesmanid'].'",
							"'.@$obj['t_activity_npd_competitor'][$i]['customerid'].'",
							"'.@$obj['t_activity_npd_competitor'][$i]['product_name'].'",
							"'.@$obj['t_activity_npd_competitor'][$i]['harga_normal'].'",
							"'.@$obj['t_activity_npd_competitor'][$i]['description'].'",
							"'.@$obj['t_activity_npd_competitor'][$i]['datecreate'].'",
							"'.@$obj['t_activity_npd_competitor'][$i]['created_by'].'",
							"'.@$obj['t_activity_npd_competitor'][$i]['created_date'].'",
							"'.@$obj['t_activity_npd_competitor'][$i]['modified_by'].'",
							"'.@$obj['t_activity_npd_competitor'][$i]['modified_date'].'",
							"'.@$obj['t_activity_npd_competitor'][$i]['transaction_id'].'");
							';

				if($conn->query($sqlnpd) === false) {
				  trigger_error(file_put_contents($filelog, $time.$ip." Insert t_activity_npd_competitor gagal "."Wrong SQL: " . $sqlnpd . ' Error: ' . $conn->error."\n", FILE_APPEND | LOCK_EX), E_USER_ERROR);
				}
			}
		}
 
		if ($actpromogsk>0)
		{

			$obj['t_activity_promo_gsk'][$i]['tipepromo']=(empty($obj['t_activity_promo_gsk'][$i]['tipepromo'])?'':$obj['t_activity_promo_gsk'][$i]['tipepromo']);
			$obj['t_activity_promo_gsk'][$i]['display']=(empty($obj['t_activity_promo_gsk'][$i]['display'])?'':$obj['t_activity_promo_gsk'][$i]['display']);
			$obj['t_activity_promo_gsk'][$i]['harga_normal']=(empty($obj['t_activity_promo_gsk'][$i]['harga_normal'])?"0":$obj['t_activity_promo_gsk'][$i]['harga_normal']);
			$obj['t_activity_promo_gsk'][$i]['harga_promo']=(empty($obj['t_activity_promo_gsk'][$i]['harga_promo'])?"0":$obj['t_activity_promo_gsk'][$i]['harga_promo']);
			$obj['t_activity_promo_gsk'][$i]['description']=(empty($obj['t_activity_promo_gsk'][$i]['description'])?'':$obj['t_activity_promo_gsk'][$i]['description']);
			$obj['t_activity_promo_gsk'][$i]['created_by']=(empty($obj['t_activity_promo_gsk'][$i]['created_by'])?'':$obj['t_activity_promo_gsk'][$i]['created_by']);
			$obj['t_activity_promo_gsk'][$i]['modified_by']=(empty($obj['t_activity_promo_gsk'][$i]['modified_by'])?'':$obj['t_activity_promo_gsk'][$i]['modified_by']);
			$obj['t_activity_promo_gsk'][$i]['transaction_id']=(empty($obj['t_activity_promo_gsk'][$i]['transaction_id'])?'':$obj['t_activity_promo_gsk'][$i]['transaction_id']);

			for($i=0; $i<$actpromogsk; $i++){
			$sqlpromogsk = 'REPLACE INTO t_activity_promo_gsk
					(ssiteid, periode, salesmanid, customerid, idpromo, tipepromo, display, harga_normal, harga_promo, description, image, created_by, created_date, modified_by, modified_date, transaction_id) 
					VALUES ("'.@$obj['t_activity_promo_gsk'][$i]['siteid'].'",
							"'.@$obj['t_activity_promo_gsk'][$i]['periode'].'",
							"'.@$obj['t_activity_promo_gsk'][$i]['salesmanid'].'",
							"'.@$obj['t_activity_promo_gsk'][$i]['customerid'].'",
							"'.@$obj['t_activity_promo_gsk'][$i]['idpromo'].'",
							"'.@$obj['t_activity_promo_gsk'][$i]['tipepromo'].'",
							"'.@$obj['t_activity_promo_gsk'][$i]['display'].'",
							"'.@$obj['t_activity_promo_gsk'][$i]['harga_normal'].'",
							"'.@$obj['t_activity_promo_gsk'][$i]['harga_promo'].'",
							"'.@$obj['t_activity_promo_gsk'][$i]['description'].'",
							"'.@$obj['t_activity_promo_gsk'][$i]['created_by'].'",
							"'.@$obj['t_activity_promo_gsk'][$i]['created_date'].'",
							"'.@$obj['t_activity_promo_gsk'][$i]['modified_by'].'",
							"'.@$obj['t_activity_promo_gsk'][$i]['modified_date'].'",
							"'.@$obj['t_activity_promo_gsk'][$i]['transaction_id'].'"
							);
							';

				if($conn->query($sqlpromogsk) === false) {
				  trigger_error(file_put_contents($filelog, $time.$ip." Insert t_activity_promo_gsk gagal "."Wrong SQL: " . $sqlpromogsk . ' Error: ' . $conn->error."\n", FILE_APPEND | LOCK_EX), E_USER_ERROR);
				}
			}
		}

		$sqltgl="select tanggal from m_setup_site where siteid='".$siteid."' limit 1";
		$result=$conn->query($sqltgl);
		if($result === false) {
		  trigger_error(file_put_contents($filelog, $time.$ip." Table setup site masih kosong : Wrong SQL: " . $sqltgl . ' Error: ' . $conn->error. "\n", FILE_APPEND | LOCK_EX), E_USER_ERROR);
		}
		$resultfetch = $result->fetch_assoc();
		$resultsetup=$resultfetch["tanggal"];
		
		$sql1 = "select * from m_area_areasite where siteid='".$siteid."' and regionalid in (select distinct regionalid from mapping_sales_aas_aam a 
								join mapping_ram_aas b on a.aas_aam_tss_tsm=b.aas_aam_tss_tsm and a.salesmanid='".$salesmanid."'
								join mapping_ram_regional c on b.ram_rsm=c.ram_rsm)";
		$sql2 = "select * from m_area_kecamatan where siteid='".$siteid."'";
		$sql3 = "select * from m_area_kelurahan where siteid='".$siteid."'";
		$sql4 = "select * from m_area_kirim where siteid='".$siteid."'";
		$sql5 = "select * from m_area_kota where siteid='".$siteid."'";
		$sql6 = "select * from m_area_propinsi where siteid='".$siteid."'";
		$sql7 = "select * from m_area_regional where siteid='".$siteid."' and regionalid in (select distinct regionalid from mapping_sales_aas_aam a 
								join mapping_ram_aas b on a.aas_aam_tss_tsm=b.aas_aam_tss_tsm and a.salesmanid='".$salesmanid."'
								join mapping_ram_regional c on b.ram_rsm=c.ram_rsm) ";
		$sql8 = "select * from m_area_subarea where siteid='".$siteid."' and regionalid in (select distinct regionalid from mapping_sales_aas_aam a 
								join mapping_ram_aas b on a.aas_aam_tss_tsm=b.aas_aam_tss_tsm and a.salesmanid='".$salesmanid."'
								join mapping_ram_regional c on b.ram_rsm=c.ram_rsm)";
		$sql9 = "select * from m_bank";
		// alamat, 
		$sql10 = "select siteid, customerid_m, customerid, replace(trim(nama_customer),'','') nama_customer,
						 replace(replace(alamat,'  ',' '),'�','') alamat,
						 top_cust, tipe_bayar, 
						 replace(replace(replace(replace(replace(tipe_tax,'      ',''),'       ',''),'      ',''),'      ',''),'�','') tipe_tax, 
						 kelurahanid, kecamatanid, kotaid, propinsiid, kodepos, telp, email, segmentid, typeid, classid, regionalid, areaid, subareaid,
						 areakirimid, createdate,latitude, longitude, saldo_piutang, saldo_overdue,
						 limit_kredit, sisa_limit_kredit, nilai_sales, salesmanid, tanggal, jenis_saran, 
						 deskripsi_saran, trim(mcc) mcc, (select count(1) from t_sales_rrk_trans where customerid=cst.customerid and salesmanid=cst.salesmanid 
												and periode between DATE_ADD('$resultsetup', INTERVAL - DAYOFMONTH('$resultsetup')+1 day) and '$resultsetup'
												and order_time is not null) as mnc, lac, cid, spot_id
						 from m_customer cst where cst.siteid='$siteid' and cst.salesmanid='$salesmanid' and (cst.customerid <> '' or cst.customerid <> null)
						 ";
		//200780
		//201000
		$sql11 = "select * from m_customer_class";
		$sql12 = "select * from m_customer_segment";
		$sql13 = "select * from m_customer_spot";
		$sql14 = "select * from m_customer_type";
		$sql15 = "select * from m_product where status='A';";
		$sql16 = "select * from m_sales_salesman_category where salesmanid='".$salesmanid."'";
		$sql17 = "select * from t_ar_ink_detail where siteid='".$siteid."' and salesmanid='".$salesmanid."'";
		$sql18 = "select * from t_sales_rrk where siteid='".$siteid."' and salesmanid='".$salesmanid."' and (customerid <> '' or customerid <> null)";
		$sql19 = "select * from t_sales_crc where siteid='".$siteid."' and salesmanid='".$salesmanid."'";
		$sql20 = "select * from t_sales_rrk_reason";
		//$sql21 = "select * from t_sales_rrk_image where siteid='".$siteid."' and salesmanid='".$salesmanid."'";
		$sql22 = "select * from t_sales_promo where categoryid in (select categoryid from m_sales_salesman_category where salesmanid='".$salesmanid."')
				  and ('".$resultsetup."' between mulai_tanggal and selesai_tanggal)";
		$sql23 = "select * from t_productivity where siteid='".$siteid."' and salesmanid='".$salesmanid."' ";
		$sql24 = "select * from t_target_prinsipal_salesman where siteid='".$siteid."' and salesmanid='".$salesmanid."' ";
		$sql25 = "select * from t_target_group_salesman where siteid='".$siteid."' and salesmanid='".$salesmanid."' ";
		$sql26 = "select companyid as companyId,key_conf as keyConf,value,keterangan  from m_config";
		//product competitor
		$sql27 = "select * from m_product_competitor where status='A';";
		$sql28 = "select * from mapping_promo_active where now() between start_periode and end_periode;";

		$sqlareafilter = " and status='0' limit 0,5000; ";
		$sqlareafilter_null = " and status='2'";
		$sqlareafilter_sts_send = " and status_send='0'";
		$sqlareafilter_periode = " and periode='".$resultsetup."'";
		$sqlfilter_periode = " and periode='".$resultsetup."'";
		$sqlareafilter_periode_ink = " and tgl_ink='".$resultsetup."'";
		$sqlareafilter_sts_send_null = " and status_send='2'";
		$vlimit = " limit 1";

		if ($tglsetup!=$resultsetup)
		{
			if ($tglsetup=='')
			{
				//area
				//echo "1".$tglsetup;
				$result1 = $conn->query($sql1);
				$result2 = $conn->query($sql2);
				$result3 = $conn->query($sql3);
				$result4 = $conn->query($sql4);
				$result5 = $conn->query($sql5);
				$result6 = $conn->query($sql6);
				$result7 = $conn->query($sql7);
				$result8 = $conn->query($sql8);

				$result17 = $conn->query($sql17.$sqlareafilter_periode_ink);
				$result18 = $conn->query($sql18.$sqlareafilter_periode);
				$result19 = $conn->query($sql19.$sqlareafilter_periode);

				$result20 = $conn->query($sql20);
				//$result21 = $conn->query($sql21);
				$result22 = $conn->query($sql22);
				$result23 = $conn->query($sql23.$sqlfilter_periode);
				$result24 = $conn->query($sql24.$sqlfilter_periode);
				$result25 = $conn->query($sql25.$sqlfilter_periode);

			}else if($tglsetup!=$resultsetup)
			{
				//echo "2".$tglsetup;
				//area
				$result1 = $conn->query($sql1.$sqlareafilter);
				$result2 = $conn->query($sql2.$sqlareafilter);
				$result3 = $conn->query($sql3.$sqlareafilter);
				$result4 = $conn->query($sql4.$sqlareafilter);
				$result5 = $conn->query($sql5.$sqlareafilter);
				$result6 = $conn->query($sql6.$sqlareafilter);
				$result7 = $conn->query($sql7.$sqlareafilter);
				$result8 = $conn->query($sql8.$sqlareafilter);
				$result17 = $conn->query($sql17.$sqlareafilter_periode_ink);
				$result18 = $conn->query($sql18.$sqlareafilter_periode);
				$result19 = $conn->query($sql19.$sqlareafilter_periode);
				$result20 = $conn->query($sql20);
				//$result21 = $conn->query($sql21);
				$result22 = $conn->query($sql22);
				$result23 = $conn->query($sql23.$sqlfilter_periode);
				$result24 = $conn->query($sql24.$sqlfilter_periode);
				$result25 = $conn->query($sql25.$sqlfilter_periode);

			}else 
			{
				//echo "3".$tglsetup;
				//area
				$result1 = $conn->query($sql1.$sqlareafilter_null);
				$result2 = $conn->query($sql2.$sqlareafilter_null);
				$result3 = $conn->query($sql3.$sqlareafilter_null);
				$result4 = $conn->query($sql4.$sqlareafilter_null);
				$result5 = $conn->query($sql5.$sqlareafilter_null);
				$result6 = $conn->query($sql6.$sqlareafilter_null);
				$result7 = $conn->query($sql7.$sqlareafilter_null);
				$result8 = $conn->query($sql8.$sqlareafilter_null);
				$result17 = $conn->query($sql17.$sqlareafilter_sts_send_null);
				$result18 = $conn->query($sql18.$sqlareafilter_sts_send_null);
				$result19 = $conn->query($sql19.$sqlareafilter_sts_send_null);
				$result20 = $conn->query($sql20);
				//$result21 = $conn->query($sql21);
				$result22 = $conn->query($sql22);
				$result23 = $conn->query($sql23.$sqlfilter_periode);
				$result24 = $conn->query($sql24.$sqlfilter_periode);
				$result25 = $conn->query($sql25.$sqlfilter_periode);
			}
			
			//bank
			$result9 = $conn->query($sql9);
			
			//customer
			$result10 = $conn->query($sql10);
			$result11 = $conn->query($sql11);
			$result12 = $conn->query($sql12);
			$result13 = $conn->query($sql13);
			$result14 = $conn->query($sql14);
			
			//product
			$result15 = $conn->query($sql15);
			
			//salesman category
			$result16 = $conn->query($sql16);
			
			$result26 = $conn->query($sql26);
			//product competitor
			$result27 = $conn->query($sql27);
			$result28 = $conn->query($sql28);

			$array1 = array();
			$array2 = array();
			$array3 = array();
			$array4 = array();
			$array5 = array();
			$array6 = array();
			$array7 = array();
			$array8 = array();
			$array9 = array();
			$array10 = array();
			$array11 = array();
			$array12 = array();
			$array13 = array();
			$array14 = array();
			$array15 = array();
			$array16 = array();
			$array17 = array();
			$array18 = array();
			$array19 = array();
			$array20 = array();
			//$array21 = array();
			$array22 = array();
			$array23 = array();
			$array24 = array();
			$array25 = array();
			$array26 = array();
			$array27 = array();
			$array28 = array();

			//while($row=$result1->fetch_assoc()){ $array1[] = $row; }
			$rows = array();
			while($row=$result1->fetch_assoc()){
				$rows["areaid"] = htmlentities(trim($row["areaid"])); 
				$rows["regionalid"] = htmlentities(trim($row["regionalid"])); 
				$rows["siteid"] = htmlentities(trim($row["siteid"])); 
 				$rows["branchid"] = htmlentities(trim($row["branchid"])); 
				$rows["companyid"] = htmlentities(trim($row["companyid"])); 
				$rows["headofficeid"] = htmlentities(trim($row["headofficeid"])); 
				$rows["nama_area"] = htmlentities(trim($row["nama_area"])); 
				$rows["status"] = htmlentities(trim($row["status"])); 
				$array1[] = $rows;
			}

			//while($row=$result2->fetch_assoc()){ $array2[] = $row; }
			/* data kota/kabupaten*/
			$rows = array();
			while($row=$result2->fetch_assoc()){
				$rows["kecamatanid"] = htmlentities(trim($row["kecamatanid"])); 
				$rows["kotaid"] = htmlentities(trim($row["kotaid"])); 
				$rows["propinsiid"] = htmlentities(trim($row["propinsiid"])); 
				$rows["nama_kecamatan"] = htmlentities(trim($row["nama_kecamatan"])); 
				$rows["siteid"] = htmlentities(trim($row["siteid"])); 
				$rows["status"] = htmlentities(trim($row["status"])); 
				$array2[] = $rows;
			}
			
			//while($row=$result3->fetch_assoc()){ $array3[] = $row; }
			/* data kelurahan
			$rows = array();
			while($row=$result3->fetch_assoc()){
				$rows["kelurahanid"] = htmlentities(trim($row["kelurahanid"])); 
				$rows["kecamatanid"] = htmlentities(trim($row["kecamatanid"])); 
				$rows["kotaid"] = htmlentities(trim($row["kotaid"])); 
				$rows["propinsiid"] = htmlentities(trim($row["propinsiid"])); 
				$rows["nama_kelurahan"] = htmlentities(trim($row["nama_kelurahan"])); 
				$rows["siteid"] = htmlentities(trim($row["siteid"])); 
				$rows["status"] = htmlentities(trim($row["status"])); 
				$array3[] = $rows;
			} */
		
									//echo "ok";die();

			//while($row=$result4->fetch_assoc()){ $array4[] = $row; }
			/*$rows = array();
			while($row=$result4->fetch_assoc()){
				$rows["areakirimid"] = htmlentities(trim($row["areakirimid"])); 
				$rows["nama_areakirim"] = htmlentities(trim($row["nama_areakirim"])); 
				$rows["siteid"] = htmlentities(trim($row["siteid"])); 
				$rows["branchid"] = htmlentities(trim($row["branchid"])); 
				$rows["companyid"] = htmlentities(trim($row["companyid"])); 
				$rows["headofficeid"] = htmlentities(trim($row["headofficeid"])); 
				$rows["flag_kirim"] = htmlentities(trim($row["flag_kirim"])); 
				$rows["status"] = htmlentities(trim($row["status"])); 
				$array4[] = $rows;
			}*/

			//while($row=$result5->fetch_assoc()){ $array5[] = $row; }
			$rows = array();
			while($row=$result5->fetch_assoc()){
				$rows["kotaid"] = htmlentities(trim($row["kotaid"]));
				$rows["propinsiid"] = htmlentities(trim($row["propinsiid"]));
				$rows["nama_kota"] = htmlentities(trim($row["nama_kota"]));
				$rows["siteid"] = htmlentities(trim($row["siteid"]));
				$rows["status"] = htmlentities(trim($row["status"]));
				$array5[] = $rows;
			}
			
			//while($row=$result6->fetch_assoc()){ $array6[] = $row; }
			$rows = array();
			while($row=$result6->fetch_assoc()){
				$rows["propinsiid"] = htmlentities(trim($row["propinsiid"]));
				$rows["nama_propinsi"] = htmlentities(trim($row["nama_propinsi"]));
				$rows["wilayahid"] = htmlentities(trim($row["wilayahid"]));
				$rows["siteid"] = htmlentities(trim($row["siteid"]));
				$rows["status"] = htmlentities(trim($row["status"]));
				$array6[] = $rows;
			}
			
			//while($row=$result7->fetch_assoc()){ $array7[] = $row; }
			$rows = array();
			while($row=$result7->fetch_assoc()){
				$rows["regionalid"] = htmlentities(trim($row["regionalid"]));
				$rows["siteid"] = htmlentities(trim($row["siteid"]));
				$rows["branchid"] = htmlentities(trim($row["branchid"]));
				$rows["companyid"] = htmlentities(trim($row["companyid"]));
				$rows["headofficeid"] = htmlentities(trim($row["headofficeid"]));
				$rows["nama_regional"] = htmlentities(trim($row["nama_regional"]));
				$rows["status"] = htmlentities(trim($row["status"]));
				$array7[] = $rows;
			}
			
			//while($row=$result8->fetch_assoc()){ $array8[] = $row; }
			$rows = array();
			while($row=$result8->fetch_assoc()){
				$rows["subareaid"] = htmlentities(trim($row["subareaid"]));
				$rows["areaid"] = htmlentities(trim($row["areaid"]));
				$rows["regionalid"] = htmlentities(trim($row["regionalid"]));
				$rows["siteid"] = htmlentities(trim($row["siteid"]));
				$rows["branchid"] = htmlentities(trim($row["branchid"]));
				$rows["companyid"] = htmlentities(trim($row["companyid"]));
				$rows["headofficeid"] = htmlentities(trim($row["headofficeid"]));
				$rows["nama_area"] = htmlentities(trim($row["nama_area"]));
				$rows["keterangan"] = htmlentities(trim($row["keterangan"]));
				$rows["status"] = htmlentities(trim($row["status"]));
				$array8[] = $rows;
			}
			
			//while($row=$result9->fetch_assoc()){ $array9[] = $row; }
			$rows = array();
			while($row=$result9->fetch_assoc()){
				$rows["bankid"] = htmlentities(trim($row["bankid"]));
				$rows["nama_bank"] = htmlentities(trim($row["nama_bank"]));
				$array9[] = $rows;
			}
			
			while($row=$result10->fetch_assoc()){ $array10[] = $row; }
			/*$rowscust = array();
			while($row=$result10->fetch_assoc()){
					
				$rowscust["siteid"] = htmlentities(trim($row["siteid"]));
				$rowscust["customerid_m"] = htmlentities(trim($row["customerid_m"]));
				$rowscust["customerid"] = htmlentities(trim($row["customerid"]));
				$rowscust["nama_customer"] = htmlentities(trim($row["nama_customer"]));
				$rowscust["alamat"] = htmlentities(trim($row["alamat"]));
				$rowscust["top_cust"] = '0';//htmlentities(trim($row["top_cust"]));
				$rowscust["tipe_bayar"] = 'T';//htmlentities(trim($row["tipe_bayar"]));
				$rowscust["tipe_tax"] = 'NONE';//htmlentities(trim($row["tipe_tax"]));
				$rowscust["kelurahanid"] = htmlentities(trim($row["kelurahanid"]));
				$rowscust["kecamatanid"] = htmlentities(trim($row["kecamatanid"]));
				$rowscust["kotaid"] = htmlentities(trim($row["kotaid"]));
				$rowscust["propinsiid"] = htmlentities(trim($row["propinsiid"]));
				$rowscust["kodepos"] = htmlentities(trim($row["kodepos"]));
				$rowscust["telp"] = htmlentities(trim($row["telp"]));
				$rowscust["email"] = htmlentities(trim($row["email"]));
				$rowscust["segmentid"] = htmlentities(trim($row["segmentid"]));
				$rowscust["typeid"] = htmlentities(trim($row["typeid"]));
				$rowscust["classid"] = htmlentities(trim($row["classid"]));
				$rowscust["regionalid"] = htmlentities(trim($row["regionalid"]));
				$rowscust["areaid"] = htmlentities(trim($row["areaid"]));
				$rowscust["subareaid"] = htmlentities(trim($row["subareaid"]));
				$rowscust["areakirimid"] = htmlentities(trim($row["areakirimid"]));
				$rowscust["createdate"] = $row["createdate"];
				$rowscust["latitude"] = $row["latitude"];
				$rowscust["longitude"] = $row["longitude"];
				$rowscust["saldo_piutang"] = $row["saldo_piutang"];
				$rowscust["saldo_overdue"] = $row["saldo_overdue"];
				$rowscust["limit_kredit"] = $row["limit_kredit"];
				$rowscust["sisa_limit_kredit"] = $row["sisa_limit_kredit"];
				$rowscust["nilai_sales"] = $row["nilai_sales"];
				$rowscust["salesmanid"] = htmlentities(trim($row["salesmanid"]));
				$rowscust["tanggal"] = $row["tanggal"];
				$rowscust["jenis_saran"] = htmlentities(trim($row["jenis_saran"]));
				$rowscust["deskripsi_saran"] = htmlentities(trim($row["deskripsi_saran"]));
				$rowscust["mcc"] = htmlentities(trim($row["mcc"]));
				$rowscust["mnc"] = htmlentities(trim($row["mnc"]));
				$rowscust["lac"] = htmlentities(trim($row["lac"]));
				$rowscust["cid"] = htmlentities(trim($row["cid"]));
				$rowscust["spot_id"] = htmlentities(trim($row["spot_id"]));			
				$array10[] = $rowscust; 
			
			}*/
			
			while($row=$result11->fetch_assoc()){ $array11[] = $row; }
			/*$rows = array();
			while($row=$result11->fetch_assoc()){
				$rows["classid"] = htmlentities(trim($row["classid"]));
				$rows["nama_class"] = htmlentities(trim($row["nama_class"]));
				$rows["flag_harga"] = htmlentities(trim($row["flag_harga"]));
				$array11[] = $rows;
			}*/
			
			while($row=$result12->fetch_assoc()){ $array12[] = $row; }
			/*$rows = array();
			while($row=$result12->fetch_assoc()){
				$rows["segmentid"] = htmlentities(trim($row["segmentid"]));
				$rows["nama_segment"] = htmlentities(trim($row["nama_segment"]));
				$rows["flag_harga"] = htmlentities(trim($row["flag_harga"]));
				$array12[] = $rows;
			}*/
			
			while($row=$result13->fetch_assoc()){ $array13[] = $row; }
			/* $rows = array();
			while($row=$result13->fetch_assoc()){
				$rows["cust_id"] = htmlentities(trim($row["cust_id"]));
				$rows["nama_spot"] = htmlentities(trim($row["nama_spot"]));
				$array13[] = $rows;
			} */
			
			while($row=$result14->fetch_assoc()){ $array14[] = $row; }
			/* $rows = array();
			while($row=$result14->fetch_assoc()){
				$rows["typeid"] = htmlentities(trim($row["typeid"]));
				$rows["nama_type"] = htmlentities(trim($row["nama_type"]));
				$rows["group1"] = htmlentities(trim($row["group1"]));
				$array14[] = $rows;
			} */

			//while($row=$result15->fetch_assoc()){ $array15[] = $row; }
			$rows = array();
			while($row=$result15->fetch_assoc()){
				$rows["productid"] = htmlentities(trim($row["productid"]));
				$rows["nama_invoice"] = trim($row["nama_invoice"]);
				$rows["categoryid"] = '11';//htmlentities(trim($row["categoryid"]));
				$rows["nama_category"] = 'GL';//htmlentities(trim($row["nama_category"]));
				$rows["brandid"] = htmlentities(trim($row["brandid"]));
				$rows["nama_brand"] = htmlentities(trim($row["nama_brand"]));
				$rows["sat_besar"] = 'PCS';//htmlentities(trim($row["sat_besar"]));
				$rows["sat_sedang"] = 'PCS';//htmlentities(trim($row["sat_sedang"]));
				$rows["sat_kecil"] = 'PCS';//htmlentities(trim($row["sat_kecil"]));
				$rows["isi_besar"] = htmlentities(trim($row["isi_besar"]));
				$rows["isi_sedang"] = htmlentities(trim($row["isi_sedang"]));
				$rows["isi_kecil"] = htmlentities(trim($row["isi_kecil"]));
				$rows["h_grosir"] = htmlentities(trim($row["h_grosir"]));
				$rows["h_ritel"] = htmlentities(trim($row["h_ritel"]));
				$rows["h_motoris_ritel"] = htmlentities(trim($row["h_motoris_ritel"]));
				$rows["qty_stock"] = htmlentities(trim($row["qty_stock"]));
				$rows["nilai_sales"] = htmlentities(trim($row["nilai_sales"]));
				$rows["qty_sales"] = htmlentities(trim($row["qty_sales"]));
				$rows["qty_sales3"] = htmlentities(trim($row["qty_sales3"]));
				$rows["qty_sales_rata3"] = htmlentities(trim($row["qty_sales_rata3"]));
				$rows["nilai_sales3"] = htmlentities(trim($row["nilai_sales3"]));
				$rows["nilai_sales_rata3"] = htmlentities(trim($row["nilai_sales_rata3"]));
				$array15[] = $rows;
			}

			//while($row=$result16->fetch_assoc()){ $array16[] = $row; }
			$rows = array();
			while($row=$result16->fetch_assoc()){
				$rows["siteid"] = htmlentities(trim($row["siteid"]));
				$rows["salesmanid"] = htmlentities(trim($row["salesmanid"]));
				$rows["categoryid"] = htmlentities(trim($row["categoryid"]));
				$rows["nama_category"] = htmlentities(trim($row["nama_category"]));
				$array16[] = $rows;
			}
			
			while($row=$result17->fetch_assoc()){ $array17[] = $row; }
			/*$rows = array();
			while($row=$result17->fetch_assoc()){
				$rows["siteid"] = htmlentities(trim($row["siteid"]));
				$rows["no_sales"] = htmlentities(trim($row["no_sales"]));
				$rows["no_ink"] = htmlentities(trim($row["no_ink"]));
				$rows["tgl_ink"] = htmlentities(trim($row["tgl_ink"]));
				$rows["tanggal"] = htmlentities(trim($row["tanggal"]));
				$rows["salesmanid"] = htmlentities(trim($row["salesmanid"]));
				$rows["customerid"] = htmlentities(trim($row["customerid"]));
				$rows["retur"] = htmlentities(trim($row["retur"]));
				$rows["bayar"] = htmlentities(trim($row["bayar"]));
				$rows["no_transfer"] = htmlentities(trim($row["no_transfer"]));
				$rows["tgl_transfer"] = htmlentities(trim($row["tgl_transfer"]));
				$rows["no_giro"] = htmlentities(trim($row["no_giro"]));
				$rows["tgl_jt_giro"] = htmlentities(trim($row["tgl_jt_giro"]));
				$rows["tgl_terima_giro"] = htmlentities(trim($row["tgl_terima_giro"]));
				$rows["AC_id_giro"] = htmlentities(trim($row["AC_id_giro"]));
				$rows["status_giro"] = htmlentities(trim($row["status_giro"]));
				$rows["bank_id"] = htmlentities(trim($row["bank_id"]));
				$rows["bank_name"] = htmlentities(trim($row["bank_name"]));
				$rows["bayar_tunai"] = htmlentities(trim($row["bayar_tunai"]));
				$rows["bayar_transfer"] = htmlentities(trim($row["bayar_transfer"]));
				$rows["bayar_giro"] = htmlentities(trim($row["bayar_giro"]));
				$rows["status_send"] = htmlentities(trim($row["status_send"]));
				$array17[] = $rows;
			}*/

			while($row=$result18->fetch_assoc()){ $array18[] = $row; }
			/*$rows = array();
			while($row=$result18->fetch_assoc()){
				$rows["periode"] = htmlentities(trim($row["periode"]));
				$rows["siteid"] = htmlentities(trim($row["siteid"]));
				$rows["salesmanid"] = htmlentities(trim($row["salesmanid"]));
				$rows["nama_salesman"] = htmlentities(trim($row["nama_salesman"]));
				$rows["customerid"] = htmlentities(trim($row["customerid"]));
				$rows["flag_proses"] = htmlentities(trim($row["flag_proses"]));
				$rows["tgl_proses"] = htmlentities(trim($row["tgl_proses"]));
				$rows["keterangan"] = htmlentities(trim($row["keterangan"]));
				$rows["user_create"] = htmlentities(trim($row["user_create"]));
				$rows["date_create"] = htmlentities(trim($row["date_create"]));
				$rows["user_update"] = htmlentities(trim($row["user_update"]));
				$rows["date_update"] = htmlentities(trim($row["date_update"]));
				$rows["minggu"] = htmlentities(trim($row["minggu"]));
				$rows["call_reasonid"] = htmlentities(trim($row["call_reasonid"]));
				$rows["reason"] = htmlentities(trim($row["reason"]));
				$rows["status_send"] = htmlentities(trim($row["status_send"]));
				$rows["check_in"] = htmlentities(trim($row["check_in"]));
				$rows["tipe"] = htmlentities(trim($row["tipe"]));
				$rows["order_time"] = htmlentities(trim($row["order_time"]));
				$rows["mcc"] = htmlentities(trim($row["mcc"]));
				$rows["mnc"] = htmlentities(trim($row["mnc"]));
				$rows["lac"] = htmlentities(trim($row["lac"]));
				$rows["cid"] = htmlentities(trim($row["cid"]));
				$rows["latitude_cell"] = htmlentities(trim($row["latitude_cell"]));
				$rows["longitude_cell"] = htmlentities(trim($row["longitude_cell"]));
				$rows["jenis"] = htmlentities(trim($row["jenis"]));
				$rows["deskripsi"] = htmlentities(trim($row["deskripsi"]));
				$rows["alasan"] = htmlentities(trim($row["alasan"]));
				$array18[] = $rows;
			}*/
			
			while($row=$result19->fetch_assoc()){ $array19[] = $row; }
			/*$rows = array();
			while($row=$result19->fetch_assoc()){
				$rows["siteid"] = htmlentities(trim($row["siteid"]));
				$rows["periode"] = trim($row["periode"];
				$rows["salesmanid"] = htmlentities(trim($row["salesmanid"]));
				$rows["customerid"] = htmlentities(trim($row["customerid"]));
				$rows["productid"] = htmlentities(trim($row["productid"]));
				$rows["categoryid"] = htmlentities(trim($row["categoryid"]));
				$rows["brandid"] = htmlentities(trim($row["brandid"]));
				$rows["harga"] = htmlentities(trim($row["harga"]));
				$rows["qty_3_bulan"] = htmlentities(trim($row["qty_3_bulan"]));
				$rows["qty_rata"] = htmlentities(trim($row["qty_rata"]));
				$rows["qty_akhir"] = htmlentities(trim($row["qty_akhir"]));
				$rows["stock_level"] = htmlentities(trim($row["stock_level"]));
				$rows["stock_buffer"] = htmlentities(trim($row["stock_buffer"]));
				$rows["qty_saran_order"] = htmlentities(trim($row["qty_saran_order"]));
				$rows["qty_fix_order"] = htmlentities(trim($row["qty_fix_order"]));
				$rows["date_update"] = $row["date_update"];
				$rows["status_send"] = htmlentities(trim($row["status_send"]));
				$rows["target_value"] = htmlentities(trim($row["target_value"]));
				$rows["target_qty"] = htmlentities(trim($row["target_qty"]));
				$rows["total_target_value"] = htmlentities(trim($row["total_target_value"]));
				$rows["total_target_qty"] = htmlentities(trim($row["total_target_qty"]));
				$rows["total_sales_value"] = htmlentities(trim($row["total_sales_value"]));
				$rows["total_sales_qty"] = htmlentities(trim($row["total_sales_qty"]));
				$rows["target_vs_sales"] = htmlentities(trim($row["target_vs_sales"]));
				$rows["nomorpromo"] = htmlentities(trim($row["nomorpromo"]));
				$rows["productinfo"] = htmlentities(trim($row["productinfo"]));
				$rows["productinfo_1"] = htmlentities(trim($row["productinfo_1"]));
				$rows["price"] = $row["price"];
				$rows["exp_date"] = $row["exp_date"];
				$array19[] = $rows;
			}*/
			
			//while($row=$result20->fetch_assoc()){ $array20[] = $row; }
			$rows = array();
			while($row=$result20->fetch_assoc()){
				$rows["call_reasonid"] = htmlentities(trim($row["call_reasonid"]));
				$rows["reason"] = htmlentities(trim($row["reason"]));
				$array20[] = $rows;
			}
			
			//while($row=$result21->fetch_assoc()){ $array21[] = $row; }
			while($row=$result22->fetch_assoc()){ $array22[] = $row; }
			//new 20191104
			/*$rows = array();
			while($row=$result22->fetch_assoc()){
				$rows["id"] = htmlentities(trim($row["id"])); 
				$rows["siteid"] = htmlentities(trim($row["siteid"])); 
				$rows["categoryid"] = htmlentities(trim($row["categoryid"])); 
				$rows["no_promo"] = htmlentities(trim($row["no_promo"])); 
				$rows["judul"] = htmlentities(trim($row["judul"])); 
				$rows["mulai_tanggal"] = $row["mulai_tanggal"]; 
				$rows["selesai_tanggal"] = $row["selesai_tanggal"]; 
				$rows["desription_promo"] = htmlentities(trim($row["desription_promo"])); 
				$array22[] = $rows;
			}*/

			while($row=$result23->fetch_assoc()){ $array23[] = $row; }
			/*$rows = array();
			while($row=$result23->fetch_assoc()){
				$rows["periode"] = $row["periode"]; 
				$rows["siteid"] = htmlentities(trim($row["siteid"])); 
				$rows["salesmanid"] = htmlentities(trim($row["salesmanid"])); 
				$rows["nama_salesman"] = htmlentities(trim($row["nama_salesman"])); 
				$rows["target"] = htmlentities(trim($row["target"])); 
				$rows["sales"] = htmlentities(trim($row["sales"])); 
				$rows["percent"] = htmlentities(trim($row["percent"])); 
				$rows["ob"] = htmlentities(trim($row["ob"])); 
				$rows["oa"] = htmlentities(trim($row["oa"])); 
				$rows["oavsob"] = htmlentities(trim($row["oavsob"])); 
				$rows["ec"] = htmlentities(trim($row["ec"])); 
				$rows["ecvsoa"] = htmlentities(trim($row["ecvsoa"])); 
				$rows["salesvsec"] = htmlentities(trim($row["salesvsec"]));
				$array23[] = $rows;
			}*/
			
			while($row=$result24->fetch_assoc()){ $array24[] = $row; }
			/*$rows = array();
			while($row=$result24->fetch_assoc()){
				$rows["periode"] = $row["periode"]; 
				$rows["siteid"] = htmlentities(trim($row["siteid"])); 
				$rows["salesmanid"] = htmlentities(trim($row["salesmanid"])); 
				$rows["nama_salesman"] = htmlentities(trim($row["nama_salesman"])); 
				$rows["prinsipalid"] = htmlentities(trim($row["prinsipalid"])); 
				$rows["nama_prinsipal"] = htmlentities(trim($row["nama_prinsipal"])); 
				$rows["target"] = htmlentities(trim($row["target"])); 
				$rows["sales"] = htmlentities(trim($row["sales"])); 
				$rows["percent"] = htmlentities(trim($row["percent"])); 
				$array24[] = $rows;
			}*/
			
			while($row=$result25->fetch_assoc()){ $array25[] = $row; }
			/*$rows = array();
			while($row=$result25->fetch_assoc()){
				$rows["periode"] = htmlentities(trim($row["periode"])); 
				$rows["siteid"] = htmlentities(trim($row["siteid"])); 
				$rows["salesmanid"] = htmlentities(trim($row["salesmanid"])); 
				$rows["nama_salesman"] = htmlentities(trim($row["nama_salesman"])); 
				$rows["groupid"] = htmlentities(trim($row["groupid"])); 
				$rows["nama_group"] = htmlentities(trim($row["nama_group"])); 
				$rows["target"] = htmlentities(trim($row["target"])); 
				$rows["sales"] = htmlentities(trim($row["sales"])); 
				$rows["percent"] = htmlentities(trim($row["percent"])); 
				$array25[] = $rows;
			}*/

			//while($row=$result26->fetch_assoc()){ $array26[] = $row; }
			$rows = array();
			while($row=$result26->fetch_assoc()){
				//$rows["Id"] = htmlentities(trim($row["Id"]));
				$rows["companyId"] = htmlentities(trim($row["companyId"])); 
				$rows["keyConf"] = htmlentities(trim($row["keyConf"])); 
				$rows["value"] = htmlentities(trim($row["value"])); 
				$rows["keterangan"] = htmlentities(trim($row["keterangan"])); 
				$array26[] = $rows;
			}

			while($row=$result27->fetch_assoc()){ $array27[] = $row; }
			while($row=$result28->fetch_assoc()){ $array28[] = $row; }

			$respon = array(
							"respon" => array(
											array(
											"status" => "1",
											"tanggal" => $resultsetup,
											"msg" => "Synchronize data"
											)
										),
							"m_area_areasite" => $array1,
							"m_area_kecamatan" => $array2, 
							"m_area_kelurahan" => $array3, 
							"m_area_kirim" => $array4, 
							"m_area_kota" => $array5, 
							"m_area_propinsi" => $array6, 
							"m_area_regional" => $array7, 
							"m_area_subarea" => $array8, 
							"m_bank" => $array9, 
							"m_customer" => $array10, 
							"m_customer_class" => $array11, 
							"m_customer_segment" => $array12, 
							"m_customer_spot" => $array13, 
							"m_customer_type" => $array14, 
							"m_product" => $array15, 
							"m_sales_salesman_category" => $array16, 
							"t_ar_ink_detail" => $array17, 
							"t_sales_rrk" => $array18, 
							"t_sales_crc" => $array19,
							"t_sales_rrk_reason" => $array20,
							"t_sales_promo" => $array22,
							"t_productivity" => $array23,
							"t_target_prinsipal_salesman" => $array24,
							"t_target_group_salesman" => $array25,
							"m_config" => $array26,
							"m_product_competitor" => $array27,
							"mapping_promo_active" => $array28
							);
			//echo json_encode($respon,JSON_UNESCAPED_UNICODE);
			//var_dump($respon);
			$json_encoded_string = json_encode($respon);
			//echo "$json_encoded_string"; die();
			$json_encoded_string = str_replace("\r", '\r', $json_encoded_string);
			$json_encoded_string = str_replace("\n", '\n', $json_encoded_string);			
			
			 //$json  = json_encode($respon,JSON_UNESCAPED_UNICODE);
			 echo $json_encoded_string;

			//var_dump($respon);
			//$json  = json_encode($respon);
			//var_dump($json);
			//echo json_last_error();
			
			file_put_contents($filelog, $time.$ip." Synchronize data first finish \n".$json_encoded_string."\n", FILE_APPEND | LOCK_EX);
		}else{
			/* $result17 = $conn->query($sql17);
			$result18 = $conn->query($sql18);
			$result19 = $conn->query($sql19);
			$array17 = array();
			$array18 = array();
			$array19 = array();
			while($row=mysqli_fetch_assoc($result17)){ $array17[] = $row; }
			while($row=mysqli_fetch_assoc($result18)){ $array18[] = $row; }
			while($row=mysqli_fetch_assoc($result19)){ $array19[] = $row; }
			 */
			//print ('{"respon":[{"status":"2","tanggal":"'.$resultsetup.'","msg":"Synchronize data"}]');
/* 			print (',"t_sales_rrk_trans":');
			print json_encode($array17); 
			print (',"t_sales_rrk":');
			print json_encode($array18); 
			print (',"t_sales_crc":');
			print json_encode($array19); 
 			print ('}');*/
 
 			$respon = array("respon" => array(
											array(
											"status" => "2",
											"tanggal" => $resultsetup,
											"msg" => "Synchronize data"
											)
										)
							);

			//echo json_encode($respon,JSON_UNESCAPED_UNICODE);
			
			//var_dump($respon);
			$json  = json_encode($respon);
			echo $json;
			
			$time = getDatetimeNow('[d/M/Y H:i:s]');
			file_put_contents($filelog, $time.$ip." Synchronize data seconds finish \n".$json."\n", FILE_APPEND | LOCK_EX);
		}
		//$sql1updatesetupsite = "update m_setup_site set tanggal= '".date("Y-n-j")."' where siteid='".$obj['m_setup_site'][0]['siteid']."'";
		//$conn->query($sql1updatesetupsite) or die(file_put_contents($filelog, $time.$ip." update setup site gagal: ".mysqli_error($conn)."\n", FILE_APPEND | LOCK_EX));
		mysqli_close($conn);
		$time = getDatetimeNow('[d/M/Y H:i:s]');
		file_put_contents($filelog, $time.$ip." Synchronize data success \n", FILE_APPEND | LOCK_EX);
	}else{
		print ('{"respon":[{"status":"0","msg":"Setting setup site not define"}]}');
		$time = getDatetimeNow('[d/M/Y H:i:s]');
		file_put_contents($filelog, $time.$ip." Setting setup site not define \n", FILE_APPEND | LOCK_EX);
	}

?>
