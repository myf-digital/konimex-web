<?php 
class Syn_model extends CI_Model {
	
	function get_data_salesman($siteid) {

		$sql = " (select a.salesmanid,a.nama_salesman, sum(ifnull(b.netto,0)) total_order 
						from m_sales_salesman a left join t_sales_master b on a.salesmanid=b.salesmanid and b.tanggal=(select tanggal from m_setup_site)
						where a.siteid='".$siteid."' and a.aktif=1
						group by a.salesmanid,a.nama_salesman
				  ) as salesmanorder" ;
		$this->db->select();
		$this->db->from($sql);
		
		$return = $this->db->get()->result_array();
		return $return;
	}

	function get_data_list_order($siteid,$salesmanid) {
		
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$domainName = $_SERVER['HTTP_HOST'] . '/';
		
		$ip = base_url();
		$sql = " (
				select  z.salesmanid, z.nama_salesman, z.customerid, z.nama_customer, z.tanggal, z.saldo_piutang,
						z.saldo_overdue, z.limit_kredit, z.sisa_limit_kredit, sum(z.total_order) total_order, sum(z.bayar_utang) bayar_utang,
						DATE_FORMAT(x.check_in, '%H:%i:%s') check_in, DATE_FORMAT(x.check_out, '%H:%i:%s') check_out,
						timediff(DATE_FORMAT(x.check_out, '%H:%i:%s'),DATE_FORMAT(x.check_in, '%H:%i:%s')) lama_kunjungan,
						concat('".$ip.'uploads/'."',ifnull(y.image,'noimage.jpg')) dir_image, status_approve
				from (
					(select a.siteid, a.salesmanid,a.nama_salesman, c.customerid, c.nama_customer, b.tanggal, c.saldo_piutang,
								 c.saldo_overdue, c.limit_kredit, c.sisa_limit_kredit, 
								 sum(ifnull(b.netto,0)) total_order, 0 bayar_utang,
								 (select count(1) from t_sales_master 
								 where siteid='".$siteid."' and salesmanid='".$salesmanid."' 
								 and tanggal=(select tanggal from m_setup_site) and status_send=0 and customerid=b.customerid) status_approve
					from t_sales_master b join m_sales_salesman a on b.salesmanid=a.salesmanid 
								join m_customer c on b.salesmanid=c.salesmanid and b.customerid=c.customerid
					where a.siteid='".$siteid."' and b.salesmanid='".$salesmanid."' and b.tanggal=(select tanggal from m_setup_site)
					group by a.salesmanid, a.nama_salesman, b.customerid )
					union all
					(select a.siteid, a.salesmanid,a.nama_salesman, c.customerid, c.nama_customer, b.tgl_ink tanggal, c.saldo_piutang,
								 c.saldo_overdue, c.limit_kredit, c.sisa_limit_kredit, 
								 0 total_order, sum((ifnull(b.bayar_tunai,0)+ifnull(b.bayar_transfer,0)+ifnull(b.bayar_giro,0))) bayar_utang,
								 0 status_approve
					from t_ar_ink_detail b join m_sales_salesman a on b.salesmanid=a.salesmanid
								join m_customer c on b.salesmanid=c.salesmanid and b.customerid=c.customerid
					where a.siteid='".$siteid."' and b.salesmanid='".$salesmanid."' and b.tgl_ink=(select tanggal from m_setup_site)
					group by a.salesmanid, a.nama_salesman, b.customerid )
					) z left join t_sales_rrk_trans x on z.customerid=x.customerid and z.siteid=x.siteid 
						and z.salesmanid=x.salesmanid and z.tanggal=x.periode
					left join m_customer_image y on y.id=(select p.id from m_customer_image p
							  where p.customerid=z.customerid and p.salesmanid=z.salesmanid order by p.id desc limit 1)
					where z.total_order > 0 or z.bayar_utang > 0
				group by z.salesmanid, z.nama_salesman, z.customerid 
				order by check_in desc
				) as salesmanorder ";
	
		$this->db->select();
		$this->db->from($sql);
		
		$return = $this->db->get()->result_array();
		return $return;
	}	
	
	function get_data_list_order_customer($siteid,$salesmanid,$customerid) {
		
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$domainName = $_SERVER['HTTP_HOST'] . '/';
		
		$ip = base_url();

		
		/*$sql_old = " (select  z.salesmanid, z.nama_salesman, z.customerid, z.nama_customer, z.tanggal, z.saldo_piutang,
						 z.saldo_overdue, z.limit_kredit, z.sisa_limit_kredit, z.no_sales, sum(z.total_order) total_order
						 , z.status_approve
					from (
						select a.salesmanid,a.nama_salesman, c.customerid, c.nama_customer, b.tanggal, c.saldo_piutang,
							   c.saldo_overdue, c.limit_kredit, c.sisa_limit_kredit, 
								b.no_sales, sum(ifnull(b.netto,0)) total_order,
								case when b.status_send='0' or b.status_send is null then '0' else '1' end status_approve
						from t_sales_master b join m_sales_salesman a on b.salesmanid=a.salesmanid
								 join m_customer c on b.salesmanid=c.salesmanid and b.customerid=c.customerid
						where a.siteid='".$siteid."' and b.salesmanid='".$salesmanid."'  and b.customerid='".$customerid."' 
						and b.tanggal=(select tanggal from m_setup_site)
						group by a.salesmanid,a.nama_salesman, b.customerid, b.no_sales
						) z
					group by z.salesmanid, z.nama_salesman, z.customerid, z.no_sales
				  ) as salesmanorder
				  " ;*/
		$sql = " (select  z.salesmanid, z.nama_salesman, z.customerid, z.nama_customer, z.tanggal, z.saldo_piutang,
						  z.saldo_overdue, z.limit_kredit, z.sisa_limit_kredit, z.no_sales, sum(z.total_order) total_order, z.status_approve,
						DATE_FORMAT(y.check_in, '%H:%i:%s') check_in, DATE_FORMAT(y.check_out, '%H:%i:%s') check_out,
						timediff(DATE_FORMAT(y.check_out, '%H:%i:%s'),DATE_FORMAT(y.check_in, '%H:%i:%s')) lama_kunjungan
					from (
						select a.siteid, a.salesmanid,a.nama_salesman, c.customerid, c.nama_customer, b.tanggal, c.saldo_piutang,
							   c.saldo_overdue, c.limit_kredit, c.sisa_limit_kredit, 
								b.no_sales, sum(ifnull(b.netto,0)) total_order,
								case when b.status_send='0' or b.status_send is null then '0' else '1' end status_approve
						from t_sales_master b join m_sales_salesman a on b.salesmanid=a.salesmanid
								 join m_customer c on b.salesmanid=c.salesmanid and b.customerid=c.customerid
						where a.siteid='".$siteid."' and b.salesmanid='".$salesmanid."'  and b.customerid='".$customerid."'
						and b.tanggal=(select tanggal from m_setup_site)
						group by a.salesmanid,a.nama_salesman, b.customerid, b.no_sales
						) z left join t_sales_rrk_trans y on z.customerid=y.customerid and z.siteid=y.siteid 
						and z.salesmanid=y.salesmanid and z.tanggal=y.periode	
					group by y.check_in, z.no_sales
					order by y.check_in asc
				  ) as salesmanorder
				  " ;
				  
		$this->db->select();
		$this->db->from($sql);
		
		$return = $this->db->get()->result_array();
		return $return;
	}	
	
	function get_data_view_detail($siteid,$salesmanid,$customerid,$no_sales) {

		$sql = " (select  z.salesmanid, z.nama_salesman, z.customerid, z.nama_customer, z.tanggal, z.productid,
							z.nama_invoice, sum(z.qty) qty, sum(z.netto) total_order
									 from (
					select a.salesmanid,a.nama_salesman, c.customerid, c.nama_customer, b.tanggal, d.productid, e.nama_invoice,
								 b.no_sales, sum(ifnull(d.qty_kecil,0)) qty,sum(ifnull(d.netto,0)) netto 
					from t_sales_master b left join m_sales_salesman a on b.salesmanid=a.salesmanid
							 left join m_customer c on b.customerid=c.customerid
							 left join t_sales_detail d on b.no_sales=d.no_sales
							 left join m_product e on d.productid=e.productid
					where a.siteid='".$siteid."' and b.salesmanid='".$salesmanid."'  and b.customerid='".$customerid."' 
							and b.tanggal=(select tanggal from m_setup_site) and d.no_sales='".$no_sales."'
					group by a.salesmanid,a.nama_salesman, c.customerid, c.nama_customer, b.tanggal, d.productid, e.nama_invoice,b.no_sales) z
					group by z.salesmanid, z.nama_salesman, z.customerid, z.nama_customer, z.tanggal, z.productid, z.nama_invoice
				  ) as salesmanorder
				  " ;
		$this->db->select();
		$this->db->from($sql);
		
		$return = $this->db->get()->result_array();
		return $return;
	}	
	
	function get_approved($spvid,$no_sales) {

		$data['status_send'] = 1;//$spvid;
		$this->db->where("no_sales",$no_sales);
		$q = $this->db->update("t_sales_master",$data);
		if ($q){$return=1;}else{$return=0;}
		return $return;
	
	}
	
	function get_data_productivity($siteid) {

		$sql = " (select b.salesmanid,b.nama_salesman, 
						   sum(CASE WHEN a.periode >= last_day(now()) + interval 1 day - interval 4 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 4 month) THEN IFNULL(a.sales,0) ELSE 0 END) as sales1, 
						   sum(CASE WHEN a.periode >= last_day(now()) + interval 1 day - interval 3 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 3 month) THEN IFNULL(a.sales,0) ELSE 0 END) as sales2, 
						   sum(CASE WHEN a.periode >= last_day(now()) + interval 1 day - interval 2 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 2 month) THEN IFNULL(a.sales,0) ELSE 0 END) as sales3,  
						   sum(CASE WHEN a.periode >= last_day(now()) + interval 1 day - interval 1 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 1 month) THEN IFNULL(a.sales,0) ELSE 0 END) as sales4, 
						   sum(CASE WHEN a.periode >= last_day(now()) + interval 1 day - interval 4 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 4 month) THEN IFNULL(a.ob,0) ELSE 0 END) as ob1, 
						   sum(CASE WHEN a.periode >= last_day(now()) + interval 1 day - interval 3 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 3 month) THEN IFNULL(a.ob,0) ELSE 0 END) as ob2, 
						   sum(CASE WHEN a.periode >= last_day(now()) + interval 1 day - interval 2 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 2 month) THEN IFNULL(a.ob,0) ELSE 0 END) as ob3,  
						   sum(CASE WHEN a.periode >= last_day(now()) + interval 1 day - interval 1 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 1 month) THEN IFNULL(a.ob,0) ELSE 0 END) as ob4, 
						   sum(CASE WHEN a.periode >= last_day(now()) + interval 1 day - interval 4 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 4 month) THEN IFNULL(a.oa,0) ELSE 0 END) as ot1, 
						   sum(CASE WHEN a.periode >= last_day(now()) + interval 1 day - interval 3 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 3 month) THEN IFNULL(a.oa,0) ELSE 0 END) as ot2, 
						   sum(CASE WHEN a.periode >= last_day(now()) + interval 1 day - interval 2 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 2 month) THEN IFNULL(a.oa,0) ELSE 0 END) as ot3,  
						   sum(CASE WHEN a.periode >= last_day(now()) + interval 1 day - interval 1 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 1 month) THEN IFNULL(a.oa,0) ELSE 0 END) as ot4, 
						   sum(CASE WHEN a.periode >= last_day(now()) + interval 1 day - interval 4 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 4 month) THEN IFNULL(a.ec,0) ELSE 0 END) as ec1, 
						   sum(CASE WHEN a.periode >= last_day(now()) + interval 1 day - interval 3 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 3 month) THEN IFNULL(a.ec,0) ELSE 0 END) as ec2, 
						   sum(CASE WHEN a.periode >= last_day(now()) + interval 1 day - interval 2 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 2 month) THEN IFNULL(a.ec,0) ELSE 0 END) as ec3,  
						   sum(CASE WHEN a.periode >= last_day(now()) + interval 1 day - interval 1 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 1 month) THEN IFNULL(a.ec,0) ELSE 0 END) as ec4,
						   MONTH(last_day(now()) + interval 1 day - interval 4 month) start_month, 
						   YEAR(last_day(now()) + interval 1 day - interval 4 month) start_year
					from  t_productivity a INNER JOIN m_sales_salesman b on a.siteid = b.siteid and a.salesmanid = b.salesmanid 
					where a.siteid = '".$siteid."' AND b.aktif = 1 
					group by b.salesmanid,b.nama_salesman
				  ) as productivity
				  " ;
		$this->db->select();
		$this->db->from($sql);
		
		$return = $this->db->get()->result_array();
		return $return;
	}	
	
	function get_data_target_per_prinsipal($siteid) {

		$sql = " (select b.salesmanid,b.nama_salesman,a.prinsipalid, a.nama_prinsipal, 
						   sum(CASE WHEN periode >= last_day(now()) + interval 1 day - interval 4 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 4 month) THEN IFNULL(a.sales,0) ELSE 0 END) as sales1, 
						   sum(CASE WHEN periode >= last_day(now()) + interval 1 day - interval 3 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 3 month) THEN IFNULL(a.sales,0) ELSE 0 END) as sales2, 
						   sum(CASE WHEN periode >= last_day(now()) + interval 1 day - interval 2 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 2 month) THEN IFNULL(a.sales,0) ELSE 0 END) as sales3,  
						   sum(CASE WHEN periode >= last_day(now()) + interval 1 day - interval 1 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 1 month) THEN IFNULL(a.sales,0) ELSE 0 END) as sales4, 
						   sum(CASE WHEN periode >= last_day(now()) + interval 1 day - interval 4 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 4 month) THEN IFNULL(a.target,0) ELSE 0 END) as target1, 
						   sum(CASE WHEN periode >= last_day(now()) + interval 1 day - interval 3 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 3 month) THEN IFNULL(a.target,0) ELSE 0 END) as target2, 
						   sum(CASE WHEN periode >= last_day(now()) + interval 1 day - interval 2 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 2 month) THEN IFNULL(a.target,0) ELSE 0 END) as target3,  
						   sum(CASE WHEN periode >= last_day(now()) + interval 1 day - interval 1 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 1 month) THEN IFNULL(a.target,0) ELSE 0 END) as target4,
						   MONTH(last_day(now()) + interval 1 day - interval 4 month) start_month, 
						   YEAR(last_day(now()) + interval 1 day - interval 4 month) start_year
					from t_target_prinsipal_salesman a INNER JOIN m_sales_salesman b on a.siteid = b.siteid and a.salesmanid = b.salesmanid 
					where a.siteid = '".$siteid."' AND b.aktif = 1
					group by b.salesmanid,b.nama_salesman,a.prinsipalid, a.nama_prinsipal
				  ) as target_per_prinsipal
				  " ;
		$this->db->select();
		$this->db->from($sql);
		
		$return = $this->db->get()->result_array();
		return $return;
	}	

	function get_data_target_per_group($siteid) {

		$sql = " (select b.salesmanid,b.nama_salesman,a.groupid, a.nama_group, 
						sum(CASE WHEN a.periode >= last_day(now()) + interval 1 day - interval 4 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 4 month) THEN IFNULL(a.sales,0) ELSE 0 END) as sales1, 
						sum(CASE WHEN a.periode >= last_day(now()) + interval 1 day - interval 3 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 3 month) THEN IFNULL(a.sales,0) ELSE 0 END) as sales2, 
						sum(CASE WHEN a.periode >= last_day(now()) + interval 1 day - interval 2 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 2 month) THEN IFNULL(a.sales,0) ELSE 0 END) as sales3,  
						sum(CASE WHEN a.periode >= last_day(now()) + interval 1 day - interval 1 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 1 month) THEN IFNULL(a.sales,0) ELSE 0 END) as sales4, 
						sum(CASE WHEN a.periode >= last_day(now()) + interval 1 day - interval 4 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 4 month) THEN IFNULL(a.target,0) ELSE 0 END) as target1, 
						sum(CASE WHEN a.periode >= last_day(now()) + interval 1 day - interval 3 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 3 month) THEN IFNULL(a.target,0) ELSE 0 END) as target2, 
						sum(CASE WHEN a.periode >= last_day(now()) + interval 1 day - interval 2 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 2 month) THEN IFNULL(a.target,0) ELSE 0 END) as target3,  
						sum(CASE WHEN a.periode >= last_day(now()) + interval 1 day - interval 1 month and a.periode <= last_day(last_day(now()) + interval 1 day - interval 1 month) THEN IFNULL(a.target,0) ELSE 0 END) as target4,
						MONTH(last_day(now()) + interval 1 day - interval 4 month) start_month, 
						YEAR(last_day(now()) + interval 1 day - interval 4 month) start_year
					from t_target_group_salesman a INNER JOIN m_sales_salesman b on a.siteid = b.siteid and a.salesmanid = b.salesmanid 
					where a.siteid = '".$siteid."' AND b.aktif = 1
					group by b.salesmanid,b.nama_salesman,a.groupid, a.nama_group
				  ) as target_per_group
				  " ;
		$this->db->select();
		$this->db->from($sql);
		
		$return = $this->db->get()->result_array();
		return $return;
	}

	function get_data_schedule_actual($siteid) {

		$sql = " (select y.salesmanid,sls.nama_salesman,count(1) as jadwal, IFNULL(x.InvalidCall,0) InvalidCall,IFNULL(x.ExtraCall,0) ExtraCall,
					IFNULL(x.cal,0) 'Call',IFNULL(x.effectivecall,0) effectivecall,IFNULL(x.noo,0) noo, IFNULL(x.amount,0) amount,
					CONCAT(IFNULL(ROUND(((IFNULL(x.cal,0) + IFNULL(x.effectivecall,0))/count(1))*100,1),0),' %') eff_time,
					CONCAT(IFNULL(ROUND((IFNULL(x.effectivecall,0)/(IFNULL(x.cal,0)+IFNULL(x.effectivecall,0)))*100,1),0),' %') eff_order,
				   (select count(1) from t_sales_rrk a, m_customer b where a.siteid=b.siteid and a.customerid=b.customerid and a.salesmanid=b.salesmanid
				   and a.periode=y.periode and a.siteid=y.siteid and a.salesmanid=y.salesmanid and b.longitude=0 and b.latitude=0) longlatnull
				from t_sales_rrk y left join m_sales_salesman sls on y.siteid=sls.siteid and y.salesmanid=sls.salesmanid and sls.aktif=1
				left join
				(select z.siteid,z.periode,z.salesmanid,z.nama_salesman, IFNULL(sum(z.INVCALL),0) as InvalidCall,IFNULL(sum(z.EXCALL),0) as ExtraCall,IFNULL(sum(z.CALL),0) as 'cal',IFNULL(sum(z.EFCALL),0) as effectivecall,
					   (select count(1) from m_customer where siteid=z.siteid and salesmanid=z.salesmanid and createdate=z.periode) noo,
					   (select sum(netto) from t_sales_master where salesmanid=z.salesmanid and tanggal=z.periode and retur=0) as amount
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
						select distinct a.siteid,a.periode,a.salesmanid,d.nama_salesman,b.customerid,e.customerid mcust,b.periode perioderrk,
							 case when b.customerid is null and c.no_sales is null then 'INVCALL' 
							  when b.customerid is null and c.no_sales is not null then 'EXCALL' 
							  when b.customerid is not null and c.no_sales is null or (c.no_sales is not null and c.retur=1) then 'CALL'
							  when b.customerid is not null and c.no_sales is not null and c.retur=0 then 'EFCALL'
							  end JML
						from t_sales_rrk_trans a left join t_sales_rrk b on 
						a.siteid = b.siteid and a.salesmanid = b.salesmanid and a.customerid=b.customerid and a.periode=b.periode
						left join t_sales_master c on a.siteid = c.siteid and a.salesmanid = c.salesmanid and a.customerid=c.customerid and a.periode=c.tanggal
						left join m_sales_salesman d on a.siteid = d.siteid and a.salesmanid = d.salesmanid 
						left join m_customer e on a.customerid = e.customerid
						where a.siteid = '".$siteid."'
						and a.periode=(select tanggal from m_setup_site)
						) d group by d.periode,d.salesmanid,d.nama_salesman,d.salesmanid,d.JML
					) d
				  ) z group by z.salesmanid
				 ) x on y.siteid=x.siteid and y.salesmanid=x.salesmanid and y.periode=x.periode
				 where y.siteid = '".$siteid."' and y.periode=(select tanggal from m_setup_site) and sls.nama_salesman is not null and sls.aktif=1
				 group by y.salesmanid,y.nama_salesman	
					 order by y.salesmanid
				  ) as schedule_actual
				  " ;
		$this->db->select();
		$this->db->from($sql);
		
		$return = $this->db->get()->result_array();
		return $return;
	}

	function get_data_list_order_by_prinsipal($siteid) {
		
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$domainName = $_SERVER['HTTP_HOST'] . '/';
		
		$ip = base_url();

		
		$sql = " (
				 select c.brandid prinsipalid, c.nama_brand nama_prinsipal, b.tanggal, sum(ifnull(a.netto,0)) total_order
					from t_sales_master b join t_sales_detail a on b.no_sales=a.no_sales
								join m_product c on a.productid=c.productid
					where b.siteid='".$siteid."' and b.tanggal =(select tanggal from m_setup_site)
					group by c.brandid, c.nama_brand, b.tanggal
				  ) as orderbyprinciple
				  " ;
				  
		$this->db->select();
		$this->db->from($sql);
		
		$return = $this->db->get()->result_array();
		return $return;
	}	
	
	function get_data_detail_order_by_prinsipal($siteid,$prinsipalid) {
		
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$domainName = $_SERVER['HTTP_HOST'] . '/';
		
		$ip = base_url();

		
		$sql = " (
					select c.brandid prinsipalid, c.nama_brand nama_prinsipal, b.salesmanid, d.nama_salesman, a.productid, c.nama_invoice, sum(ifnull(a.netto,0)) total_order
					from t_sales_master b join t_sales_detail a on b.no_sales=a.no_sales
								join m_product c on a.productid=c.productid
								left JOIN m_sales_salesman d on d.salesmanid=b.salesmanid
						where b.siteid='".$siteid."' and c.brandid='".$prinsipalid."' and b.tanggal =(select tanggal from m_setup_site)
					group by c.brandid, c.nama_brand, b.salesmanid, d.nama_salesman, a.productid, c.nama_invoice
				  ) as orderdetailbyprinciple
				  " ;
				  
		$this->db->select();
		$this->db->from($sql);
		
		$return = $this->db->get()->result_array();
		return $return;
	}	
	
	function get_point_maps($siteid,$sid) {
		
		$q = $this->db->query(" select z.* from (
								select case when b.customerid is null and c.no_sales is null then 'InvalidCall' 
									   when b.customerid is null and c.no_sales is not null then 'ExtraCall' 
									   when b.customerid is not null and c.no_sales is null or (c.no_sales is not null and c.retur=1) then 'Call'
									   when b.customerid is not null and c.no_sales is not null and c.retur=0 then 'EffectiveCall' end as flag,
									   a.siteid, e.nama_site , a.salesmanid,  a.customerid , f.nama_customer, f.alamat, f.latitude, f.longitude, 
									   a.latitude_cell, a.longitude_cell, d.nama_salesman,a.check_in
								from t_sales_rrk_trans a left join t_sales_rrk b on 
								a.siteid = b.siteid and a.salesmanid = b.salesmanid and a.customerid=b.customerid and a.periode=b.periode
								left join t_sales_master c on a.siteid = c.siteid and a.salesmanid = c.salesmanid and a.customerid=c.customerid and a.periode=c.tanggal
								left join m_sales_salesman d on a.siteid = d.siteid and a.salesmanid = d.salesmanid 
								left join m_setup_site e ON a.siteid = e.siteid
								left join m_customer f on a.customerid=f.customerid
								where a.siteid ='".$siteid."' and a.salesmanid='".$sid."' and a.periode=(select tanggal from m_setup_site)
								UNION ALL
								select 'Jadwal' as flag, rrk.siteid, site.nama_site , rrk.salesmanid,  rrk.customerid , cust.nama_customer, cust.alamat, cust.latitude, 
								cust.longitude, cust.latitude as latitude_cell, cust.longitude as longitude_cell, sls.nama_salesman, '' check_in
								from  t_sales_rrk rrk								
								INNER JOIN  m_sales_salesman sls ON rrk.siteid = sls.siteid and rrk.salesmanid = sls.salesmanid
								INNER JOIN  m_customer cust ON rrk.siteid = cust.siteid and rrk.customerid = cust.customerid and rrk.salesmanid=cust.salesmanid
								INNER JOIN  m_setup_site site ON rrk.siteid = site.siteid
								where rrk.siteid = '".$siteid."'  AND rrk.salesmanid = '".$sid."' AND rrk.periode = (select tanggal from m_setup_site)
								UNION ALL
								select 'Noo' as flag, cust.siteid, site.nama_site , cust.salesmanid,  cust.customerid , cust.nama_customer, 
										cust.alamat, cust.latitude, cust.longitude, cust.latitude as latitude_cell, cust.longitude as longitude_cell, 
										'' nama_salesman, '' check_in
								from  m_customer as cust
								INNER JOIN  m_sales_salesman sls ON cust.siteid = sls.siteid and cust.salesmanid = sls.salesmanid
								INNER JOIN  m_setup_site site ON cust.siteid = site.siteid
								where cust.siteid = '".$siteid."'  AND cust.salesmanid = '".$sid."' AND cust.createdate = (select tanggal from m_setup_site) ) z
								order by z.check_in asc
								");
								
		return $q->result_array();
	}

	function get_tracking($siteid,$sid) {
		
		$q = $this->db->query(" select ifnull(latitude_cell,0) latitude_cell, ifnull(longitude_cell,0) longitude_cell,
										DATE_FORMAT(createdate,'%H:%i') waktu
								from t_tracker_salesman
								where siteid = '".$siteid."'  AND salesmanid = '".$sid."' AND periode = (select tanggal from m_setup_site)
								order by DATE_FORMAT(createdate,'%H:%i') asc
								");

		return $q->result_array();
	}
	
	function get_data_daily_order($companyid,$siteid,$periode) {
		$q = $this->db->query(" select * from t_daily_order
								where companyid = '".$companyid."' and siteid = '".$siteid."' and periode = '".$periode."'
								");
		return $q->result_array();
	}

	function get_data_daily_sales($companyid,$siteid,$periode) {
		$q = $this->db->query(" select * from t_daily_sales
								where companyid = '".$companyid."' and siteid = '".$siteid."' and periode = '".$periode."'
								");
		return $q->result_array();
	}

	function get_data_monthly_sales($companyid,$siteid,$periode) {
		$q = $this->db->query(" select * from t_monthly_sales
								where companyid = '".$companyid."' and siteid = '".$siteid."' and periode = '".$periode."'
								");
		return $q->result_array();
	}

	function insert_image($data_insert){
		return $this->db->insert('m_customer_image',$data_insert);
	}
	
}