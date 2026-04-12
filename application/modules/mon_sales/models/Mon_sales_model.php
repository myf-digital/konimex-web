<?php 
class Mon_sales_model extends CI_Model {
	
	function get_siteid() {
		$this->db->select("siteid");
		$this->db->from("m_setup_site");
		$site = $this->db->get()->row()->siteid;
		return $site;
	}
	
	function get_lat_long() {
		$this->db->select("latitude,longitude");
		$this->db->from("m_setup_site");
		$data = $this->db->get()->row();
		return $data;
	}
	
	function get_record_month($customerid,$salesmanid,$month,$year) {
		$q = $this->db->query("
			SELECT tcrc.siteid, site.nama_site,
			   tcrc.salesmanid, sls.nama_salesman,
			   tcrc.customerid, cust.nama_customer,
			   tcrc.categoryid, product.nama_category,
			   tcrc.brandid, product.nama_brand,
			   tcrc.productid, product.nama_invoice,
			   (CASE WHEN DAY(tcrc.periode) = 1 THEN tcrc.qty_rata ELSE 0 END) as r1,
			   (CASE WHEN DAY(tcrc.periode) = 1 THEN tcrc.qty_akhir ELSE 0 END) as a1,
			   (CASE WHEN DAY(tcrc.periode) = 1 THEN tcrc.qty_saran_order ELSE 0 END) as s1, 
			   (CASE WHEN DAY(tcrc.periode) = 1 THEN tcrc.qty_fix_order ELSE 0 END) as f1,
			   (CASE WHEN DAY(tcrc.periode) = 2 THEN tcrc.qty_rata ELSE 0 END) as r2,
			   (CASE WHEN DAY(tcrc.periode) = 2 THEN tcrc.qty_akhir ELSE 0 END) as a2,
			   (CASE WHEN DAY(tcrc.periode) = 2 THEN tcrc.qty_saran_order ELSE 0 END) as s2, 
			   (CASE WHEN DAY(tcrc.periode) = 2 THEN tcrc.qty_fix_order ELSE 0 END) as f2,
			   (CASE WHEN DAY(tcrc.periode) = 3 THEN tcrc.qty_rata ELSE 0 END) as r3,
			   (CASE WHEN DAY(tcrc.periode) = 3 THEN tcrc.qty_akhir ELSE 0 END) as a3,
			   (CASE WHEN DAY(tcrc.periode) = 3 THEN tcrc.qty_saran_order ELSE 0 END) as s3, 
			   (CASE WHEN DAY(tcrc.periode) = 3 THEN tcrc.qty_fix_order ELSE 0 END) as f3,
			   (CASE WHEN DAY(tcrc.periode) = 4 THEN tcrc.qty_rata ELSE 0 END) as r4,
			   (CASE WHEN DAY(tcrc.periode) = 4 THEN tcrc.qty_akhir ELSE 0 END) as a4,
			   (CASE WHEN DAY(tcrc.periode) = 4 THEN tcrc.qty_saran_order ELSE 0 END) as s4, 
			   (CASE WHEN DAY(tcrc.periode) = 4 THEN tcrc.qty_fix_order ELSE 0 END) as f4,
			   (CASE WHEN DAY(tcrc.periode) = 5 THEN tcrc.qty_rata ELSE 0 END) as r5,
			   (CASE WHEN DAY(tcrc.periode) = 5 THEN tcrc.qty_akhir ELSE 0 END) as a5,
			   (CASE WHEN DAY(tcrc.periode) = 5 THEN tcrc.qty_saran_order ELSE 0 END) as s5, 
			   (CASE WHEN DAY(tcrc.periode) = 5 THEN tcrc.qty_fix_order ELSE 0 END) as f5,
			   (CASE WHEN DAY(tcrc.periode) = 6 THEN tcrc.qty_rata ELSE 0 END) as r6,
			   (CASE WHEN DAY(tcrc.periode) = 6 THEN tcrc.qty_akhir ELSE 0 END) as a6,
			   (CASE WHEN DAY(tcrc.periode) = 6 THEN tcrc.qty_saran_order ELSE 0 END) as s6, 
			   (CASE WHEN DAY(tcrc.periode) = 6 THEN tcrc.qty_fix_order ELSE 0 END) as f6,
			   (CASE WHEN DAY(tcrc.periode) = 7 THEN tcrc.qty_rata ELSE 0 END) as r7,
			   (CASE WHEN DAY(tcrc.periode) = 7 THEN tcrc.qty_akhir ELSE 0 END) as a7,
			   (CASE WHEN DAY(tcrc.periode) = 7 THEN tcrc.qty_saran_order ELSE 0 END) as s7, 
			   (CASE WHEN DAY(tcrc.periode) = 7 THEN tcrc.qty_fix_order ELSE 0 END) as f7,
			   (CASE WHEN DAY(tcrc.periode) = 8 THEN tcrc.qty_rata ELSE 0 END) as r8,
			   (CASE WHEN DAY(tcrc.periode) = 8 THEN tcrc.qty_akhir ELSE 0 END) as a8,
			   (CASE WHEN DAY(tcrc.periode) = 8 THEN tcrc.qty_saran_order ELSE 0 END) as s8, 
			   (CASE WHEN DAY(tcrc.periode) = 8 THEN tcrc.qty_fix_order ELSE 0 END) as f8,
			   (CASE WHEN DAY(tcrc.periode) = 9 THEN tcrc.qty_rata ELSE 0 END) as r9,
			   (CASE WHEN DAY(tcrc.periode) = 9 THEN tcrc.qty_akhir ELSE 0 END) as a9,
			   (CASE WHEN DAY(tcrc.periode) = 9 THEN tcrc.qty_saran_order ELSE 0 END) as s9, 
			   (CASE WHEN DAY(tcrc.periode) = 9 THEN tcrc.qty_fix_order ELSE 0 END) as f9,
			   (CASE WHEN DAY(tcrc.periode) = 10 THEN tcrc.qty_rata ELSE 0 END) as r10,
			   (CASE WHEN DAY(tcrc.periode) = 10 THEN tcrc.qty_akhir ELSE 0 END) as a10,
			   (CASE WHEN DAY(tcrc.periode) = 10 THEN tcrc.qty_saran_order ELSE 0 END) as s10, 
			   (CASE WHEN DAY(tcrc.periode) = 10 THEN tcrc.qty_fix_order ELSE 0 END) as f10,
			   (CASE WHEN DAY(tcrc.periode) = 11 THEN tcrc.qty_rata ELSE 0 END) as r11,
			   (CASE WHEN DAY(tcrc.periode) = 11 THEN tcrc.qty_akhir ELSE 0 END) as a11,
			   (CASE WHEN DAY(tcrc.periode) = 11 THEN tcrc.qty_saran_order ELSE 0 END) as s11, 
			   (CASE WHEN DAY(tcrc.periode) = 11 THEN tcrc.qty_fix_order ELSE 0 END) as f11,
			   (CASE WHEN DAY(tcrc.periode) = 12 THEN tcrc.qty_rata ELSE 0 END) as r12,
			   (CASE WHEN DAY(tcrc.periode) = 12 THEN tcrc.qty_akhir ELSE 0 END) as a12,
			   (CASE WHEN DAY(tcrc.periode) = 12 THEN tcrc.qty_saran_order ELSE 0 END) as s12, 
			   (CASE WHEN DAY(tcrc.periode) = 12 THEN tcrc.qty_fix_order ELSE 0 END) as f12,
			   (CASE WHEN DAY(tcrc.periode) = 13 THEN tcrc.qty_rata ELSE 0 END) as r13,
			   (CASE WHEN DAY(tcrc.periode) = 13 THEN tcrc.qty_akhir ELSE 0 END) as a13,
			   (CASE WHEN DAY(tcrc.periode) = 13 THEN tcrc.qty_saran_order ELSE 0 END) as s13, 
			   (CASE WHEN DAY(tcrc.periode) = 13 THEN tcrc.qty_fix_order ELSE 0 END) as f13,
			   (CASE WHEN DAY(tcrc.periode) = 14 THEN tcrc.qty_rata ELSE 0 END) as r14,
			   (CASE WHEN DAY(tcrc.periode) = 14 THEN tcrc.qty_akhir ELSE 0 END) as a14,
			   (CASE WHEN DAY(tcrc.periode) = 14 THEN tcrc.qty_saran_order ELSE 0 END) as s14, 
			   (CASE WHEN DAY(tcrc.periode) = 14 THEN tcrc.qty_fix_order ELSE 0 END) as f14,
			   (CASE WHEN DAY(tcrc.periode) = 15 THEN tcrc.qty_rata ELSE 0 END) as r15,
			   (CASE WHEN DAY(tcrc.periode) = 15 THEN tcrc.qty_akhir ELSE 0 END) as a15,
			   (CASE WHEN DAY(tcrc.periode) = 15 THEN tcrc.qty_saran_order ELSE 0 END) as s15, 
			   (CASE WHEN DAY(tcrc.periode) = 15 THEN tcrc.qty_fix_order ELSE 0 END) as f15,
			   (CASE WHEN DAY(tcrc.periode) = 16 THEN tcrc.qty_rata ELSE 0 END) as r16,
			   (CASE WHEN DAY(tcrc.periode) = 16 THEN tcrc.qty_akhir ELSE 0 END) as a16,
			   (CASE WHEN DAY(tcrc.periode) = 16 THEN tcrc.qty_saran_order ELSE 0 END) as s16, 
			   (CASE WHEN DAY(tcrc.periode) = 16 THEN tcrc.qty_fix_order ELSE 0 END) as f16,
			   (CASE WHEN DAY(tcrc.periode) = 17 THEN tcrc.qty_rata ELSE 0 END) as r17,
			   (CASE WHEN DAY(tcrc.periode) = 17 THEN tcrc.qty_akhir ELSE 0 END) as a17,
			   (CASE WHEN DAY(tcrc.periode) = 17 THEN tcrc.qty_saran_order ELSE 0 END) as s17, 
			   (CASE WHEN DAY(tcrc.periode) = 17 THEN tcrc.qty_fix_order ELSE 0 END) as f17,
			   (CASE WHEN DAY(tcrc.periode) = 18 THEN tcrc.qty_rata ELSE 0 END) as r18,
			   (CASE WHEN DAY(tcrc.periode) = 18 THEN tcrc.qty_akhir ELSE 0 END) as a18,
			   (CASE WHEN DAY(tcrc.periode) = 18 THEN tcrc.qty_saran_order ELSE 0 END) as s18, 
			   (CASE WHEN DAY(tcrc.periode) = 18 THEN tcrc.qty_fix_order ELSE 0 END) as f18,
			   (CASE WHEN DAY(tcrc.periode) = 19 THEN tcrc.qty_rata ELSE 0 END) as r19,
			   (CASE WHEN DAY(tcrc.periode) = 19 THEN tcrc.qty_akhir ELSE 0 END) as a19,
			   (CASE WHEN DAY(tcrc.periode) = 19 THEN tcrc.qty_saran_order ELSE 0 END) as s19, 
			   (CASE WHEN DAY(tcrc.periode) = 19 THEN tcrc.qty_fix_order ELSE 0 END) as f19,
			   (CASE WHEN DAY(tcrc.periode) = 20 THEN tcrc.qty_rata ELSE 0 END) as r20,
			   (CASE WHEN DAY(tcrc.periode) = 20 THEN tcrc.qty_akhir ELSE 0 END) as a20,
			   (CASE WHEN DAY(tcrc.periode) = 20 THEN tcrc.qty_saran_order ELSE 0 END) as s20, 
			   (CASE WHEN DAY(tcrc.periode) = 20 THEN tcrc.qty_fix_order ELSE 0 END) as f20,
			   (CASE WHEN DAY(tcrc.periode) = 21 THEN tcrc.qty_rata ELSE 0 END) as r21,
			   (CASE WHEN DAY(tcrc.periode) = 21 THEN tcrc.qty_akhir ELSE 0 END) as a21,
			   (CASE WHEN DAY(tcrc.periode) = 21 THEN tcrc.qty_saran_order ELSE 0 END) as s21, 
			   (CASE WHEN DAY(tcrc.periode) = 21 THEN tcrc.qty_fix_order ELSE 0 END) as f21,
			   (CASE WHEN DAY(tcrc.periode) = 22 THEN tcrc.qty_rata ELSE 0 END) as r22,
			   (CASE WHEN DAY(tcrc.periode) = 22 THEN tcrc.qty_akhir ELSE 0 END) as a22,
			   (CASE WHEN DAY(tcrc.periode) = 22 THEN tcrc.qty_saran_order ELSE 0 END) as s22, 
			   (CASE WHEN DAY(tcrc.periode) = 22 THEN tcrc.qty_fix_order ELSE 0 END) as f22,
			   (CASE WHEN DAY(tcrc.periode) = 23 THEN tcrc.qty_rata ELSE 0 END) as r23,
			   (CASE WHEN DAY(tcrc.periode) = 23 THEN tcrc.qty_akhir ELSE 0 END) as a23,
			   (CASE WHEN DAY(tcrc.periode) = 23 THEN tcrc.qty_saran_order ELSE 0 END) as s23, 
			   (CASE WHEN DAY(tcrc.periode) = 23 THEN tcrc.qty_fix_order ELSE 0 END) as f23,
			   (CASE WHEN DAY(tcrc.periode) = 24 THEN tcrc.qty_rata ELSE 0 END) as r24,
			   (CASE WHEN DAY(tcrc.periode) = 24 THEN tcrc.qty_akhir ELSE 0 END) as a24,
			   (CASE WHEN DAY(tcrc.periode) = 24 THEN tcrc.qty_saran_order ELSE 0 END) as s24, 
			   (CASE WHEN DAY(tcrc.periode) = 24 THEN tcrc.qty_fix_order ELSE 0 END) as f24,
			   (CASE WHEN DAY(tcrc.periode) = 25 THEN tcrc.qty_rata ELSE 0 END) as r25,
			   (CASE WHEN DAY(tcrc.periode) = 25 THEN tcrc.qty_akhir ELSE 0 END) as a25,
			   (CASE WHEN DAY(tcrc.periode) = 25 THEN tcrc.qty_saran_order ELSE 0 END) as s25, 
			   (CASE WHEN DAY(tcrc.periode) = 25 THEN tcrc.qty_fix_order ELSE 0 END) as f25,
			   (CASE WHEN DAY(tcrc.periode) = 26 THEN tcrc.qty_rata ELSE 0 END) as r26,
			   (CASE WHEN DAY(tcrc.periode) = 26 THEN tcrc.qty_akhir ELSE 0 END) as a26,
			   (CASE WHEN DAY(tcrc.periode) = 26 THEN tcrc.qty_saran_order ELSE 0 END) as s26, 
			   (CASE WHEN DAY(tcrc.periode) = 26 THEN tcrc.qty_fix_order ELSE 0 END) as f26,
			   (CASE WHEN DAY(tcrc.periode) = 27 THEN tcrc.qty_rata ELSE 0 END) as r27,
			   (CASE WHEN DAY(tcrc.periode) = 27 THEN tcrc.qty_akhir ELSE 0 END) as a27,
			   (CASE WHEN DAY(tcrc.periode) = 27 THEN tcrc.qty_saran_order ELSE 0 END) as s27, 
			   (CASE WHEN DAY(tcrc.periode) = 27 THEN tcrc.qty_fix_order ELSE 0 END) as f27,
			   (CASE WHEN DAY(tcrc.periode) = 28 THEN tcrc.qty_rata ELSE 0 END) as r28,
			   (CASE WHEN DAY(tcrc.periode) = 28 THEN tcrc.qty_akhir ELSE 0 END) as a28,
			   (CASE WHEN DAY(tcrc.periode) = 28 THEN tcrc.qty_saran_order ELSE 0 END) as s28, 
			   (CASE WHEN DAY(tcrc.periode) = 28 THEN tcrc.qty_fix_order ELSE 0 END) as f28,
			   (CASE WHEN DAY(tcrc.periode) = 29 THEN tcrc.qty_rata ELSE 0 END) as r29,
			   (CASE WHEN DAY(tcrc.periode) = 29 THEN tcrc.qty_akhir ELSE 0 END) as a29,
			   (CASE WHEN DAY(tcrc.periode) = 29 THEN tcrc.qty_saran_order ELSE 0 END) as s29, 
			   (CASE WHEN DAY(tcrc.periode) = 29 THEN tcrc.qty_fix_order ELSE 0 END) as f29,
			   (CASE WHEN DAY(tcrc.periode) = 30 THEN tcrc.qty_rata ELSE 0 END) as r30,
			   (CASE WHEN DAY(tcrc.periode) = 30 THEN tcrc.qty_akhir ELSE 0 END) as a30,
			   (CASE WHEN DAY(tcrc.periode) = 30 THEN tcrc.qty_saran_order ELSE 0 END) as s30, 
			   (CASE WHEN DAY(tcrc.periode) = 30 THEN tcrc.qty_fix_order ELSE 0 END) as f30,
			   (CASE WHEN DAY(tcrc.periode) = 31 THEN tcrc.qty_rata ELSE 0 END) as r31,
			   (CASE WHEN DAY(tcrc.periode) = 31 THEN tcrc.qty_akhir ELSE 0 END) as a31,
			   (CASE WHEN DAY(tcrc.periode) = 31 THEN tcrc.qty_saran_order ELSE 0 END) as s31, 
			   (CASE WHEN DAY(tcrc.periode) = 31 THEN tcrc.qty_fix_order ELSE 0 END) as f31
		FROM
		t_sales_crc tcrc 
		INNER JOIN m_customer cust ON tcrc.siteid = cust.siteid and tcrc.salesmanid = cust.salesmanid and tcrc.customerid = cust.customerid
		INNER JOIN m_product product ON tcrc.productid = product.productid
		INNER JOIN m_sales_salesman  sls ON tcrc.siteid = sls.siteid and tcrc.salesmanid = sls.salesmanid 
		INNER JOIN m_setup_site site ON tcrc.siteid = site.siteid
		WHERE tcrc.siteid = site.siteid AND
			  MONTH(tcrc.periode) = ".$month." AND
			  YEAR(tcrc.periode) = ".$year." AND
			  tcrc.salesmanid = '".$salesmanid."' AND
			   tcrc.customerid = '".$customerid."' 
		");
		$data = $q->result_array();
	
		$return ='<table class="table table-striped table-bordered table-condensed">
				<thead>
					<tr>
						<td colspan="8" align="center">Tanggal</td>
						<td colspan="4" align="center">1</td>
						<td colspan="4" align="center">2</td>
						<td colspan="4" align="center">3</td>
						<td colspan="4" align="center">4</td>
						<td colspan="4" align="center">5</td>
						<td colspan="4" align="center">6</td>
						<td colspan="4" align="center">7</td>
						<td colspan="4" align="center">8</td>
						<td colspan="4" align="center">9</td>
						<td colspan="4" align="center">10</td>
						<td colspan="4" align="center">11</td>
						<td colspan="4" align="center">12</td>
						<td colspan="4" align="center">13</td>
						<td colspan="4" align="center">14</td>
						<td colspan="4" align="center">15</td>
						<td colspan="4" align="center">16</td>
						<td colspan="4" align="center">17</td>
						<td colspan="4" align="center">18</td>
						<td colspan="4" align="center">19</td>
						<td colspan="4" align="center">20</td>
						<td colspan="4" align="center">21</td>
						<td colspan="4" align="center">22</td>
						<td colspan="4" align="center">23</td>
						<td colspan="4" align="center">24</td>
						<td colspan="4" align="center">25</td>
						<td colspan="4" align="center">26</td>
						<td colspan="4" align="center">27</td>
						<td colspan="4" align="center">28</td>
						<td colspan="4" align="center">29</td>
						<td colspan="4" align="center">30</td>
						<td colspan="4" align="center">31</td>
					 </tr>
					 <tr>
						<td colspan ="4" align="center">Product ID</td>
						<td colspan ="4" align="center">Product Name</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
						<td align="center">R</td>
						<td align="center">A</td>
						<td align="center">S</td>
						<td align="center">F</td>
					</td>
				</thead>
				<tbody>';
		
		foreach ($data as $value) {
			$return .= '<tr><td colspan ="4">'.$value['productid'].'</td>';
			$return .= '<td colspan ="4"></td>';
			
				$return .= '<td>'.$value['r1'].'</td>';
				$return .= '<td>'.$value['a1'].'</td>';
				$return .= '<td>'.$value['s1'].'</td>';
				$return .= '<td>'.$value['f1'].'</td>';
				
				$return .= '<td>'.$value['r2'].'</td>';
				$return .= '<td>'.$value['a2'].'</td>';
				$return .= '<td>'.$value['s2'].'</td>';
				$return .= '<td>'.$value['f2'].'</td>';
				
				$return .= '<td>'.$value['r3'].'</td>';
				$return .= '<td>'.$value['a3'].'</td>';
				$return .= '<td>'.$value['s3'].'</td>';
				$return .= '<td>'.$value['f3'].'</td>';
				
				$return .= '<td>'.$value['r4'].'</td>';
				$return .= '<td>'.$value['a4'].'</td>';
				$return .= '<td>'.$value['s4'].'</td>';
				$return .= '<td>'.$value['f4'].'</td>';
				
				$return .= '<td>'.$value['r5'].'</td>';
				$return .= '<td>'.$value['a5'].'</td>';
				$return .= '<td>'.$value['s5'].'</td>';
				$return .= '<td>'.$value['f5'].'</td>';
				
				$return .= '<td>'.$value['r6'].'</td>';
				$return .= '<td>'.$value['a6'].'</td>';
				$return .= '<td>'.$value['s6'].'</td>';
				$return .= '<td>'.$value['f6'].'</td>';
				
				$return .= '<td>'.$value['r7'].'</td>';
				$return .= '<td>'.$value['a7'].'</td>';
				$return .= '<td>'.$value['s7'].'</td>';
				$return .= '<td>'.$value['f7'].'</td>';
				
				$return .= '<td>'.$value['r8'].'</td>';
				$return .= '<td>'.$value['a8'].'</td>';
				$return .= '<td>'.$value['s8'].'</td>';
				$return .= '<td>'.$value['f8'].'</td>';
				
				$return .= '<td>'.$value['r9'].'</td>';
				$return .= '<td>'.$value['a9'].'</td>';
				$return .= '<td>'.$value['s9'].'</td>';
				$return .= '<td>'.$value['f9'].'</td>';
				
				$return .= '<td>'.$value['r10'].'</td>';
				$return .= '<td>'.$value['a10'].'</td>';
				$return .= '<td>'.$value['s10'].'</td>';
				$return .= '<td>'.$value['f10'].'</td>';
				
				$return .= '<td>'.$value['r11'].'</td>';
				$return .= '<td>'.$value['a11'].'</td>';
				$return .= '<td>'.$value['s11'].'</td>';
				$return .= '<td>'.$value['f11'].'</td>';
				
				$return .= '<td>'.$value['r12'].'</td>';
				$return .= '<td>'.$value['a12'].'</td>';
				$return .= '<td>'.$value['s12'].'</td>';
				$return .= '<td>'.$value['f12'].'</td>';
				
				$return .= '<td>'.$value['r13'].'</td>';
				$return .= '<td>'.$value['a13'].'</td>';
				$return .= '<td>'.$value['s13'].'</td>';
				$return .= '<td>'.$value['f13'].'</td>';
				
				$return .= '<td>'.$value['r14'].'</td>';
				$return .= '<td>'.$value['a14'].'</td>';
				$return .= '<td>'.$value['s14'].'</td>';
				$return .= '<td>'.$value['f14'].'</td>';
				
				$return .= '<td>'.$value['r15'].'</td>';
				$return .= '<td>'.$value['a15'].'</td>';
				$return .= '<td>'.$value['s15'].'</td>';
				$return .= '<td>'.$value['f15'].'</td>';
				
				$return .= '<td>'.$value['r16'].'</td>';
				$return .= '<td>'.$value['a16'].'</td>';
				$return .= '<td>'.$value['s16'].'</td>';
				$return .= '<td>'.$value['f16'].'</td>';
				
				$return .= '<td>'.$value['r17'].'</td>';
				$return .= '<td>'.$value['a17'].'</td>';
				$return .= '<td>'.$value['s17'].'</td>';
				$return .= '<td>'.$value['f17'].'</td>';
				
				$return .= '<td>'.$value['r18'].'</td>';
				$return .= '<td>'.$value['a18'].'</td>';
				$return .= '<td>'.$value['s18'].'</td>';
				$return .= '<td>'.$value['f18'].'</td>';
				
				$return .= '<td>'.$value['r19'].'</td>';
				$return .= '<td>'.$value['a19'].'</td>';
				$return .= '<td>'.$value['s19'].'</td>';
				$return .= '<td>'.$value['f19'].'</td>';
				
				$return .= '<td>'.$value['r20'].'</td>';
				$return .= '<td>'.$value['a20'].'</td>';
				$return .= '<td>'.$value['s20'].'</td>';
				$return .= '<td>'.$value['f20'].'</td>';
				
				$return .= '<td>'.$value['r21'].'</td>';
				$return .= '<td>'.$value['a21'].'</td>';
				$return .= '<td>'.$value['s21'].'</td>';
				$return .= '<td>'.$value['f21'].'</td>';
				
				$return .= '<td>'.$value['r22'].'</td>';
				$return .= '<td>'.$value['a22'].'</td>';
				$return .= '<td>'.$value['s22'].'</td>';
				$return .= '<td>'.$value['f22'].'</td>';
				
				$return .= '<td>'.$value['r23'].'</td>';
				$return .= '<td>'.$value['a23'].'</td>';
				$return .= '<td>'.$value['s23'].'</td>';
				$return .= '<td>'.$value['f23'].'</td>';
				
				$return .= '<td>'.$value['r24'].'</td>';
				$return .= '<td>'.$value['a24'].'</td>';
				$return .= '<td>'.$value['s24'].'</td>';
				$return .= '<td>'.$value['f24'].'</td>';
				
				$return .= '<td>'.$value['r25'].'</td>';
				$return .= '<td>'.$value['a25'].'</td>';
				$return .= '<td>'.$value['s25'].'</td>';
				$return .= '<td>'.$value['f25'].'</td>';
				
				$return .= '<td>'.$value['r26'].'</td>';
				$return .= '<td>'.$value['a26'].'</td>';
				$return .= '<td>'.$value['s26'].'</td>';
				$return .= '<td>'.$value['f26'].'</td>';
				
				$return .= '<td>'.$value['r27'].'</td>';
				$return .= '<td>'.$value['a27'].'</td>';
				$return .= '<td>'.$value['s27'].'</td>';
				$return .= '<td>'.$value['f27'].'</td>';
				
				$return .= '<td>'.$value['r28'].'</td>';
				$return .= '<td>'.$value['a28'].'</td>';
				$return .= '<td>'.$value['s28'].'</td>';
				$return .= '<td>'.$value['f28'].'</td>';
				
				$return .= '<td>'.$value['r29'].'</td>';
				$return .= '<td>'.$value['a29'].'</td>';
				$return .= '<td>'.$value['s29'].'</td>';
				$return .= '<td>'.$value['f29'].'</td>';
				
				$return .= '<td>'.$value['r30'].'</td>';
				$return .= '<td>'.$value['a30'].'</td>';
				$return .= '<td>'.$value['s30'].'</td>';
				$return .= '<td>'.$value['f30'].'</td>';
				
				$return .= '<td>'.$value['r31'].'</td>';
				$return .= '<td>'.$value['a31'].'</td>';
				$return .= '<td>'.$value['s31'].'</td>';
				$return .= '<td>'.$value['f31'].'</td>';
				
				
				
			
				$return .= '</tr>';
					
		}
		
		$return .='</tbody></table>';	
		
		return $return;
	}
	
	function get_record($siteid,$customerid,$salesmanid,$get_date) {
		$q = $this->db->query("SELECT tcrc.siteid, site.nama_site,
			   tcrc.salesmanid, sls.nama_salesman,
			   tcrc.customerid,  cust.nama_customer,
			   tcrc.categoryid, product.nama_category,
			   tcrc.brandid, product.nama_brand,
			   tcrc.productid, product.nama_invoice,
			   tcrc.qty_rata  as r1,
			   tcrc.qty_akhir  as a1,
			   tcrc.qty_saran_order  as s1, 
			   tcrc.qty_fix_order  as f1
				FROM
				t_sales_crc tcrc 
				INNER JOIN m_customer cust ON tcrc.siteid = cust.siteid and tcrc.salesmanid = cust.salesmanid and tcrc.customerid = cust.customerid
				INNER JOIN m_product product ON tcrc.productid = product.productid
				INNER JOIN m_sales_salesman  sls ON tcrc.siteid = sls.siteid and tcrc.salesmanid = sls.salesmanid 
				INNER JOIN m_setup_site site ON tcrc.siteid = site.siteid
				WHERE tcrc.siteid = '".$siteid."' AND
					  tcrc.periode = '".$get_date."' AND
					  tcrc.salesmanid = '".$salesmanid."' AND
					  tcrc.customerid = '".$customerid."'  
		 
		");
		$data = $q->result_array();
	
		$return ='\'<table class="table table-striped table-bordered table-condensed" style="white-space: nowrap;">\'+
				\'<thead>\'+
					\'<tr>\'+
						\'<td colspan="8" align="center">Tanggal</td>\'+
						\'<td colspan="4" align="center">'.$get_date.'</td>\'+						
					\'</tr>\'+
					\'<tr>\'+
						\'<td colspan ="4" style="text-align:center;">Product ID</td>\'+
						\'<td colspan ="4" style="text-align:left;padding:10px;">Product Name</td>\'+
						\'<td style="text-align:right;padding:10px;">Rata-rata</td>\'+
						\'<td style="text-align:right;padding:10px;">Akhir</td>\'+
						\'<td style="text-align:right;padding:10px;">Saran Order</td>\'+
						\'<td style="text-align:right;padding:10px;">Fix Order</td>\'+
						
					\'</td>\'+
				\'</thead>\'+
				\'<tbody>\'+';
		
		foreach ($data as $value) {
			$return .= '\'<tr><td colspan ="4" style="text-align:center;">'.$value['productid'].'</td>\'+';
			$return .= '\'<td colspan ="4" style="text-align:left;padding:10px;">'.$value['nama_invoice'].'</td>\'+';
			
				$return .= '\'<td style="text-align:right;padding:10px;">'.$value['r1'].'</td>\'+';
				$return .= '\'<td style="text-align:right;padding:10px;">'.$value['a1'].'</td>\'+';
				$return .= '\'<td style="text-align:right;padding:10px;">'.$value['s1'].'</td>\'+';
				$return .= '\'<td style="text-align:right;padding:10px;">'.$value['f1'].'</td>\'+';			
			
				$return .= '\'</tr>\'+';
					
		}
		
		$return .='\'</tbody></table>\'+';	
		
		return $return;
	}

	function get_order($siteid,$customerid,$salesmanid,$get_date) {
				$return = '\'<table class="table table-striped table-bordered table-condensed">\'+
							\'<thead>\'+
							\'<tr>\'+
							\'<th style="white-space: nowrap;padding-left:10px">Product ID </th>\'+
							\'<th style="white-space: nowrap;padding-left:10px" >Nama Invoice</th>\'+
							\'<th style="white-space: nowrap;text-align:right;padding-right:10px;" >QTY PCS</th>\'+
							\'<th style="white-space: nowrap;text-align:right;padding-right:10px;" >Harga Jual</th>\'+
							\'<th style="white-space: nowrap;text-align:right;padding-right:10px;" >Bruto</th>\'+
							\'<th style="white-space: nowrap;text-align:right;padding-right:10px;" >Diskon</th>\'+
							\'<th style="white-space: nowrap;text-align:right;padding-right:10px;" >Neto</th>\'+	
							\'</tr>\'+
							\'</thead>\'+
							\'<tbody>\'+';
				$q_detail = $this->db->query("
					select 
					   sls.siteid, 
					   sls.salesmanid,
					   salesamn.nama_salesman,
					   sls.customerid,
					   cst.nama_customer,
					   cst.alamat,
					   dtl.productid,
					   product.nama_invoice,
					   sum(case when dtl.flag_bonus = 0 then 'JUAL' else 'BONUS' end) as statu_order,
					   sum(case when dtl.flag_bonus = 0 then dtl.qty_kecil else dtl.qty_bonus end) as qty_jual_in_pcs,
					   sum(case when dtl.flag_bonus = 0 then dtl.qty_kecil/product.isi_besar else dtl.qty_bonus/product.isi_besar end) as qty_jual_in_carton,
					   dtl.h_jual,
					   sum(case when dtl.flag_bonus = 0 then dtl.qty_kecil*dtl.h_jual else 0 end) as total_bruto,
					   dtl.disc_cabang,
					   dtl.disc_prinsipal,
					   dtl.disc_xtra,
					   dtl.disc_cod,
					   SUM(dtl.rp_cabang) AS rp_cabang,
					   SUM(dtl.rp_prinsipal) AS rp_prinsipal,
					   SUM(dtl.rp_xtra) AS rp_xtra,
					   sum(dtl.rp_cod) as rp_cod,
					   sum(case when dtl.flag_bonus = 0 then dtl.rp_cabang+dtl.rp_prinsipal+dtl.rp_xtra+dtl.rp_cod else 0 end) as total_discount,
					   sum(case when dtl.flag_bonus = 0 then (dtl.qty_kecil*dtl.h_jual) - (dtl.rp_cabang+dtl.rp_prinsipal+dtl.rp_xtra+dtl.rp_cod) else 0 end) as total_netto
					from 
					t_sales_master sls left join
					t_sales_detail dtl on sls.siteid = dtl.siteid and sls.no_sales = dtl.no_sales left JOIN
					m_customer cst on sls.siteid = cst.siteid and sls.customerid = cst.customerid and sls.salesmanid = cst.salesmanid left JOIN
					m_sales_salesman salesamn on sls.siteid = salesamn.siteid and sls.salesmanid = salesamn.salesmanid left JOIN  
					m_product product on dtl.productid = product.productid 
					where sls.siteid = '".$siteid."' AND 
						  sls.salesmanid = '".$salesmanid."' AND
						  sls.retur = 0 AND
						  date(sls.tanggal) = '".$get_date."' AND
						  sls.customerid = '".$customerid."'
					group by sls.siteid, 
						   sls.salesmanid,
						   salesamn.nama_salesman,
						   sls.customerid,
						   cst.nama_customer,
						   cst.alamat,
						   dtl.productid,
						   product.nama_invoice,dtl.h_jual,
						   dtl.disc_cabang,
						   dtl.disc_prinsipal,
						   dtl.disc_xtra,
						   dtl.disc_cod		
				");
				$totbruto = 0;
				$totdisc  = 0;
				$totnetto = 0;
				$k_detail = $q_detail->result_array();
				foreach ($k_detail as $v_detail) {
					$return .= '\'<tr>\'+
								\'<td style="white-space: nowrap;padding-left:10px;">'.$v_detail['productid'].'</td>\'+
								\'<td style="white-space: nowrap;padding-left:10px;">'.$v_detail['nama_invoice'].'</td>\'+
								\'<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['qty_jual_in_pcs'], 0, '.', ',').'</td>\'+
								\'<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['h_jual'], 0, '.', ',').'</td>\'+
								\'<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['total_bruto'], 2, '.', ',').'</td>\'+
								\'<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['total_discount'], 2, '.', ',').'</td>\'+			
								\'<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['total_netto'], 2, '.', ',').'</td>\'+
								\'</tr>\'+';
					$totbruto = $totbruto+$v_detail['total_bruto'];
					$totdisc = $totdisc+$v_detail['total_discount'];
					$totnetto = $totnetto+$v_detail['total_netto'];
				}
				$return .='\'<tr>\'+
							\'<th colspan="4" style="white-space: nowrap;padding-left:10px">Total </th>\'+
							\'<th style="white-space: nowrap;text-align:right;padding-right:10px;" >'.number_format($totbruto, 2, '.', ',').'</th>\'+
							\'<th style="white-space: nowrap;text-align:right;padding-right:10px;" >'.number_format($totdisc, 2, '.', ',').'</th>\'+
							\'<th style="white-space: nowrap;text-align:right;padding-right:10px;" >'.number_format($totnetto, 2, '.', ',').'</th>\'+	
							\'</tr>\'+
							\'</tbody>\'+
							\'</table>\'+';
		
		return $return;
	}

	function get_retur($siteid,$customerid,$salesmanid,$get_date) {
				$return = '\'<table class="table table-striped table-bordered table-condensed">\'+
							\'<thead>\'+
							\'<tr>\'+
							\'<th style="white-space: nowrap;padding-left:10px">Product ID </th>\'+
							\'<th style="white-space: nowrap;padding-left:10px" >Nama Invoice</th>\'+
							\'<th style="white-space: nowrap;text-align:right;padding-right:10px;" >QTY PCS</th>\'+
							\'<th style="white-space: nowrap;text-align:right;padding-right:10px;" >Harga Jual</th>\'+
							\'<th style="white-space: nowrap;text-align:right;padding-right:10px;" >Bruto</th>\'+
							\'<th style="white-space: nowrap;text-align:right;padding-right:10px;" >Diskon</th>\'+
							\'<th style="white-space: nowrap;text-align:right;padding-right:10px;" >Neto</th>\'+	
							\'</tr>\'+
							\'</thead>\'+
							\'<tbody>\'+';
				$q_detail = $this->db->query("
					select 
					   sls.siteid, 
					   sls.salesmanid,
					   salesamn.nama_salesman,
					   sls.customerid,
					   cst.nama_customer,
					   cst.alamat,
					   dtl.productid,
					   product.nama_invoice,
					   sum(case when dtl.flag_bonus = 0 then 'JUAL' else 'BONUS' end) as statu_order,
					   sum(case when dtl.flag_bonus = 0 then dtl.qty_kecil else dtl.qty_bonus end) as qty_jual_in_pcs,
					   sum(case when dtl.flag_bonus = 0 then dtl.qty_kecil/product.isi_besar else dtl.qty_bonus/product.isi_besar end) as qty_jual_in_carton,
					   dtl.h_jual,
					   sum(case when dtl.flag_bonus = 0 then dtl.qty_kecil*dtl.h_jual else 0 end) as total_bruto,
					   dtl.disc_cabang,
					   dtl.disc_prinsipal,
					   dtl.disc_xtra,
					   dtl.disc_cod,
					   SUM(dtl.rp_cabang) AS rp_cabang,
					   SUM(dtl.rp_prinsipal) AS rp_prinsipal,
					   SUM(dtl.rp_xtra) AS rp_xtra,
					   sum(dtl.rp_cod) as rp_cod,
					   sum(case when dtl.flag_bonus = 0 then dtl.rp_cabang+dtl.rp_prinsipal+dtl.rp_xtra+dtl.rp_cod else 0 end) as total_discount,
					   sum(case when dtl.flag_bonus = 0 then (dtl.qty_kecil*dtl.h_jual) - (dtl.rp_cabang+dtl.rp_prinsipal+dtl.rp_xtra+dtl.rp_cod) else 0 end) as total_netto
					from 
					t_sales_master sls left join
					t_sales_detail dtl on sls.siteid = dtl.siteid and sls.no_sales = dtl.no_sales left JOIN
					m_customer cst on sls.siteid = cst.siteid and sls.customerid = cst.customerid and sls.salesmanid = cst.salesmanid left JOIN
					m_sales_salesman salesamn on sls.siteid = salesamn.siteid and sls.salesmanid = salesamn.salesmanid left JOIN  
					m_product product on dtl.productid = product.productid 
					where sls.siteid = '".$siteid."' AND 
						  sls.salesmanid = '".$salesmanid."' AND
						  sls.retur = 1 AND
						  sls.tanggal >= '".$get_date."' AND
						  sls.tanggal <= '".$get_date."' AND
						  sls.customerid = '".$customerid."'
					group by sls.siteid, 
						   sls.salesmanid,
						   salesamn.nama_salesman,
						   sls.customerid,
						   cst.nama_customer,
						   cst.alamat,
						   dtl.productid,
						   product.nama_invoice,dtl.h_jual,
						   dtl.disc_cabang,
						   dtl.disc_prinsipal,
						   dtl.disc_xtra,
						   dtl.disc_cod		
				");
				$totbruto = 0;
				$totdisc  = 0;
				$totnetto = 0;
				$k_detail = $q_detail->result_array();
				foreach ($k_detail as $v_detail) {
					$return .= '\'<tr>\'+
								\'<td style="white-space: nowrap;padding-left:10px;">'.$v_detail['productid'].'</td>\'+
								\'<td style="white-space: nowrap;padding-left:10px;">'.$v_detail['nama_invoice'].'</td>\'+
								\'<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['qty_jual_in_pcs'], 0, '.', ',').'</td>\'+
								\'<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['h_jual'], 0, '.', ',').'</td>\'+
								\'<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['total_bruto'], 2, '.', ',').'</td>\'+
								\'<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['total_discount'], 2, '.', ',').'</td>\'+			
								\'<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.number_format($v_detail['total_netto'], 2, '.', ',').'</td>\'+
								\'</tr>\'+';
					$totbruto = $totbruto+$v_detail['total_bruto'];
					$totdisc = $totdisc+$v_detail['total_discount'];
					$totnetto = $totnetto+$v_detail['total_netto'];
				}
				$return .='\'<tr>\'+
							\'<th colspan="4" style="white-space: nowrap;padding-left:10px">Total </th>\'+
							\'<th style="white-space: nowrap;text-align:right;padding-right:10px;" >'.number_format($totbruto, 2, '.', ',').'</th>\'+
							\'<th style="white-space: nowrap;text-align:right;padding-right:10px;" >'.number_format($totdisc, 2, '.', ',').'</th>\'+
							\'<th style="white-space: nowrap;text-align:right;padding-right:10px;" >'.number_format($totnetto, 2, '.', ',').'</th>\'+	
							\'</tr>\'+
							\'</tbody>\'+
							\'</table>\'+';
		
		return $return;
	}
	
	function get_tagihan($siteid,$customerid,$salesmanid,$get_date) {
		$this->db->select("customerid,salesmanid, no_sales, no_ink, 
							case when SUBSTRING(no_sales, 1, 1)='R' then concat('-',bayar_tunai) else bayar_tunai end bayar_tunai, 
							case when SUBSTRING(no_sales, 1, 1)='R' then concat('-',bayar_transfer) else bayar_transfer end bayar_transfer, 
							case when SUBSTRING(no_sales, 1, 1)='R' then concat('-',bayar_giro) else bayar_giro end bayar_giro, 
							case when SUBSTRING(no_sales, 1, 1)='R' then concat('-',bayar) else bayar end bayar");	
		//$this->db->select("customerid,salesmanid, no_sales, no_ink, bayar_tunai,bayar_transfer,bayar_giro, bayar");	
		$this->db->from("t_ar_ink_detail");
		$this->db->where("siteid",$siteid);	
		$this->db->where("tgl_ink",$get_date);	
		$this->db->where("salesmanid",$salesmanid);	
		$this->db->where("customerid",$customerid);	
		
		$data = $this->db->get()->result_array();
		
		$return ='\'<table class="table table-striped table-bordered table-condensed">\'+
				  \'<thead>\'+
				  \'<tr>\'+
				  \'<th style="white-space: nowrap;">No Tagihan</th>\'+
				  \'<th style="white-space: nowrap;">No Sales</th>\'+
				  \'<th style="white-space: nowrap; text-align: right;" >Nilai Tagihan</th>\'+
				  \'<th style="white-space: nowrap; text-align: right;" >Bayar Tunai</th>\'+
				  \'<th style="white-space: nowrap; text-align: right;" >Bayar Transfer</th>\'+
				  \'<th style="white-space: nowrap; text-align: right;" >Bayar Giro</th>\'+
				  \'<th style="white-space: nowrap; text-align: right;" >Bayar Total</th>\'+
				  \'</tr>\'+
				  \'</thead>\'+
				  \'<tbody>\'+';
		
		$total_bayar = 0;
		$total_tunai = 0;
		$total_transfer = 0;
		$total_giro = 0;
		$total_all = 0; 	
		foreach ($data as $value) {
		
			$total = $value['bayar_tunai'] + $value['bayar_transfer'] + $value['bayar_giro'];
			
			$total_bayar = $total_bayar + $value['bayar'];
			$total_tunai = $total_tunai + $value['bayar_tunai'];
			$total_transfer = $total_transfer + $value['bayar_transfer'];
			$total_giro = $total_giro + $value['bayar_giro'];
			
			$total_all = $total_all + $total; 
			
			$return .= '\'<tr>\'+';
			$return .= '\'<td style="white-space: nowrap;">'.$value['no_ink'].'</td>\'+';
			$return .= '\'<td style="white-space: nowrap;">'.$value['no_sales'].'</td>\'+';
			$return .= '\'<td style="white-space: nowrap; text-align: right;">'.number_format($value['bayar'],2).'</td>\'+';
			$return .= '\'<td style="white-space: nowrap; text-align: right;">'.number_format($value['bayar_tunai'],2).'</td>\'+';
			$return .= '\'<td style="white-space: nowrap; text-align: right;">'.number_format($value['bayar_transfer'],2).'</td>\'+';
			$return .= '\'<td style="white-space: nowrap; text-align: right;">'.number_format($value['bayar_giro'],2).'</td>\'+';
			$return .= '\'<td style="white-space: nowrap; text-align: right;">'.number_format($total,2).'</td>\'+';
			$return .= '\'</tr>\'+';
		}
		$return .= '\'<tr>\'+';
		$return .= '\'<th style="white-space: nowrap;" colspan="2">Total</th>\'+';
		$return .= '\'<th style="white-space: nowrap; text-align: right;">'.number_format($total_bayar,2).'</th>\'+';
		$return .= '\'<th style="white-space: nowrap; text-align: right;">'.number_format($total_tunai,2).'</th>\'+';
		$return .= '\'<th style="white-space: nowrap; text-align: right;">'.number_format($total_transfer,2).'</th>\'+';
		$return .= '\'<th style="white-space: nowrap; text-align: right;">'.number_format($total_giro,2).'</th>\'+';
		$return .= '\'<th style="white-space: nowrap; text-align: right;">'.number_format($total_all,2).'</th>\'+';
		$return .= '\'</tr>\'+';
		$return .= '\'</tbody>\'+';
		$return .= '\'</table>\'+';
		
		return $return;
	}

	function target_detail($salesmanid,$get_date) {
		$periode='';
		$percenttot=0;
		$qtd = $this->db->query(" select 
									periode, salesmanid,target,sales,percent,ob,oa,ec,oavsob,ecvsoa,salesvsec 
								from t_productivity
								where year(periode) = year(STR_TO_DATE('".$get_date."', '%Y-%m-%d')) and 
									  month(periode) = month(STR_TO_DATE('".$get_date."', '%Y-%m-%d'))
								and salesmanid = '".$salesmanid."'");
		$data = $qtd->result_array();
		if ($data){
		foreach ($data as $value) { 
			$target 	= $value['target'];
			$sales 		= $value['sales'];
			$percent 	= $value['percent'];
			$ob 		= $value['ob'];
			$oa			= $value['oa'];
			$ec			= $value['ec'];
			$oavsob		= $value['oavsob'];
			$ecvsoa		= $value['ecvsoa'];
			$salesvsec	= $value['salesvsec'];
		}
		}else{
			$target 	= 0;
			$sales 		= 0;
			$percent 	= 0;
			$ob 		= 0;
			$oa			= 0;
			$ec			= 0;
			$oavsob		= 0;
			$ecvsoa		= 0;
			$salesvsec	= 0;
		}
		$time = strtotime($get_date);
		$newformat = date('M - Y',$time);
		
		$html ='<div><h3>Productivity Sales '.$newformat.'</h3>';
		$html .= '<table class="table table-striped table-bordered table-condensed" style="width:400px;">';
		$html .= '<thead">';
		$html .= '<tr>';
		$html .= '<th style="white-space: nowrap;text-align:left;padding-left:15px">Description</th>';
		$html .= '<th style="white-space: nowrap;text-align:right; padding-right:15px;">Value</th>';	
		$html .= '</tr>';
		$html .= '</thead">';
		$html .= '<tbody">';		
		$html .= '<tr">';
		$html .= '<td text-align:left;padding-left:15px>Value Target</td>';
		$html .= '<td style="text-align:right; padding-right:15px;">'.number_format($target, 2, '.', ',').'</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td text-align:left;padding-left:15px>Value Sales</td>';
		$html .= '<td style="text-align:right; padding-right:15px;">'.number_format($sales, 2, '.', ',').'</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td text-align:left;padding-left:15px>Percent</td>';
		$html .= '<td style="text-align:right; padding-right:15px;">'.number_format($percent, 2, '.', ',').'%</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td text-align:left;padding-left:15px>Outlet Binaan (OB)</td>';
		$html .= '<td style="text-align:right; padding-right:15px;">'.number_format($ob, 0, '.', ',').'</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td text-align:left;padding-left:15px>Outlet Aktif (OA)</td>';
		$html .= '<td style="text-align:right; padding-right:15px;">'.number_format($oa, 0, '.', ',').'</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td text-align:left;padding-left:15px>Effektif Call (EC)</td>';
		$html .= '<td style="text-align:right; padding-right:15px;">'.number_format($ec, 0, '.', ',').'</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td text-align:left;padding-left:15px>OA vs OB</td>';
		$html .= '<td style="text-align:right; padding-right:15px;">'.number_format($oavsob, 2, '.', ',').'</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td text-align:left;padding-left:15px>Rata-Rata Transaksi(EC vs OA)</td>';
		$html .= '<td style="text-align:right; padding-right:15px;">'.number_format($ecvsoa, 2, '.', ',').'</td>';		
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td text-align:left;padding-left:15px>Transaksi Per EC (Sales vs EC)</td>';
		$html .= '<td style="text-align:right; padding-right:15px;">'.number_format($salesvsec, 2, '.', ',').'</td>';		
		$html .= '</tr>';
		$html .= '</tbody>';
		$html .= '</table>';
		
		$data = '';
		$qtd = $this->db->query("
			select 
				salesmanid,
				DATE_FORMAT(periode,'%d-%m-%Y') periode,
				nama_prinsipal,
				target,
				sales,
				percent
			from t_target_prinsipal_salesman
			where DATE_FORMAT(periode,'%Y%m') = DATE_FORMAT('".$get_date."','%Y%m')
			and salesmanid = '".$salesmanid."'
		");

		$data = $qtd->result_array();
		foreach ($data as $vperiode) {
			$periode = $vperiode['periode'];
		}

		$html .= '<h3>Target Penjualan</h3><h7>Periode : '.$periode.'</h7>
				  <table class="table table-striped table-bordered table-condensed" style="width:700px;">';
		$html .= '<thead>';
		$html .= '<tr>';
		$html .= '<th style="white-space: nowrap;text-align:left;padding-left:15px">Prinsipal</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">Target</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">Sales</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">Percent</th>';
		$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody>';
		
		$total_target = 0; $total_sales = 0; $total_percent = 0;
		foreach ($data as $value) {
			
			
			$prinsipal 	= $value['nama_prinsipal'];
			$target 	= $value['target'];
			$sales 		= $value['sales'];
			if ($value['sales']==0 || $value['target']==0){
			$percent = 0;
			} else {
			$percent = ($sales/$target)*100;
			}
			
			$total_target	= $total_target +  $target;
			$total_sales 	= $total_sales +  $sales;
			
			if ($total_target==0 || $total_sales==0){
			$percenttot = 0;
			} else {
			$percenttot = ($total_sales/$total_target)*100;
			}
			
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;text-align:left;padding-left:15px">'.$prinsipal.'</td>';
			$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($target ,2, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($sales ,2, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($percent ,2, '.', ',').' %</td>';
			$html .= '</tr>';
		}
		
		$html .= '<tr>';
			$html .= '<th style="white-space: nowrap;text-align:left;padding-left:15px">Total</th>';
			$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($total_target ,2, '.', ',').'</th>';
			$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($total_sales ,2, '.', ',').'</th>';
			$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($percenttot ,2, '.', ',').' %</th>';	
			$html .= '</tr>';
		$html .= '</tbody>';
		$html .= '</table></div>';
		
		//Target per group
		$q = $this->db->query("
			select 
				salesmanid,
				DATE_FORMAT(periode,'%d-%m-%Y') periode,
				nama_group,
				target,
				sales,
				percent
			from t_target_group_salesman
			where DATE_FORMAT(periode,'%Y%m') = DATE_FORMAT('".$get_date."','%Y%m')
			and salesmanid = '".$salesmanid."'
		");

		$data = $q->result_array();
		foreach ($data as $vperiode) {
			$periode = $vperiode['periode'];
		}

		$html .= '<table class="table table-striped table-bordered table-condensed" style="width:700px;">';
		$html .= '<thead>';
		$html .= '<tr>';
		$html .= '<th style="white-space: nowrap;text-align:left;padding-left:15px">Nama Group</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">Target</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">Sales</th>';
		$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">Percent</th>';
		$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody>';
		
		$total_target = 0; $total_sales = 0; $total_percent = 0;
		foreach ($data as $value) {
			
			
			$group 	= $value['nama_group'];
			$target 	= $value['target'];
			$sales 		= $value['sales'];
			if ($value['sales']==0 || $value['target']==0){
			$percent = 0;
			} else {
			$percent = ($sales/$target)*100;
			}
			
			$total_target	= $total_target +  $target;
			$total_sales 	= $total_sales +  $sales;
			
			if ($total_target==0 || $total_sales==0){
			$percenttot = 0;
			} else {
			$percenttot = ($total_sales/$total_target)*100;
			}
			
			$html .= '<tr>';
			$html .= '<td style="white-space: nowrap;text-align:left;padding-left:15px">'.$group.'</td>';
			$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($target ,2, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($sales ,2, '.', ',').'</td>';
			$html .= '<td style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($percent ,2, '.', ',').' %</td>';
			$html .= '</tr>';
		}
		
		$html .= '<tr>';
			$html .= '<th style="white-space: nowrap;text-align:left;padding-left:15px">Total</th>';
			$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($total_target ,2, '.', ',').'</th>';
			$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($total_sales ,2, '.', ',').'</th>';
			$html .= '<th style="white-space: nowrap;text-align:right;padding-right:15px">'.number_format($percenttot ,2, '.', ',').' %</th>';	
			$html .= '</tr>';
		$html .= '</tbody>';
		$html .= '</table>';

		echo $html;
	}

	function get_tagihan_sales($siteid,$salesmanid,$get_date,$customerid) {
		$this->db->select("customerid,salesmanid, no_sales, no_ink, 
							case when SUBSTRING(no_sales, 1, 1)='R' then concat('-',bayar_tunai) else bayar_tunai end bayar_tunai, 
							case when SUBSTRING(no_sales, 1, 1)='R' then concat('-',bayar_transfer) else bayar_transfer end bayar_transfer, 
							case when SUBSTRING(no_sales, 1, 1)='R' then concat('-',bayar_giro) else bayar_giro end bayar_giro, 
							case when SUBSTRING(no_sales, 1, 1)='R' then concat('-',bayar) else bayar end bayar");
		//$this->db->select("customerid,salesmanid, no_sales, no_ink, bayar_tunai,bayar_transfer,bayar_giro, bayar");	
		$this->db->from("t_ar_ink_detail");
		$this->db->where("tgl_ink",$get_date);	
		$this->db->where("salesmanid",$salesmanid);	
		$this->db->where("customerid",$customerid);	
		
		$data = $this->db->get()->result_array();
		
		$return ='<div><h4>Tagihan</h4>';
		$return .='<table class="table table-striped table-bordered table-condensed">';
		$return .='<thead>';
		$return .='<tr>';
		$return .='<th style="white-space: nowrap;">No Tagihan</th>';
		$return .='<th style="white-space: nowrap;">No Sales</th>';
		$return .='<th style="white-space: nowrap; text-align: right;" >Nilai Tagihan</th>';
		$return .='<th style="white-space: nowrap; text-align: right;" >Bayar Tunai</th>';
		$return .='<th style="white-space: nowrap; text-align: right;" >Bayar Transfer</th>';
		$return .='<th style="white-space: nowrap; text-align: right;" >Bayar Giro</th>';
		$return .='<th style="white-space: nowrap; text-align: right;" >Bayar Total</th>';
		$return .='</tr>';
		$return .='</thead>';
		$return .='<tbody>';
		
		$total_bayar = 0;
		$total_tunai = 0;
		$total_transfer = 0;
		$total_giro = 0;
		$total_all = 0; 	
		foreach ($data as $value) {
		
			$total = $value['bayar_tunai'] + $value['bayar_transfer'] + $value['bayar_giro'];
			
			$total_bayar = $total_bayar + $value['bayar'];
			$total_tunai = $total_tunai + $value['bayar_tunai'];
			$total_transfer = $total_transfer + $value['bayar_transfer'];
			$total_giro = $total_giro + $value['bayar_giro'];
			
			$total_all = $total_all + $total; 
			
			$return .= '<tr>';
			$return .= '<td style="white-space: nowrap;">'.$value['no_ink'].'</td>';
			$return .= '<td style="white-space: nowrap;">'.$value['no_sales'].'</td>';
			$return .= '<td style="white-space: nowrap; text-align: right;">'.number_format($value['bayar'],2).'</td>';
			$return .= '<td style="white-space: nowrap; text-align: right;">'.number_format($value['bayar_tunai'],2).'</td>';
			$return .= '<td style="white-space: nowrap; text-align: right;">'.number_format($value['bayar_transfer'],2).'</td>';
			$return .= '<td style="white-space: nowrap; text-align: right;">'.number_format($value['bayar_giro'],2).'</td>';
			$return .= '<td style="white-space: nowrap; text-align: right;">'.number_format($total,2).'</td>';
			$return .= '</tr>';
		}
		$return .='<thead>';
		$return .= '<tr>';
		$return .= '<th style="white-space: nowrap;" colspan="2">Total</th>';
		$return .= '<th style="white-space: nowrap; text-align: right;">'.number_format($total_bayar,2).'</th>';
		$return .= '<th style="white-space: nowrap; text-align: right;">'.number_format($total_tunai,2).'</th>';
		$return .= '<th style="white-space: nowrap; text-align: right;">'.number_format($total_transfer,2).'</th>';
		$return .= '<th style="white-space: nowrap; text-align: right;">'.number_format($total_giro,2).'</th>';
		$return .= '<th style="white-space: nowrap; text-align: right;">'.number_format($total_all,2).'</th>';
		$return .= '</tr>';
		$return .= '</thead>';
		$return .= '</tbody>';
		$return .= '</table>';
		$return .='</div>';
		return $return;
	}

	function get_crc($siteid,$salesmanid,$get_date,$customerid) {
		$q = $this->db->query("SELECT tcrc.siteid, site.nama_site,
			   tcrc.salesmanid, sls.nama_salesman,
			   tcrc.customerid,  cust.nama_customer,
			   tcrc.categoryid, product.nama_category,
			   tcrc.brandid, product.nama_brand,
			   tcrc.productid, product.nama_invoice,
			   tcrc.qty_rata  as r1,
			   tcrc.qty_akhir  as a1,
			   tcrc.qty_saran_order  as s1, 
			   tcrc.qty_fix_order  as f1
				FROM
				t_sales_crc tcrc 
				INNER JOIN m_customer cust ON tcrc.siteid = cust.siteid and tcrc.salesmanid = cust.salesmanid and tcrc.customerid = cust.customerid
				INNER JOIN m_product product ON tcrc.productid = product.productid
				INNER JOIN m_sales_salesman  sls ON tcrc.siteid = sls.siteid and tcrc.salesmanid = sls.salesmanid 
				INNER JOIN m_setup_site site ON tcrc.siteid = site.siteid
				WHERE tcrc.siteid = '".$siteid."' AND
					  tcrc.periode = '".$get_date."' AND
					  tcrc.salesmanid = '".$salesmanid."' AND
					  tcrc.customerid = '".$customerid."'  
				");
		$data = $q->result_array();
	
		$return ='<table class="table table-striped table-bordered table-condensed" style="white-space: nowrap;">';
		$return .= '<thead>';
		$return .=	'<tr>';
		$return .='<th colspan="8" align="center">Tanggal</th>';
		$return .='<th colspan="4" align="center">'.$get_date.'</th>';
		$return .='</tr><tr>';
		$return .='<th colspan ="4" style="text-align:center;">Product ID</th>';
		$return .='<th colspan ="4" style="text-align:left;padding:10px;">Product Name</th>';
		$return .='<th style="text-align:right;padding:10px;">Rata-rata</th>';
		$return .='<th style="text-align:right;padding:10px;">Akhir</th>';
		$return .='<th style="text-align:right;padding:10px;">Saran Order</th>';
		$return .='<th style="text-align:right;padding:10px;">Fix Order</th>';
		$return .='</tr></thead><tbody>';
		
		foreach ($data as $value) {
			$return .= '<tr><td colspan ="4" style="text-align:center;">'.$value['productid'].'</td>';
			$return .= '<td colspan ="4" style="text-align:left;padding:10px;">'.$value['nama_invoice'].'</td>';
			
				$return .= '<td style="text-align:right;padding:10px;">'.$value['r1'].'</td>';
				$return .= '<td style="text-align:right;padding:10px;">'.$value['a1'].'</td>';
				$return .= '<td style="text-align:right;padding:10px;">'.$value['s1'].'</td>';
				$return .= '<td style="text-align:right;padding:10px;">'.$value['f1'].'</td>';			
			
				$return .= '</tr>';
					
		}
		
		$return .='</tbody></table>';	
		
		return $return;
	}
	
	function get_node($sid,$get_date) {
		
		$siteid = $this->get_siteid(); 
		
		$q = $this->db->query(" select z.* from (
								select case when b.customerid is null and c.no_sales is null then 'InvalidCall' 
									   when b.customerid is null and c.no_sales is not null then 'ExtraCall' 
									   when b.customerid is not null and c.no_sales is null or (c.no_sales is not null and c.retur=1) then 'Cal'
									   when b.customerid is not null and c.no_sales is not null and c.retur=0 then 'EffectiveCall' end as flag,
									   a.siteid, e.nama_site , a.salesmanid,  a.customerid , f.nama_customer, f.alamat, f.latitude, f.longitude, 
									   a.latitude_cell, a.longitude_cell, d.nama_salesman,a.check_in
								from t_sales_rrk_trans a left join t_sales_rrk b on 
								a.siteid = b.siteid and a.salesmanid = b.salesmanid and a.customerid=b.customerid and a.periode=b.periode
								left join t_sales_master c on a.siteid = c.siteid and a.salesmanid = c.salesmanid and a.customerid=c.customerid and a.periode=c.tanggal
								left join m_sales_salesman d on a.siteid = d.siteid and a.salesmanid = d.salesmanid 
								left join m_setup_site e ON a.siteid = e.siteid
								left join m_customer f on a.customerid=f.customerid
								where a.siteid ='".$siteid."' and a.periode='".$get_date."' and a.salesmanid='".$sid."'
								UNION ALL
								select 'Jadwal' as flag, rrk.siteid, site.nama_site , rrk.salesmanid,  rrk.customerid , cust.nama_customer, cust.alamat, cust.latitude, 
								cust.longitude, cust.latitude as latitude_cell, cust.longitude as longitude_cell, sls.nama_salesman, '' check_in
								from  t_sales_rrk rrk								
								INNER JOIN  m_sales_salesman sls ON rrk.siteid = sls.siteid and rrk.salesmanid = sls.salesmanid
								INNER JOIN  m_customer cust ON rrk.siteid = cust.siteid and rrk.customerid = cust.customerid and rrk.salesmanid=cust.salesmanid
								INNER JOIN  m_setup_site site ON rrk.siteid = site.siteid
								where rrk.siteid = '".$siteid."'  AND rrk.salesmanid = '".$sid."' AND rrk.periode = '".$get_date."'
								UNION ALL
								select 'Noo' as flag, cust.siteid, site.nama_site , cust.salesmanid,  cust.customerid , cust.nama_customer, 
										cust.alamat, cust.latitude, cust.longitude, cust.latitude as latitude_cell, cust.longitude as longitude_cell, 
										sls.nama_salesman, '' check_in
								from  m_customer as cust
								INNER JOIN  m_sales_salesman sls ON cust.siteid = sls.siteid and cust.salesmanid = sls.salesmanid
								INNER JOIN  m_setup_site site ON cust.siteid = site.siteid
								where cust.siteid = '".$siteid."'  AND cust.salesmanid = '".$sid."' AND cust.createdate = '".$get_date."') z
								order by z.check_in asc
								");
		//$this->db->order_by('check_in', 'ASC');
		//$q=$this->db->get();
		return $q->result_array();
	}

	
function get_list_data() {
		
		$siteid = $this->get_siteid(); 
		
		$periode = "";
		$periode = $this->input->post("get_date");
		
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
					//CONCAT(ROUND((IFNULL(x.effectivecall,0)/(IFNULL(x.cal,0)+IFNULL(x.effectivecall,0)))*100,1),' %') eff_order,
					 $sql = "select y.salesmanid,sls.nama_salesman,count(1) as jadwal, IFNULL(x.InvalidCall,0) InvalidCall,IFNULL(x.ExtraCall,0) ExtraCall,
					IFNULL(x.cal,0) cal,IFNULL(x.effectivecall,0) effectivecall,IFNULL(x.noo,0) noo, IFNULL(x.amount,0) amount,
					CONCAT(ROUND(((IFNULL(x.cal,0)+IFNULL(x.effectivecall,0))/count(1))*100,1),' %') eff_time,
					CONCAT(ROUND((IFNULL(x.effectivecall,0)/count(1))*100,1),' %') eff_order,
				   (select count(1) from t_sales_rrk a, m_customer b where a.siteid=b.siteid and a.customerid=b.customerid and a.salesmanid=b.salesmanid
				   and a.periode=y.periode and a.siteid=y.siteid and a.salesmanid=y.salesmanid and b.longitude=0 and b.latitude=0) longlatnull
			from t_sales_rrk y left join m_sales_salesman sls on y.siteid=sls.siteid and y.salesmanid=sls.salesmanid
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
                    and a.periode='".$periode."'                    
                    ) d group by d.periode,d.salesmanid,d.nama_salesman,d.salesmanid,d.JML
                ) d
              ) z group by z.salesmanid
 ) x on y.siteid=x.siteid and y.salesmanid=x.salesmanid and y.periode=x.periode
 where y.siteid = '".$siteid."' and y.periode='".$periode."' and sls.nama_salesman is not null
 group by y.salesmanid,y.nama_salesman	
						order by y.salesmanid";
				
				$result_array = $this->db->query($sql, array($offset, intval($param['rows'])));
				$response['total'] = $this->db->query($sqlcount)->num_rows();
				$response['rows'] = $result_array->result();
			} else {
				$sort = $param['sort'];
				$order = $param['order'];
					$sql = "select y.salesmanid,sls.nama_salesman,count(1) as jadwal, IFNULL(x.InvalidCall,0) InvalidCall,IFNULL(x.ExtraCall,0) ExtraCall,
							IFNULL(x.cal,0) cal,IFNULL(x.effectivecall,0) effectivecall,IFNULL(x.noo,0) noo, IFNULL(x.longlatnull,0) longlatnull, IFNULL(x.amount,0) amount,
							CONCAT(ROUND(((IFNULL(x.cal,0)+IFNULL(x.effectivecall,0))/count(1))*100,1),' %') eff_time,
							CONCAT(ROUND((IFNULL(x.effectivecall,0)/count(1))*100,1),' %') eff_order,
						   (select count(1) from t_sales_rrk a, m_customer b where a.siteid=b.siteid and a.customerid=b.customerid and a.salesmanid=b.salesmanid
						   and a.periode=y.periode and a.siteid=y.siteid and a.salesmanid=y.salesmanid and b.longitude=0 and b.latitude=0) longlatnull
				from t_sales_rrk y left join m_sales_salesman sls on y.siteid=sls.siteid and y.salesmanid=sls.salesmanid
				left join
				(select z.siteid,z.periode,z.salesmanid,z.nama_salesman, IFNULL(sum(z.INVCALL),0) as InvalidCall,IFNULL(sum(z.EXCALL),0) as ExtraCall,IFNULL(sum(z.CALL),0) as 'cal',IFNULL(sum(z.EFCALL),0) as effectivecall,
                   (select count(1) from m_customer where siteid=z.siteid and salesmanid=z.salesmanid and createdate=z.periode) noo,
				   (select count(1) from m_customer where siteid=z.siteid and salesmanid=z.salesmanid 
				   and createdate=z.periode and longitude=0 and latitude=0) longlatnull,
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
                    and a.periode='".$periode."'                    
                    ) d group by d.periode,d.salesmanid,d.nama_salesman,d.salesmanid,d.JML
                ) d
              ) z group by z.salesmanid
 ) x on y.siteid=x.siteid and y.salesmanid=x.salesmanid and y.periode=x.periode
 where y.siteid = '".$siteid."' and y.periode='".$periode."' and sls.nama_salesman is not null
 group by y.salesmanid,y.nama_salesman	
						order by $sort limit ?, ?
					";
					$sqlcount = "select y.salesmanid,sls.nama_salesman,count(1) as jadwal, IFNULL(x.InvalidCall,0) InvalidCall,IFNULL(x.ExtraCall,0) ExtraCall,
       IFNULL(x.cal,0) cal,IFNULL(x.effectivecall,0) effectivecall,IFNULL(x.noo,0) noo, IFNULL(x.amount,0) amount
       from t_sales_rrk y left join m_sales_salesman sls on y.siteid=sls.siteid and y.salesmanid=sls.salesmanid
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
                    and a.periode='".$periode."'                    
                    ) d group by d.periode,d.salesmanid,d.nama_salesman,d.salesmanid,d.JML
                ) d
              ) z group by z.salesmanid
 ) x on y.siteid=x.siteid and y.salesmanid=x.salesmanid and y.periode=x.periode
 where y.siteid = '".$siteid."' and y.periode='".$periode."' and sls.nama_salesman is not null
 group by y.salesmanid,y.nama_salesman	
						";
				
				
				
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

function get_list_data_summary() {
		
		$siteid = $this->get_siteid(); 
		
		$periode1 = "";
		$periode2 = "";
		$periode1 = $this->input->post("get_date1");
		$periode2 = $this->input->post("get_date2");
		
		//echo $periode; die();
		if ($periode1 != "" && $periode2 != "") {
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
					 $sql = "select y.salesmanid,sls.nama_salesman,count(1) as jadwal, IFNULL(x.InvalidCall,0) InvalidCall,IFNULL(x.ExtraCall,0) ExtraCall,
					IFNULL(x.cal,0) cal,IFNULL(x.effectivecall,0) effectivecall,IFNULL(x.noo,0) noo, IFNULL(x.amount,0) amount,
					CONCAT(ROUND(((IFNULL(x.cal,0)+IFNULL(x.effectivecall,0))/count(1))*100,1),' %') eff_time,
					CONCAT(ROUND((IFNULL(x.effectivecall,0)/count(1))*100,1),' %') eff_order,
				   (select count(1) from t_sales_rrk a, m_customer b where a.siteid=b.siteid and a.customerid=b.customerid and a.salesmanid=b.salesmanid
				   and a.periode=y.periode and a.siteid=y.siteid and a.salesmanid=y.salesmanid and b.longitude=0 and b.latitude=0) longlatnull
			from t_sales_rrk y left join m_sales_salesman sls on y.siteid=sls.siteid and y.salesmanid=sls.salesmanid
			left join
			(select z.siteid,z.salesmanid,z.nama_salesman, IFNULL(sum(z.INVCALL),0) as InvalidCall,IFNULL(sum(z.EXCALL),0) as ExtraCall,IFNULL(sum(z.CALL),0) as 'cal',IFNULL(sum(z.EFCALL),0) as effectivecall,
                   (select count(1) from m_customer where siteid=z.siteid and salesmanid=z.salesmanid and createdate=z.periode) noo,
                   (select sum(netto) from t_sales_master where salesmanid=z.salesmanid and tanggal between '".$periode1."' and '".$periode2."' and retur=0) as amount
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
                    and a.periode between '".$periode1."' and '".$periode2."'
                    ) d group by d.periode,d.salesmanid,d.nama_salesman,d.salesmanid,d.JML
                ) d
              ) z group by z.siteid, z.salesmanid
 ) x on y.siteid=x.siteid and y.salesmanid=x.salesmanid
 where y.siteid = '".$siteid."' and y.periode between '".$periode1."' and '".$periode2."' and sls.nama_salesman is not null
 group by y.salesmanid,y.nama_salesman	
					 order by y.salesmanid limit ?, ?
					";
					$sqlcount = "select y.salesmanid,sls.nama_salesman,count(1) as jadwal, IFNULL(x.InvalidCall,0) InvalidCall,IFNULL(x.ExtraCall,0) ExtraCall,
       IFNULL(x.cal,0) cal,IFNULL(x.effectivecall,0) effectivecall,IFNULL(x.noo,0) noo, IFNULL(x.amount,0) amount
       from t_sales_rrk y left join m_sales_salesman sls on y.siteid=sls.siteid and y.salesmanid=sls.salesmanid
left join
 (select z.siteid,z.salesmanid,z.nama_salesman, IFNULL(sum(z.INVCALL),0) as InvalidCall,IFNULL(sum(z.EXCALL),0) as ExtraCall,IFNULL(sum(z.CALL),0) as 'cal',IFNULL(sum(z.EFCALL),0) as effectivecall,
                   (select count(1) from m_customer where siteid=z.siteid and salesmanid=z.salesmanid and createdate=z.periode) noo,
                   (select sum(netto) from t_sales_master where salesmanid=z.salesmanid and tanggal between '".$periode1."' and '".$periode2."' and retur=0) as amount
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
                    and a.periode between '".$periode1."' and '".$periode2."'
                    ) d group by d.periode,d.salesmanid,d.nama_salesman,d.salesmanid,d.JML
                ) d
              ) z group by z.siteid, z.salesmanid
 ) x on y.siteid=x.siteid and y.salesmanid=x.salesmanid
 where y.siteid = '".$siteid."' and y.periode between '".$periode1."' and '".$periode2."' and sls.nama_salesman is not null
 group by y.salesmanid,y.nama_salesman	
						order by y.salesmanid";
				
				$result_array = $this->db->query($sql, array($offset, intval($param['rows'])));
				$response['total'] = $this->db->query($sqlcount)->num_rows();
				$response['rows'] = $result_array->result();
			} else {
				$sort = $param['sort'];
				$order = $param['order'];
					$sql = "select y.salesmanid,sls.nama_salesman,count(1) as jadwal, IFNULL(x.InvalidCall,0) InvalidCall,IFNULL(x.ExtraCall,0) ExtraCall,
							IFNULL(x.cal,0) cal,IFNULL(x.effectivecall,0) effectivecall,IFNULL(x.noo,0) noo, IFNULL(x.longlatnull,0) longlatnull, IFNULL(x.amount,0) amount,
							CONCAT(ROUND(((IFNULL(x.cal,0)+IFNULL(x.effectivecall,0))/count(1))*100,1),' %') eff_time,
							CONCAT(ROUND((IFNULL(x.effectivecall,0)/count(1))*100,1),' %') eff_order,
						   (select count(1) from t_sales_rrk a, m_customer b where a.siteid=b.siteid and a.customerid=b.customerid and a.salesmanid=b.salesmanid
						   and a.periode=y.periode and a.siteid=y.siteid and a.salesmanid=y.salesmanid and b.longitude=0 and b.latitude=0) longlatnull
				from t_sales_rrk y left join m_sales_salesman sls on y.siteid=sls.siteid and y.salesmanid=sls.salesmanid
				left join
				(select z.siteid,z.salesmanid,z.nama_salesman, IFNULL(sum(z.INVCALL),0) as InvalidCall,IFNULL(sum(z.EXCALL),0) as ExtraCall,IFNULL(sum(z.CALL),0) as 'cal',IFNULL(sum(z.EFCALL),0) as effectivecall,
                   (select count(1) from m_customer where siteid=z.siteid and salesmanid=z.salesmanid and createdate=z.periode) noo,
				   (select count(1) from m_customer where siteid=z.siteid and salesmanid=z.salesmanid 
				   and createdate=z.periode and longitude=0 and latitude=0) longlatnull,
                   (select sum(netto) from t_sales_master where salesmanid=z.salesmanid and tanggal between '".$periode1."' and '".$periode2."' and retur=0) as amount
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
                    and a.periode between '".$periode1."' and '".$periode2."'
                    ) d group by d.periode,d.salesmanid,d.nama_salesman,d.salesmanid,d.JML
                ) d
              ) z group by z.siteid, z.salesmanid
 ) x on y.siteid=x.siteid and y.salesmanid=x.salesmanid
 where y.siteid = '".$siteid."' and y.periode between '".$periode1."' and '".$periode2."' and sls.nama_salesman is not null
 group by y.salesmanid,y.nama_salesman	
						order by $sort limit ?, ?
					";
					$sqlcount = "select y.salesmanid,sls.nama_salesman,count(1) as jadwal, IFNULL(x.InvalidCall,0) InvalidCall,IFNULL(x.ExtraCall,0) ExtraCall,
       IFNULL(x.cal,0) cal,IFNULL(x.effectivecall,0) effectivecall,IFNULL(x.noo,0) noo, IFNULL(x.amount,0) amount
       from t_sales_rrk y left join m_sales_salesman sls on y.siteid=sls.siteid and y.salesmanid=sls.salesmanid
