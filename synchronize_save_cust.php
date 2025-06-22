<?php 

include "conf_conn.php";
$json = file_get_contents('php://input');
$obj = json_decode($json,true);
$tglsynch=getDatetimeNow("Y-m-d H:i:s");

$custCount = count($obj['m_customer']); // customerid is null and customerid_m is not null
echo $custCount;
$conn = @mysqli_connect($host, $uname, $pwd, $db);
if (!$conn){
	print ('{"respon":[{"status":"0","msg":"Koneksi ke database server bermasalah"}]}');
	die();
}

		if ($custCount>0) 
		{
			for($i=0; $i<$custCount; $i++){
			
			$obj['m_customer'][$i]['customerid']=(empty($obj['m_customer'][$i]['customerid'])?"":$obj['m_customer'][$i]['customerid']);
			$obj['m_customer'][$i]['customerid_m']=(empty($obj['m_customer'][$i]['customerid_m'])?"":$obj['m_customer'][$i]['customerid_m']);
			$obj['m_customer'][$i]['jenis_saran']=(empty($obj['m_customer'][$i]['jenis_saran'])?"":$obj['m_customer'][$i]['jenis_saran']);
			$obj['m_customer'][$i]['deskripsi_saran']=(empty($obj['m_customer'][$i]['deskripsi_saran'])?"":$obj['m_customer'][$i]['deskripsi_saran']);
			$obj['m_customer'][$i]['mcc']=(empty($obj['m_customer'][$i]['mcc'])?"":$obj['m_customer'][$i]['mcc']);
			$obj['m_customer'][$i]['mnc']=(empty($obj['m_customer'][$i]['mnc'])?"":$obj['m_customer'][$i]['mnc']);
			$obj['m_customer'][$i]['lac']=(empty($obj['m_customer'][$i]['lac'])?"":$obj['m_customer'][$i]['lac']);
			$obj['m_customer'][$i]['cid']=(empty($obj['m_customer'][$i]['cid'])?"":$obj['m_customer'][$i]['cid']);
			$obj['m_customer'][$i]['spot_id']=(empty($obj['m_customer'][$i]['spot_id'])?"":$obj['m_customer'][$i]['spot_id']);
			                        
						
			$sqlcustins = 'REPLACE INTO m_customer_test (siteid,customerid_m,customerid,nama_customer,alamat,top_cust,tipe_bayar,kelurahanid,kecamatanid,
					kotaid,propinsiid,kodepos,telp,email,segmentid,typeid,classid,regionalid,areaid,subareaid,areakirimid,tipe_tax,createdate,latitude,
					longitude,saldo_piutang,saldo_overdue,limit_kredit,sisa_limit_kredit,nilai_sales,salesmanid,jenis_saran,deskripsi_saran,mcc,
					mnc,lac,cid,spot_id) 
					VALUES ("'.$obj['m_customer'][$i]['siteid'].'", 
							"'.$obj['m_customer'][$i]['customerid_m'].'", 
							"'.$obj['m_customer'][$i]['customerid'].'", 
							"'.mysql_real_escape_string($obj['m_customer'][$i]['nama_customer']).'", 
							"'.mysql_real_escape_string($obj['m_customer'][$i]['alamat']).'", 
							"'.$obj['m_customer'][$i]['top_cust'].'", 
							"'.$obj['m_customer'][$i]['tipe_bayar'].'", 
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
							"'.@$obj['m_customer'][$i]['areakirimid'].'", 
							"'.$obj['m_customer'][$i]['tipe_tax'].'", 
							"'.$tglsynch.'",
							"'.$obj['m_customer'][$i]['latitude'].'", 
							"'.$obj['m_customer'][$i]['longitude'].'", 
							"'.$obj['m_customer'][$i]['saldo_piutang'].'", 
							"'.$obj['m_customer'][$i]['saldo_overdue'].'", 
							"'.$obj['m_customer'][$i]['limit_kredit'].'", 
							"'.$obj['m_customer'][$i]['sisa_limit_kredit'].'", 
							"'.$obj['m_customer'][$i]['nilai_sales'].'", 
							"'.$obj['m_customer'][$i]['salesmanid'].'", 
							"'.$obj['m_customer'][$i]['jenis_saran'].'", 
							"'.mysql_real_escape_string($obj['m_customer'][$i]['deskripsi_saran']).'", 
							"'.$obj['m_customer'][$i]['mcc'].'", 
							"'.$obj['m_customer'][$i]['mnc'].'", 
							"'.$obj['m_customer'][$i]['lac'].'", 
							"'.$obj['m_customer'][$i]['cid'].'", 
							"'.$obj['m_customer'][$i]['spot_id'].'"
							);';
 
					if($conn->query($sqlcustins) === false) {
					  echo " Insert m_customer_test gagal "."Wrong SQL: " . $sqlcustins . ' Error: ' . $conn->error."\n";
					}
			}
		}
		
mysqli_close($conn);
print " Synchronize data success \n";

?>
