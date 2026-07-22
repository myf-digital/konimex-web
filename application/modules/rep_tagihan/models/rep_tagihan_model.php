<?php 
class Rep_tagihan_model extends CI_Model { 
	
	//get list data
	/*function get_list_data() {
		
		$param = $this->input->post(array('page', 'rows', 'sort', 'order', 'filterRules'));
        $offset = intval(($param['page'] - 1) * $param['rows']);		
		
		$sales = "";
		$start = "";
		$finish = "";
		
		$sales = $this->input->post("sales");
		$start = $this->input->post("start");
		$finish = $this->input->post("finish");
		
		//echo "start ".$start; die();
		
		if (count($param['filterRules']) > 0) {
            $filter = json_decode($param['filterRules']);
            $loop = 0;
            foreach ($filter as $json) {
                // convert to array
                $rule = get_object_vars($json);
                // declare variable
                $field = $rule['field'];
                $opt = $rule['op'];
                $value = $rule['value'];
                if ($loop == 0) {
                    if (!empty($value)) {
                        if ($opt == 'contains') {
                          	$this->db->like($field,$value);	
                        } else if ($opt == 'greater') {
                            $this->db->where('$field > ',$value);
                        } else if ($opt == 'less') {
                           	$this->db->where('$field < ',$value);
                        } else if ($opt == 'notequal') {
                           	$this->db->where('$field != ',$value);
                        } else if ($opt == 'equal') {
                           	$this->db->where($field,$value);
                        }
                        $loop++; // flag where
                    }
                } else {
                    if (!empty($value)) {
                        if ($opt == 'contains') {                            
							$this->db->like($field,$value);	
                        } else if ($opt == 'greater') {
							$this->db->where('$field > ',$value);
                        } else if ($opt == 'less') {
                            $this->db->where('$field < ',$value);
                        } else if ($opt == 'notequal') {
                            $this->db->where('$field != ',$value);
                        } else if ($opt == 'equal') {
                            $this->db->where($field,$value);
                        }
                    }
                }
            }
        }
		
		$response = array();	
		
		//set order
		if (!empty($param['sort'])) {
			$sort = $param['sort'];
			$order = $param['order'];			
			$this->db->order_by($sort,$order); 
		}		

		//query data
		$this->db->select("
			DATE_FORMAT(a.tgl_ink, '%d-%m-%Y') tgl_ink,
			DATE_FORMAT(a.tanggal, '%d-%m-%Y') tanggal,
			a.no_sales as no_sales,
			a.no_ink as no_ink,
			a.salesmanid as salesmanid,
			a.customerid as customerid,
			a.retur as retur,
			a.bayar as bayar,
			a.no_transfer as no_transfer,
			DATE_FORMAT(a.tgl_transfer, '%d-%m-%Y') tgl_transfer,
			a.no_giro as no_giro,
			DATE_FORMAT(a.tgl_jt_giro, '%d-%m-%Y') tgl_jt_giro,
			DATE_FORMAT(a.tgl_terima_giro, '%d-%m-%Y') tgl_terima_giro,
			a.AC_id_giro as AC_id_giro,
			a.status_giro as status_giro,
			a.bank_id as bank_id,
			a.bank_name as bank_name,
			a.bayar_tunai as bayar_tunai,
			a.bayar_transfer as bayar_transfer, 
			a.bayar_giro as bayar_giro,
			a.status_send as status_send,
			b.nama_salesman as nama_salesman	
			");	
			
		$this->db->from("t_ar_ink_detail a");		
		if ($sales != "") {
			$this->db->where("a.salesmanid",$sales);
		}
		
		if (($start != "") && ($finish != "")) {
			$this->db->where("a.tgl_ink between '".$start."' and '".$finish."'");
		}
		
		$this->db->join("m_sales_salesman b","a.salesmanid = b.salesmanid");
		$this->db->limit($param['rows'], $offset);
		
		$data = $this->db->get();
		
		//echo $this->db->last_query(); die();
		
		$this->db->select("
			a.no_sales	
			");	
			
		$this->db->from("t_ar_ink_detail a");		
		if ($sales != "") {
			$this->db->where("a.salesmanid",$sales);
		}
		
		if (($start != "") && ($finish != "")) {
			$this->db->where("a.tgl_ink between '".$start."' and '".$finish."'");
		}
		
		$this->db->join("m_sales_salesman b","a.salesmanid = b.salesmanid");
		
		$total = $this->db->get()->num_rows();
		
		$response['total'] = $total;
		$response['rows']  = $data->result();
		return $response;
    } */
	
	function get_siteid() {
		$this->db->select("siteid");
		$this->db->from("m_setup_site");
		$site = $this->db->get()->row()->siteid;
		return $site;
	}
	