left join
 (select z.siteid,z.salesmanid,z.nama_salesman, IFNULL(sum(z.INVCALL),0) as InvalidCall,IFNULL(sum(z.EXCALL),0) as ExtraCall,IFNULL(sum(z.CALL),0) as 'cal',IFNULL(sum(z.EFCALL),0) as effectivecall,
                   (select count(1) from m_customer where siteid=z.siteid and salesmanid=z.salesmanid and createdate=z.periode) noo,
                   (select sum(netto) from t_sales_master where salesmanid=z.salesmanid and tanggal between '".$periode1."' and '".$periode2."' and retur=0) as amount
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
                    and a.periode between '".$periode1."' and '".$periode2."'
                    ) d group by d.periode,d.salesmanid,d.nama_salesman,d.salesmanid,d.JML
                ) d
              ) z group by z.siteid, z.salesmanid
 ) x on y.siteid=x.siteid and y.salesmanid=x.salesmanid
 where y.siteid = '".$siteid."' and y.periode between '".$periode1."' and '".$periode2."' and sls.nama_salesman is not null
 group by y.salesmanid,y.nama_salesman	
						";
				
				
				
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
	
	function get_detail_rrk($siteid,$customerid,$salesmanid,$get_date) {
	
		$this->db->select("DATE_FORMAT(a.check_in, '%H:%i:%s') check_in, DATE_FORMAT(a.check_out, '%H:%i:%s') check_out, DATE_FORMAT(a.order_time, '%H:%i:%s') order_time, 
						DATE_FORMAT(a.crc_time, '%H:%i:%s') crc_time, DATE_FORMAT(a.ink_time, '%H:%i:%s') tagihan_time, 
						(select reason from t_sales_rrk_reason where call_reasonid=a.call_reasonid) alasan,
						timediff(DATE_FORMAT(a.check_out, '%H:%i:%s'),DATE_FORMAT(a.check_in, '%H:%i:%s')) lama_kunjungan");
		$this->db->from("t_sales_rrk_trans a");
		$this->db->where("a.customerid",$customerid);
		$this->db->where("a.siteid",$siteid);
		$this->db->where("a.salesmanid",$salesmanid);
		$this->db->where("a.periode",$get_date);
		$data = $this->db->get()->row();
		return $data;
	}

	function get_image_cust($siteid,$customerid,$salesmanid) {
	
		$this->db->select("image");
		$this->db->from("m_customer_image");
		$this->db->where("customerid",$customerid);
		$this->db->where("siteid",$siteid);
		$this->db->where("salesmanid",$salesmanid);
		$this->db->order_by("id","desc");
		$this->db->limit(null, null);
		$data = $this->db->get()->row();
		return $data;
	}
	
	function get_fancy() {
	
		$customerid = $_POST['cusid'];
		$salesid = $_POST['sales'];
		
		$sql = "select image from m_customer_image where customerid = '".$customerid."' and salesmanid = '".$salesid."' order by id desc";
        $result_array = $this->db->query($sql);
        $response['images'] = $result_array->result();
        return $response;	
	
	}
	
	function get_tracking($sid,$get_date) {
		$siteid = $this->get_siteid(); 

		$q = $this->db->query(" select ifnull(latitude_cell,0) latitude_cell, ifnull(longitude_cell,0) longitude_cell,
										DATE_FORMAT(createdate,'%H:%i') waktu
								from t_tracker_salesman
								where siteid = '".$siteid."'  AND salesmanid = '".$sid."' AND periode = '".$get_date."'
								order by DATE_FORMAT(createdate,'%H:%i') asc
								");

		return $q->result_array();
	}
	
}