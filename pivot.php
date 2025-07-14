<?php

	 $host = 'localhost';
	 $uname = 'root';
	 $pwd = '12345678';
	 $db = 'dbckp';

	$json = file_get_contents('php://input');
	$obj = json_decode($json,true);
	
		$connhost = @mysqli_connect($host, $uname, $pwd, $db);
		if (!$connhost) {
			print ('{"respon":[{"status":"0","msg":"Koneksi ke database server local bermasalah"}]}');
		}else {
				$sql="SET SESSION group_concat_max_len = 1000000;";
				$exec = mysqli_query($connhost, $sql);
				$sql1="SELECT
						  GROUP_CONCAT(DISTINCT
							CONCAT(
							  'concat(''',cell_name,''',row_number,''|'',ifnull(SUM(case when cell_name = ''',cell_name, ''' then qty end),0)) AS `',cell_name, '`'
							)
						  ) INTO @sql1
						FROM
						  dbckp.sgs_rak_mapping_trx;
							";
				$exec1 = mysqli_query($connhost, $sql1);
				$sql2="SET @sql = CONCAT('SELECT id_gudang,id_lorong,id_rak,row_number as cell, ', @sql1, ' 
										  FROM sgs_rak_mapping_trx 
										   GROUP BY id_lorong,id_rak,id_gudang,row_number');";
				$exec2 = mysqli_query($connhost, $sql2);
				$sql3="PREPARE stmt FROM @sql;";
				$exec3 = mysqli_query($connhost, $sql3);
				$sql4="EXECUTE stmt;";
				$exec4 = mysqli_query($connhost, $sql4);
				$sql5="DEALLOCATE PREPARE stmt;";
				
				//$stringconn = mysqli_query($connhost, $sqlconn);
				$respons=mysqli_fetch_assoc($exec4);
				

		}
		echo json_encode($respons);

		mysqli_close($connhost);

?>