	function get_data_export($periode) {
		
		$siteid = $this->get_siteid();
		$q = $this->db->query("select y.salesmanid,x.nama_salesman,count(1) as jadwal, IFNULL(x.InvalidCall,0) InvalidCall,IFNULL(x.ExtraCall,0) ExtraCall,
							IFNULL(x.cal,0) cal,IFNULL(x.effectivecall,0) effectivecall,IFNULL(x.noo,0) noo, IFNULL(x.amount,0) amount,
							CONCAT(ROUND(((IFNULL(x.cal,0)+IFNULL(x.effectivecall,0))/count(1))*100,1),' %') eff_time,
							CONCAT(ROUND((IFNULL(x.effectivecall,0)/count(1))*100,1),' %') eff_order
							from t_sales_rrk y 
								left join
									(select z.siteid,z.periode,z.salesmanid,z.nama_salesman, IFNULL(sum(z.INVCALL),0) as InvalidCall,IFNULL(sum(z.EXCALL),0) as ExtraCall,IFNULL(sum(z.CALL),0) as 'cal',IFNULL(sum(z.EFCALL),0) as effectivecall,
												   (select count(1) from m_customer where siteid=z.siteid and salesmanid=z.salesmanid and createdate=z.periode) noo,
												   (select sum(netto) from t_sales_master where salesmanid=z.salesmanid and tanggal=z.periode) as amount
											  from
											  (
												select d.siteid,d.periode,d.salesmanid,d.nama_salesman,
												  case when d.nama_colom = 'INVCALL' then d.jml end INVCALL,
												  case when d.nama_colom = 'EXCALL' then d.jml end EXCALL,
												  case when d.nama_colom = 'CALL' then d.jml end 'CALL',
												  case when d.nama_colom = 'EFCALL' then d.jml end EFCALL
												from
													 (
													select d.siteid,d.periode,d.salesmanid,d.nama_salesman,d.JML as nama_colom, count(d.JML) jml,d.perioderrk from (
													select a.siteid,a.periode,a.salesmanid,d.nama_salesman,b.customerid,e.customerid custid,b.periode perioderrk,c.no_sales,
														 case when b.customerid is null and c.no_sales is null then 'INVCALL' 
														  when b.customerid is null and c.no_sales is not null then 'EXCALL' 
														  when b.customerid is not null and c.no_sales is null then 'CALL'
														  when b.customerid is not null and c.no_sales is not null then 'EFCALL'
														  end JML
													from t_sales_rrk_trans a left join t_sales_rrk b on 
													a.siteid = b.siteid and a.salesmanid = b.salesmanid and a.customerid=b.customerid and a.periode=b.periode
													left join t_sales_master c on a.siteid = c.siteid and a.salesmanid = c.salesmanid and a.customerid=c.customerid and a.periode=c.tanggal
													left join m_sales_salesman d on a.siteid = d.siteid and a.salesmanid = d.salesmanid 
													left join m_customer e on a.customerid = e.customerid
													where a.siteid = '".$siteid."' 
													and a.periode='".$periode."'                    
													) d group by d.periode,d.salesmanid,d.nama_salesman,d.salesmanid,d.JML
												) d
											  ) z group by z.salesmanid
									) x on y.siteid=x.siteid and y.salesmanid=x.salesmanid and y.periode=x.periode
								where y.siteid = '".$siteid."' and y.periode='".$periode."' and x.nama_salesman is not null
								group by y.salesmanid,y.nama_salesman");
		$data = $q->result_array();
		return $data;
	}
	
	function get_list_data() {
		
		$siteid = $this->get_siteid(); 
		
		$periode = "";
		$periode = $this->input->post("start");
		
		//echo $periode; die();
		if ($periode != "") {
			$param = $this->input->post(array('page', 'rows', 'sort', 'order', 'filterRules'));
			$offset = intval(($param['page'] - 1) * $param['rows']);
			
			// clause filter, array not null
			$cond = '';
			if (count($param['filterRules']) > 0) {
				$filter = json_decode($param['filterRules']);
				$loop = 0;
				foreach ($filter as $json) {
					// convert to array
					$rule = get_object_vars($json);
					// declare variable
					$field = $rule['field'];
					$opt = $rule['op'];
					$value = $rule['value'];
					if ($loop == 0) {
						// user where
						if (!empty($value)) {
							if ($opt == 'contains') {
								$cond .= "where ($field like '%$value%')";
							} else if ($opt == 'greater') {
								$cond .= "where $field > '$value'";
							} else if ($opt == 'less') {
								$cond .= "where $field < '$value'";
							} else if ($opt == 'notequal') {
								$cond .= "where $field != '$value'";
							} else if ($opt == 'equal') {
								$cond .= "where $field = '$value'";
							}
							$loop++; // flag where
						}
					} else {
						// user and
						if (!empty($value)) {
							if ($opt == 'contains') {
								$cond .= " and ($field like '%$value%')";
							} else if ($opt == 'greater') {
								$cond .= " and $field > '$value'";
							} else if ($opt == 'less') {
								$cond .= " and $field < '$value'";
							} else if ($opt == 'notequal') {
								$cond .= " and $field != '$value'";
							} else if ($opt == 'equal') {
								$cond .= " and $field = '$value'";
							}
						}
					}
				}
			}
			
			$response = array();
			 //$table = 'adm_roles'; 
			
			if (empty($param['sort'])) {
				if (empty($cond)) {
					//$sql = "select role_id, role_name, role_status, created_by, created_date, modified_by, modified_date from $table  order by role_id limit ?, ?";
					$sql = "select y.salesmanid,sls.nama_salesman,count(1) as jadwal, IFNULL(x.InvalidCall,0) InvalidCall,IFNULL(x.ExtraCall,0) ExtraCall,
							IFNULL(x.cal,0) cal,IFNULL(x.effectivecall,0) effectivecall,IFNULL(x.noo,0) noo, IFNULL(x.amount,0) amount,
							CONCAT(ROUND(((IFNULL(x.cal,0)+IFNULL(x.effectivecall,0))/count(1))*100,1),' %') eff_time,
							CONCAT(ROUND((IFNULL(x.effectivecall,0)/count(1))*100,1),' %') eff_order
				   from t_sales_rrk y left join m_sales_salesman sls on y.siteid=sls.siteid and y.salesmanid=sls.salesmanid
			left join
			 (select z.siteid,z.periode,z.salesmanid,z.nama_salesman, IFNULL(sum(z.INVCALL),0) as InvalidCall,IFNULL(sum(z.EXCALL),0) as ExtraCall,IFNULL(sum(z.CALL),0) as 'cal',IFNULL(sum(z.EFCALL),0) as effectivecall,
                   (select count(1) from m_customer where siteid=z.siteid and salesmanid=z.salesmanid and createdate=z.periode) noo,
                   (select sum(netto) from t_sales_master where salesmanid=z.salesmanid and tanggal=z.periode) as amount
              from
              (
                select d.siteid,d.periode,d.salesmanid,d.nama_salesman,
                  case when d.nama_colom = 'INVCALL' then d.jml end INVCALL,
                  case when d.nama_colom = 'EXCALL' then d.jml end EXCALL,
                  case when d.nama_colom = 'CALL' then d.jml end 'CALL',
                  case when d.nama_colom = 'EFCALL' then d.jml end EFCALL
                from
                     (
                    select d.siteid,d.periode,d.salesmanid,d.nama_salesman,d.JML as nama_colom, count(d.JML) jml,d.perioderrk from (
                    select a.siteid,a.periode,a.salesmanid,d.nama_salesman,b.customerid,e.customerid custid,b.periode perioderrk,c.no_sales,
                         case when b.customerid is null and c.no_sales is null then 'INVCALL' 
                          when b.customerid is null and c.no_sales is not null then 'EXCALL' 
                          when b.customerid is not null and c.no_sales is null then 'CALL'
                          when b.customerid is not null and c.no_sales is not null then 'EFCALL'
                          end JML
                    from t_sales_rrk_trans a left join t_sales_rrk b on 
                    a.siteid = b.siteid and a.salesmanid = b.salesmanid and a.customerid=b.customerid and a.periode=b.periode
                    left join t_sales_master c on a.siteid = c.siteid and a.salesmanid = c.salesmanid and a.customerid=c.customerid and a.periode=c.tanggal
                    left join m_sales_salesman d on a.siteid = d.siteid and a.salesmanid = d.salesmanid 
					left join m_customer e on a.customerid = e.customerid
                    where a.siteid = '".$siteid."' 
                    and a.periode='".$periode."'                    
                    ) d group by d.periode,d.salesmanid,d.nama_salesman,d.salesmanid,d.JML
                ) d
              ) z group by z.salesmanid
 ) x on y.siteid=x.siteid and y.salesmanid=x.salesmanid and y.periode=x.periode
 where y.siteid = '".$siteid."' and y.periode='".$periode."' and sls.nama_salesman is not null
 group by y.salesmanid,y.nama_salesman	
						order by y.salesmanid limit ?, ?
					";
					$sqlcount = "select y.salesmanid,sls.nama_salesman,count(1) as jadwal, IFNULL(x.InvalidCall,0) InvalidCall,IFNULL(x.ExtraCall,0) ExtraCall,
       IFNULL(x.cal,0) cal,IFNULL(x.effectivecall,0) effectivecall,IFNULL(x.noo,0) noo, IFNULL(x.amount,0) amount
       from t_sales_rrk y left join m_sales_salesman sls on y.siteid=sls.siteid and y.salesmanid=sls.salesmanid
left join
 (select z.siteid,z.periode,z.salesmanid,z.nama_salesman, IFNULL(sum(z.INVCALL),0) as InvalidCall,IFNULL(sum(z.EXCALL),0) as ExtraCall,IFNULL(sum(z.CALL),0) as 'cal',IFNULL(sum(z.EFCALL),0) as effectivecall,
                   (select count(1) from m_customer where siteid=z.siteid and salesmanid=z.salesmanid and createdate=z.periode) noo,
                   (select sum(netto) from t_sales_master where salesmanid=z.salesmanid and tanggal=z.periode) as amount
              from
              (
                select d.siteid,d.periode,d.salesmanid,d.nama_salesman,
                  case when d.nama_colom = 'INVCALL' then d.jml end INVCALL,
                  case when d.nama_colom = 'EXCALL' then d.jml end EXCALL,
                  case when d.nama_colom = 'CALL' then d.jml end 'CALL',
                  case when d.nama_colom = 'EFCALL' then d.jml end EFCALL
                from
                     (
                    select d.siteid,d.periode,d.salesmanid,d.nama_salesman,d.JML as nama_colom, count(d.JML) jml,d.perioderrk from (
                    select a.siteid,a.periode,a.salesmanid,d.nama_salesman,b.customerid,e.customerid custid,b.periode perioderrk,c.no_sales,
                         case when b.customerid is null and c.no_sales is null then 'INVCALL' 
                          when b.customerid is null and c.no_sales is not null then 'EXCALL' 
                          when b.customerid is not null and c.no_sales is null then 'CALL'
                          when b.customerid is not null and c.no_sales is not null then 'EFCALL'
                          end JML
                    from t_sales_rrk_trans a left join t_sales_rrk b on 
                    a.siteid = b.siteid and a.salesmanid = b.salesmanid and a.customerid=b.customerid and a.periode=b.periode
                    left join t_sales_master c on a.siteid = c.siteid and a.salesmanid = c.salesmanid and a.customerid=c.customerid and a.periode=c.tanggal
                    left join m_sales_salesman d on a.siteid = d.siteid and a.salesmanid = d.salesmanid 
					left join m_customer e on a.customerid = e.customerid
                    where a.siteid = '".$siteid."' 
                    and a.periode='".$periode."'                    
                    ) d group by d.periode,d.salesmanid,d.nama_salesman,d.salesmanid,d.JML
                ) d
              ) z group by z.salesmanid
 ) x on y.siteid=x.siteid and y.salesmanid=x.salesmanid and y.periode=x.periode
 where y.siteid = '".$siteid."' and y.periode='".$periode."' and sls.nama_salesman is not null
 group by y.salesmanid,y.nama_salesman";
				} else {
					//$sql = "select role_id, role_name, role_status, created_by, created_date, modified_by, modified_date from $table  $cond order by role_id limit ?, ?";
					 $sql = "select y.salesmanid,sls.nama_salesman,count(1) as jadwal, IFNULL(x.InvalidCall,0) InvalidCall,IFNULL(x.ExtraCall,0) ExtraCall,
								IFNULL(x.cal,0) cal,IFNULL(x.effectivecall,0) effectivecall,IFNULL(x.noo,0) noo, IFNULL(x.amount,0) amount,
								CONCAT(ROUND(((IFNULL(x.cal,0)+IFNULL(x.effectivecall,0))/count(1))*100,1),' %') eff_time,
								CONCAT(ROUND((IFNULL(x.effectivecall,0)/count(1))*100,1),' %') eff_order
				   from t_sales_rrk y left join m_sales_salesman sls on y.siteid=sls.siteid and y.salesmanid=sls.salesmanid
			left join
			 (select z.siteid,z.periode,z.salesmanid,z.nama_salesman, IFNULL(sum(z.INVCALL),0) as InvalidCall,IFNULL(sum(z.EXCALL),0) as ExtraCall,IFNULL(sum(z.CALL),0) as 'cal',IFNULL(sum(z.EFCALL),0) as effectivecall,
                   (select count(1) from m_customer where siteid=z.siteid and salesmanid=z.salesmanid and createdate=z.periode) noo,
                   (select sum(netto) from t_sales_master where salesmanid=z.salesmanid and tanggal=z.periode) as amount
              from
              (
                select d.siteid,d.periode,d.salesmanid,d.nama_salesman,
                  case when d.nama_colom = 'INVCALL' then d.jml end INVCALL,
                  case when d.nama_colom = 'EXCALL' then d.jml end EXCALL,
                  case when d.nama_colom = 'CALL' then d.jml end 'CALL',
                  case when d.nama_colom = 'EFCALL' then d.jml end EFCALL
                from
                     (
                    select d.siteid,d.periode,d.salesmanid,d.nama_salesman,d.JML as nama_colom, count(d.JML) jml,d.perioderrk from (
                    select a.siteid,a.periode,a.salesmanid,d.nama_salesman,b.customerid,e.customerid custid,b.periode perioderrk,c.no_sales,
                         case when b.customerid is null and c.no_sales is null then 'INVCALL' 
                          when b.customerid is null and c.no_sales is not null then 'EXCALL' 
                          when b.customerid is not null and c.no_sales is null then 'CALL'
                          when b.customerid is not null and c.no_sales is not null then 'EFCALL'
                          end JML
                    from t_sales_rrk_trans a left join t_sales_rrk b on 
                    a.siteid = b.siteid and a.salesmanid = b.salesmanid and a.customerid=b.customerid and a.periode=b.periode
                    left join t_sales_master c on a.siteid = c.siteid and a.salesmanid = c.salesmanid and a.customerid=c.customerid and a.periode=c.tanggal
                    left join m_sales_salesman d on a.siteid = d.siteid and a.salesmanid = d.salesmanid 
					left join m_customer e on a.customerid = e.customerid
                    where a.siteid = '".$siteid."' 
                    and a.periode='".$periode."'                    
                    ) d group by d.periode,d.salesmanid,d.nama_salesman,d.salesmanid,d.JML
                ) d
              ) z group by z.salesmanid
 ) x on y.siteid=x.siteid and y.salesmanid=x.salesmanid and y.periode=x.periode
 where y.siteid = '".$siteid."' and y.periode='".$periode."' and sls.nama_salesman is not null
 group by y.salesmanid,y.nama_salesman	
						$cond order by y.salesmanid limit ?, ?
					";
					$sqlcount = "select y.salesmanid,sls.nama_salesman,count(1) as jadwal, IFNULL(x.InvalidCall,0) InvalidCall,IFNULL(x.ExtraCall,0) ExtraCall,
       IFNULL(x.cal,0) cal,IFNULL(x.effectivecall,0) effectivecall,IFNULL(x.noo,0) noo
       from t_sales_rrk y left join m_sales_salesman sls on y.siteid=sls.siteid and y.salesmanid=sls.salesmanid
left join
 (select z.siteid,z.periode,z.salesmanid,z.nama_salesman, IFNULL(sum(z.INVCALL),0) as InvalidCall,IFNULL(sum(z.EXCALL),0) as ExtraCall,IFNULL(sum(z.CALL),0) as 'cal',IFNULL(sum(z.EFCALL),0) as effectivecall,
                   (select count(1) from m_customer where siteid=z.siteid and salesmanid=z.salesmanid and createdate=z.periode) noo,
                   (select sum(netto) from t_sales_master where salesmanid=z.salesmanid and tanggal=z.periode) as amount
              from
              (
                select d.siteid,d.periode,d.salesmanid,d.nama_salesman,
                  case when d.nama_colom = 'INVCALL' then d.jml end INVCALL,
                  case when d.nama_colom = 'EXCALL' then d.jml end EXCALL,
                  case when d.nama_colom = 'CALL' then d.jml end 'CALL',
                  case when d.nama_colom = 'EFCALL' then d.jml end EFCALL
                from
                     (
                    select d.siteid,d.periode,d.salesmanid,d.nama_salesman,d.JML as nama_colom, count(d.JML) jml,d.perioderrk from (
                    select a.siteid,a.periode,a.salesmanid,d.nama_salesman,b.customerid,e.customerid custid,b.periode perioderrk,c.no_sales,
                         case when b.customerid is null and c.no_sales is null then 'INVCALL' 
                          when b.customerid is null and c.no_sales is not null then 'EXCALL' 
                          when b.customerid is not null and c.no_sales is null then 'CALL'
                          when b.customerid is not null and c.no_sales is not null then 'EFCALL'
                          end JML
                    from t_sales_rrk_trans a left join t_sales_rrk b on 
                    a.siteid = b.siteid and a.salesmanid = b.salesmanid and a.customerid=b.customerid and a.periode=b.periode
                    left join t_sales_master c on a.siteid = c.siteid and a.salesmanid = c.salesmanid and a.customerid=c.customerid and a.periode=c.tanggal
                    left join m_sales_salesman d on a.siteid = d.siteid and a.salesmanid = d.salesmanid 
					left join m_customer e on a.customerid = e.customerid
                    where a.siteid = '".$siteid."' 
                    and a.periode='".$periode."'                    
                    ) d group by d.periode,d.salesmanid,d.nama_salesman,d.salesmanid,d.JML
                ) d
              ) z group by z.salesmanid
 ) x on y.siteid=x.siteid and y.salesmanid=x.salesmanid and y.periode=x.periode
 where y.siteid = '".$siteid."' and y.periode='".$periode."' and sls.nama_salesman is not null
 group by y.salesmanid,y.nama_salesman	
						$cond order by y.salesmanid";
				}
				$result_array = $this->db->query($sql, array($offset, intval($param['rows'])));
				$response['total'] = $this->db->query($sqlcount)->num_rows();
				$response['rows'] = $result_array->result();
			} else {
				$sort = $param['sort'];
				$order = $param['order'];
				if (empty($cond)) {
					//$sql = "select role_id, role_name, role_status, created_by, created_date, modified_by, modified_date from $table order by $sort $order limit ?, ?";
					$sql = "select y.salesmanid,sls.nama_salesman,count(1) as jadwal, IFNULL(x.InvalidCall,0) InvalidCall,IFNULL(x.ExtraCall,0) ExtraCall,
							IFNULL(x.cal,0) cal,IFNULL(x.effectivecall,0) effectivecall,IFNULL(x.noo,0) noo, IFNULL(x.amount,0) amount,
							CONCAT(ROUND(((IFNULL(x.cal,0)+IFNULL(x.effectivecall,0))/count(1))*100,1),' %') eff_time,
							CONCAT(ROUND((IFNULL(x.effectivecall,0)/count(1))*100,1),' %') eff_order
				   from t_sales_rrk y left join m_sales_salesman sls on y.siteid=sls.siteid and y.salesmanid=sls.salesmanid
			left join
			 (select z.siteid,z.periode,z.salesmanid,z.nama_salesman, IFNULL(sum(z.INVCALL),0) as InvalidCall,IFNULL(sum(z.EXCALL),0) as ExtraCall,IFNULL(sum(z.CALL),0) as 'cal',IFNULL(sum(z.EFCALL),0) as effectivecall,
                   (select count(1) from m_customer where siteid=z.siteid and salesmanid=z.salesmanid and createdate=z.periode) noo,
                   (select sum(netto) from t_sales_master where salesmanid=z.salesmanid and tanggal=z.periode) as amount
              from
              (
                select d.siteid,d.periode,d.salesmanid,d.nama_salesman,
                  case when d.nama_colom = 'INVCALL' then d.jml end INVCALL,
                  case when d.nama_colom = 'EXCALL' then d.jml end EXCALL,
                  case when d.nama_colom = 'CALL' then d.jml end 'CALL',
                  case when d.nama_colom = 'EFCALL' then d.jml end EFCALL
                from
                     (
                    select d.siteid,d.periode,d.salesmanid,d.nama_salesman,d.JML as nama_colom, count(d.JML) jml,d.perioderrk from (
                    select a.siteid,a.periode,a.salesmanid,d.nama_salesman,b.customerid,e.customerid custid,b.periode perioderrk,c.no_sales,
                         case when b.customerid is null and c.no_sales is null then 'INVCALL' 
                          when b.customerid is null and c.no_sales is not null then 'EXCALL' 
                          when b.customerid is not null and c.no_sales is null then 'CALL'
                          when b.customerid is not null and c.no_sales is not null then 'EFCALL'
                          end JML
                    from t_sales_rrk_trans a left join t_sales_rrk b on 
                    a.siteid = b.siteid and a.salesmanid = b.salesmanid and a.customerid=b.customerid and a.periode=b.periode
                    left join t_sales_master c on a.siteid = c.siteid and a.salesmanid = c.salesmanid and a.customerid=c.customerid and a.periode=c.tanggal
                    left join m_sales_salesman d on a.siteid = d.siteid and a.salesmanid = d.salesmanid 
					left join m_customer e on a.customerid = e.customerid
                    where a.siteid = '".$siteid."' 
                    and a.periode='".$periode."'                    
                    ) d group by d.periode,d.salesmanid,d.nama_salesman,d.salesmanid,d.JML
                ) d
              ) z group by z.salesmanid
 ) x on y.siteid=x.siteid and y.salesmanid=x.salesmanid and y.periode=x.periode
 where y.siteid = '".$siteid."' and y.periode='".$periode."' and sls.nama_salesman is not null
 group by y.salesmanid,y.nama_salesman	
						$cond order by $sort $order limit ?, ?";
					 $sqlcount = "select y.salesmanid,sls.nama_salesman,count(1) as jadwal, IFNULL(x.InvalidCall,0) InvalidCall,IFNULL(x.ExtraCall,0) ExtraCall,
       IFNULL(x.cal,0) cal,IFNULL(x.effectivecall,0) effectivecall,IFNULL(x.noo,0) noo
       from t_sales_rrk y left join m_sales_salesman sls on y.siteid=sls.siteid and y.salesmanid=sls.salesmanid
left join
 (select z.siteid,z.periode,z.salesmanid,z.nama_salesman, IFNULL(sum(z.INVCALL),0) as InvalidCall,IFNULL(sum(z.EXCALL),0) as ExtraCall,IFNULL(sum(z.CALL),0) as 'cal',IFNULL(sum(z.EFCALL),0) as effectivecall,
                   (select count(1) from m_customer where siteid=z.siteid and salesmanid=z.salesmanid and createdate=z.periode) noo,
                   (select sum(netto) from t_sales_master where salesmanid=z.salesmanid and tanggal=z.periode) as amount
              from
              (
                select d.siteid,d.periode,d.salesmanid,d.nama_salesman,
                  case when d.nama_colom = 'INVCALL' then d.jml end INVCALL,
                  case when d.nama_colom = 'EXCALL' then d.jml end EXCALL,
                  case when d.nama_colom = 'CALL' then d.jml end 'CALL',
                  case when d.nama_colom = 'EFCALL' then d.jml end EFCALL
                from
                     (
                    select d.siteid,d.periode,d.salesmanid,d.nama_salesman,d.JML as nama_colom, count(d.JML) jml,d.perioderrk from (
                    select a.siteid,a.periode,a.salesmanid,d.nama_salesman,b.customerid,e.customerid custid,b.periode perioderrk,c.no_sales,
                         case when b.customerid is null and c.no_sales is null then 'INVCALL' 
                          when b.customerid is null and c.no_sales is not null then 'EXCALL' 
                          when b.customerid is not null and c.no_sales is null then 'CALL'
                          when b.customerid is not null and c.no_sales is not null then 'EFCALL'
                          end JML
                    from t_sales_rrk_trans a left join t_sales_rrk b on 
                    a.siteid = b.siteid and a.salesmanid = b.salesmanid and a.customerid=b.customerid and a.periode=b.periode
                    left join t_sales_master c on a.siteid = c.siteid and a.salesmanid = c.salesmanid and a.customerid=c.customerid and a.periode=c.tanggal
                    left join m_sales_salesman d on a.siteid = d.siteid and a.salesmanid = d.salesmanid 
					left join m_customer e on a.customerid = e.customerid
                    where a.siteid = '".$siteid."' 
                    and a.periode='".$periode."'                    
                    ) d group by d.periode,d.salesmanid,d.nama_salesman,d.salesmanid,d.JML
                ) d
              ) z group by z.salesmanid
 ) x on y.siteid=x.siteid and y.salesmanid=x.salesmanid and y.periode=x.periode
 where y.siteid = '".$siteid."' and y.periode='".$periode."' and sls.nama_salesman is not null
 group by y.salesmanid,y.nama_salesman	
						";
				} else {
					//$sql = "select role_id, role_name, role_status, created_by, created_date, modified_by, modified_date from $table  $cond order by $sort $order limit ?, ?";
					$sql = "
						select y.salesmanid,sls.nama_salesman,count(1) as jadwal, IFNULL(x.InvalidCall,0) InvalidCall,IFNULL(x.ExtraCall,0) ExtraCall,
							IFNULL(x.cal,0) cal,IFNULL(x.effectivecall,0) effectivecall,IFNULL(x.noo,0) noo , IFNULL(x.amount,0) amount,
							CONCAT(ROUND(((IFNULL(x.cal,0)+IFNULL(x.effectivecall,0))/count(1))*100,1),' %') eff_time,
							CONCAT(ROUND((IFNULL(x.effectivecall,0)/count(1))*100,1),' %') eff_order
				   from t_sales_rrk y left join m_sales_salesman sls on y.siteid=sls.siteid and y.salesmanid=sls.salesmanid
			left join
			 (select z.siteid,z.periode,z.salesmanid,z.nama_salesman, IFNULL(sum(z.INVCALL),0) as InvalidCall,IFNULL(sum(z.EXCALL),0) as ExtraCall,IFNULL(sum(z.CALL),0) as 'cal',IFNULL(sum(z.EFCALL),0) as effectivecall,
                   (select count(1) from m_customer where siteid=z.siteid and salesmanid=z.salesmanid and createdate=z.periode) noo,
                   (select sum(netto) from t_sales_master where salesmanid=z.salesmanid and tanggal=z.periode) as amount
              from
              (
                select d.siteid,d.periode,d.salesmanid,d.nama_salesman,
                  case when d.nama_colom = 'INVCALL' then d.jml end INVCALL,
                  case when d.nama_colom = 'EXCALL' then d.jml end EXCALL,
                  case when d.nama_colom = 'CALL' then d.jml end 'CALL',
                  case when d.nama_colom = 'EFCALL' then d.jml end EFCALL
                from
                     (
                    select d.siteid,d.periode,d.salesmanid,d.nama_salesman,d.JML as nama_colom, count(d.JML) jml,d.perioderrk from (
                    select a.siteid,a.periode,a.salesmanid,d.nama_salesman,b.customerid,e.customerid custid,b.periode perioderrk,c.no_sales,
                         case when b.customerid is null and c.no_sales is null then 'INVCALL' 
                          when b.customerid is null and c.no_sales is not null then 'EXCALL' 
                          when b.customerid is not null and c.no_sales is null then 'CALL'
                          when b.customerid is not null and c.no_sales is not null then 'EFCALL'
                          end JML
                    from t_sales_rrk_trans a left join t_sales_rrk b on 
                    a.siteid = b.siteid and a.salesmanid = b.salesmanid and a.customerid=b.customerid and a.periode=b.periode
                    left join t_sales_master c on a.siteid = c.siteid and a.salesmanid = c.salesmanid and a.customerid=c.customerid and a.periode=c.tanggal
                    left join m_sales_salesman d on a.siteid = d.siteid and a.salesmanid = d.salesmanid 
					left join m_customer e on a.customerid = e.customerid
                    where a.siteid = '".$siteid."' 
                    and a.periode='".$periode."'                    
                    ) d group by d.periode,d.salesmanid,d.nama_salesman,d.salesmanid,d.JML
                ) d
              ) z group by z.salesmanid
 ) x on y.siteid=x.siteid and y.salesmanid=x.salesmanid and y.periode=x.periode
 where y.siteid = '".$siteid."' and y.periode='".$periode."' and sls.nama_salesman is not null
 group by y.salesmanid,y.nama_salesman	
						$cond order by $sort limit ?, ?
					";
					$sqlcount = "select y.salesmanid,sls.nama_salesman,count(1) as jadwal, IFNULL(x.InvalidCall,0) InvalidCall,IFNULL(x.ExtraCall,0) ExtraCall,
       IFNULL(x.cal,0) cal,IFNULL(x.effectivecall,0) effectivecall,IFNULL(x.noo,0) noo, IFNULL(x.amount,0) amount
       from t_sales_rrk y left join m_sales_salesman sls on y.siteid=sls.siteid and y.salesmanid=sls.salesmanid
left join
 (select z.siteid,z.periode,z.salesmanid,z.nama_salesman, IFNULL(sum(z.INVCALL),0) as InvalidCall,IFNULL(sum(z.EXCALL),0) as ExtraCall,IFNULL(sum(z.CALL),0) as 'cal',IFNULL(sum(z.EFCALL),0) as effectivecall,
                   (select count(1) from m_customer where siteid=z.siteid and salesmanid=z.salesmanid and createdate=z.periode) noo,
                   (select sum(netto) from t_sales_master where salesmanid=z.salesmanid and tanggal=z.periode) as amount
              from
              (
                select d.siteid,d.periode,d.salesmanid,d.nama_salesman,
                  case when d.nama_colom = 'INVCALL' then d.jml end INVCALL,
                  case when d.nama_colom = 'EXCALL' then d.jml end EXCALL,
                  case when d.nama_colom = 'CALL' then d.jml end 'CALL',
                  case when d.nama_colom = 'EFCALL' then d.jml end EFCALL
                from
                     (
                    select d.siteid,d.periode,d.salesmanid,d.nama_salesman,d.JML as nama_colom, count(d.JML) jml,d.perioderrk from (
                    select a.siteid,a.periode,a.salesmanid,d.nama_salesman,b.customerid,e.customerid custid,b.periode perioderrk,c.no_sales,
                         case when b.customerid is null and c.no_sales is null then 'INVCALL' 
                          when b.customerid is null and c.no_sales is not null then 'EXCALL' 
                          when b.customerid is not null and c.no_sales is null then 'CALL'
                          when b.customerid is not null and c.no_sales is not null then 'EFCALL'
                          end JML
                    from t_sales_rrk_trans a left join t_sales_rrk b on 
                    a.siteid = b.siteid and a.salesmanid = b.salesmanid and a.customerid=b.customerid and a.periode=b.periode
                    left join t_sales_master c on a.siteid = c.siteid and a.salesmanid = c.salesmanid and a.customerid=c.customerid and a.periode=c.tanggal
                    left join m_sales_salesman d on a.siteid = d.siteid and a.salesmanid = d.salesmanid 
					left join m_customer e on a.customerid = e.customerid
                    where a.siteid = '".$siteid."' 
                    and a.periode='".$periode."'                    
                    ) d group by d.periode,d.salesmanid,d.nama_salesman,d.salesmanid,d.JML
                ) d
              ) z group by z.salesmanid
 ) x on y.siteid=x.siteid and y.salesmanid=x.salesmanid and y.periode=x.periode
 where y.siteid = '".$siteid."' and y.periode='".$periode."' and sls.nama_salesman is not null
 group by y.salesmanid,y.nama_salesman	
						$cond ";
				}
				
				
				$result_array = $this->db->query($sql, array($offset, intval($param['rows'])));
				$response['total'] = $this->db->query($sqlcount)->num_rows();
				$response['rows'] = $result_array->result();
			}
			return $response;
		} else {
			$response['total'] = "";
			$response['rows']  = "";
			return $response;
		}
		
	}
	
	function get_salesman() {
		
		$this->db->select("nama_salesman,salesmanid");
		$this->db->from("m_sales_salesman");
		$data = $this->db->get()->result_array();
		
		$return   ='<select name="salesmanid" id="salesmanid" class="chosen-select">';
		$return .='<option value="">--TPE--</option>';
		foreach ($data as $value) {
			$return .='<option value="'.$value['salesmanid'].'">'.$value['nama_salesman'].'</option>';
		} 		
		$return .= '</select>';		
		return $return;
	}
	
		
	
	
}