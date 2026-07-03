<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scheduler_model extends CI_Model
{
	function get_all_send_to() {
		$sql = "
			SELECT DISTINCT z.send_to
			FROM (
				SELECT vocp.send_to FROM v_outlet_coverage_parma vocp WHERE vocp.send_to IS NOT NULL AND vocp.send_to <> ''
				union all
				SELECT vtcdp.send_to FROM v_target_call_daily_parma vtcdp WHERE vtcdp.send_to IS NOT NULL AND vtcdp.send_to <> ''
				union all
				SELECT vtcmp.send_to FROM v_target_call_monthly_parma vtcmp WHERE vtcmp.send_to IS NOT NULL AND vtcmp.send_to <> ''
			) z
		";
		$result_array = $this->db->query($sql);
		return $result_array->result();
	}

	function get_data_send_email($email = null) {
		$result = [];
		
		$res_oc = $this->db->query("SELECT vocp.* FROM v_outlet_coverage_parma vocp" . ($email ? " WHERE vocp.send_to = '" . $email . "'" : ""));
		$result['outlet_coverage'] = $res_oc->result();

		$res_tcm = $this->db->query("SELECT vtcmp.* FROM v_target_call_monthly_parma vtcmp" . ($email ? " WHERE vtcmp.send_to = '" . $email . "'" : ""));
		$result['target_call_monthly'] = $res_tcm->result();

		$res_tcd = $this->db->query("SELECT vtcdp.* FROM v_target_call_daily_parma vtcdp" . ($email ? " WHERE vtcdp.send_to = '" . $email . "'" : "") . " ORDER BY vtcdp.periode DESC");
		$result['target_call_daily'] = $res_tcd->result();

		return $result;
	}

	function get_all_order_send_to() {
		$sql = "
			SELECT DISTINCT z.send_to
			FROM (
				SELECT vpop.send_to FROM v_pending_order_parma vpop WHERE vpop.send_to IS NOT NULL AND vpop.send_to <> ''
			) z
		";
		$result_array = $this->db->query($sql);
		return $result_array->result();
	}

	function get_data_order_send_email($email = null) {
		$result = [];

		$res_op = $this->db->query("SELECT vpop.* FROM v_pending_order_parma vpop" . ($email ? " WHERE vpop.send_to = '" . $email . "'" : "") . " ORDER BY vpop.tanggal DESC");
		$result['order_pending'] = $res_op->result();

		return $result;
	}

	public function run_daily_target($date) {
		$year = date('Y', strtotime($date));
		$month = (int)date('m', strtotime($date));

		// target salesmen
		$sqlTargets = "
			SELECT 
				mss.salesmanid,
				mss.nama_salesman,
				mss.tipe_sales,
				rmt.target_dub,
				rmt.target_hk,
				rmt.target_call_dub,
				rmt.target_call_visit
			FROM m_sales_salesman mss
			LEFT JOIN app_role ar ON ar.role_name = mss.tipe_sales
			LEFT JOIN role_mapping_target rmt ON rmt.role_id = ar.role_id
				AND rmt.tahun = ? AND rmt.bulan = ?
			WHERE mss.aktif = 1
		";
		$salesmenTargets = $this->db->query($sqlTargets, [$year, $month])->result_array();

		// planned DUB
		$sqlActualPlanned = "
			SELECT 
				tvd.salesmanid,
				COUNT(DISTINCT tsrt.customerid) AS actual_call_planned
			FROM trx_visit_detailing tvd
			JOIN t_sales_rrk_trans tsrt ON tsrt.salesmanid = tvd.salesmanid
				AND tsrt.customerid = tvd.customerid
				AND tsrt.periode = tvd.periode
			JOIN m_customer_ob mco ON mco.salesmanid = tsrt.salesmanid
				AND mco.customerid = tsrt.customerid
				AND mco.user_id = tvd.user_id
			JOIN t_sales_rrk_user tsru ON tsru.salesmanid = tsrt.salesmanid
				AND tsru.customerid = tsrt.customerid
				AND tsru.periode = tsrt.periode
				AND tsru.user_id = tvd.user_id
			WHERE DATE(tsrt.check_in) = ?
			GROUP BY tvd.salesmanid
		";
		$actualPlanned = $this->db->query($sqlActualPlanned, [$date])->result_array();
		$plannedMap = array_column($actualPlanned, 'actual_call_planned', 'salesmanid');

		// visit (planned & unplanned)
		$sqlActualVisit = "
			SELECT 
				tvd.salesmanid,
				COUNT(DISTINCT tsrt.customerid) AS actual_call_visit
			FROM trx_visit_detailing tvd
			JOIN t_sales_rrk_trans tsrt ON tsrt.salesmanid = tvd.salesmanid
				AND tsrt.customerid = tvd.customerid
				AND tsrt.periode = tvd.periode
			WHERE DATE(tsrt.check_in) = ?
			GROUP BY tvd.salesmanid
		";
		$actualVisit = $this->db->query($sqlActualVisit, [$date])->result_array();
		$visitMap = array_column($actualVisit, 'actual_call_visit', 'salesmanid');

		// rekap_dub_visit
		foreach ($salesmenTargets as $sm) {
			$smId = $sm['salesmanid'];
			$actPlanned = isset($plannedMap[$smId]) ? (int)$plannedMap[$smId] : 0;
			$actVisit = isset($visitMap[$smId]) ? (int)$visitMap[$smId] : 0;

			$insertData = [
				'tanggal' => $date,
				'salesmanid' => $smId,
				'nama_salesman' => $sm['nama_salesman'] ?? '',
				'tipe_sales' => $sm['tipe_sales'] ?? '',
				'target_dub' => isset($sm['target_dub']) ? (int)$sm['target_dub'] : 0,
				'target_hk' => isset($sm['target_hk']) ? (int)$sm['target_hk'] : 0,
				'target_call_dub' => isset($sm['target_call_dub']) ? (int)$sm['target_call_dub'] : 0,
				'target_call_visit' => isset($sm['target_call_visit']) ? (int)$sm['target_call_visit'] : 0,
				'actual_call_planned' => $actPlanned,
				'actual_call_visit' => $actVisit
			];

			$this->db->replace('rekap_dub_visit', $insertData);
		}

		// target spesialisasi
		$sqlSpecTargets = "
			SELECT 
				mss.salesmanid,
				mss.nama_salesman,
				mss.tipe_sales,
				mst.spesialisasi_id,
				mst.nama_spesialisasi,
				mst.target
			FROM m_sales_salesman mss
			LEFT JOIN app_role ar ON ar.role_name = mss.tipe_sales
			JOIN m_sales_spesialis_target mst ON mst.role_id = ar.role_id
				AND mst.tahun = ? AND mst.bulan = ?
			WHERE mss.aktif = 1
		";
		$specTargets = $this->db->query($sqlSpecTargets, [$year, $month])->result_array();

		$specMap = [];
		foreach ($specTargets as $t) {
			$key = $t['salesmanid'] . '_' . $t['spesialisasi_id'];
			$specMap[$key] = [
				'tanggal' => $date,
				'salesmanid' => $t['salesmanid'],
				'nama_salesman' => $t['nama_salesman'] ?? '',
				'tipe_sales' => $t['tipe_sales'] ?? '',
				'spesialisasi_id' => $t['spesialisasi_id'],
				'nama_spesialisasi' => $t['nama_spesialisasi'] ?? '',
				'target' => (int)$t['target'],
				'actual' => 0
			];
		}

		$sqlSpecActuals = "
			SELECT 
				tvd.salesmanid,
				mss.nama_salesman,
				mss.tipe_sales,
				rp.spesialisasi_id,
				rs.name AS nama_spesialisasi,
				COUNT(*) AS actual
			FROM trx_visit_detailing tvd
			JOIN ref_professional rp ON rp.id = tvd.user_id
			LEFT JOIN ref_spesialisasi rs ON rs.id = rp.spesialisasi_id
			LEFT JOIN t_sales_rrk_trans tsrt ON tsrt.salesmanid = tvd.salesmanid
				AND tsrt.customerid = tvd.customerid
				AND tsrt.periode = tvd.periode
			JOIN m_sales_salesman mss ON mss.salesmanid = tvd.salesmanid
			WHERE DATE(tsrt.check_in) = ?
			GROUP BY tvd.salesmanid, mss.nama_salesman, mss.tipe_sales, rp.spesialisasi_id, rs.name
		";
		$specActuals = $this->db->query($sqlSpecActuals, [$date])->result_array();

		foreach ($specActuals as $a) {
			$sp_id = $a['spesialisasi_id'];
			if (empty($sp_id)) continue;
			$key = $a['salesmanid'] . '_' . $sp_id;
			if (isset($specMap[$key])) {
				$specMap[$key]['actual'] = (int)$a['actual'];
			} else {
				$specMap[$key] = [
					'tanggal' => $date,
					'salesmanid' => $a['salesmanid'],
					'nama_salesman' => $a['nama_salesman'] ?? '',
					'tipe_sales' => $a['tipe_sales'] ?? '',
					'spesialisasi_id' => $sp_id,
					'nama_spesialisasi' => $a['nama_spesialisasi'] ?? 'Unknown Specialization',
					'target' => 0,
					'actual' => (int)$a['actual']
				];
			}
		}

		foreach ($specMap as $dataRow) {
			$this->db->replace('rekap_spesialis_visit', $dataRow);
		}

		// target produk
		$sqlProdTargets = "
			SELECT 
				mss.salesmanid,
				mss.nama_salesman,
				mss.tipe_sales,
				mpt.product_id,
				mpt.nama_invoice,
				mpt.target,
				mpt.target_qty
			FROM m_sales_salesman mss
			LEFT JOIN app_role ar ON ar.role_name = mss.tipe_sales
			JOIN m_sales_produk_target mpt ON mpt.role_id = ar.role_id
				AND mpt.tahun = ? AND mpt.bulan = ?
			WHERE mss.aktif = 1
		";
		$prodTargets = $this->db->query($sqlProdTargets, [$year, $month])->result_array();

		$prodMap = [];
		foreach ($prodTargets as $t) {
			$key = $t['salesmanid'] . '_' . $t['product_id'];
			$prodMap[$key] = [
				'tanggal' => $date,
				'salesmanid' => $t['salesmanid'],
				'nama_salesman' => $t['nama_salesman'] ?? '',
				'tipe_sales' => $t['tipe_sales'] ?? '',
				'product_id' => $t['product_id'],
				'nama_invoice' => $t['nama_invoice'] ?? '',
				'target' => (int)$t['target'],
				'target_qty' => (int)$t['target_qty'],
				'actual_visit' => 0,
				'actual_qty' => 0
			];
		}

		$sqlProdVisits = "
			SELECT 
				tvd.salesmanid, 
				tvd.array_product,
				mss.nama_salesman,
				mss.tipe_sales
			FROM trx_visit_detailing tvd
			LEFT JOIN t_sales_rrk_trans tsrt ON tsrt.salesmanid = tvd.salesmanid
				AND tsrt.customerid = tvd.customerid
				AND tsrt.periode = tvd.periode
			JOIN m_sales_salesman mss ON mss.salesmanid = tvd.salesmanid
			WHERE DATE(tsrt.check_in) = ?
		";
		$prodVisits = $this->db->query($sqlProdVisits, [$date])->result_array();

		$missingProdNames = [];
		foreach ($prodVisits as $v) {
			if (empty($v['array_product'])) continue;
			$p_ids = explode(',', $v['array_product']);
			foreach ($p_ids as $p_id) {
				$p_id = trim($p_id);
				if ($p_id === '') continue;

				$key = $v['salesmanid'] . '_' . $p_id;
				if (!isset($prodMap[$key])) {
					$prodMap[$key] = [
						'tanggal' => $date,
						'salesmanid' => $v['salesmanid'],
						'nama_salesman' => $v['nama_salesman'] ?? '',
						'tipe_sales' => $v['tipe_sales'] ?? '',
						'product_id' => $p_id,
						'nama_invoice' => '',
						'target' => 0,
						'target_qty' => 0,
						'actual_visit' => 0,
						'actual_qty' => 0
					];
					$missingProdNames[$p_id] = true;
				}
				$prodMap[$key]['actual_visit']++;
			}
		}

		$sqlProdSales = "
			SELECT 
				tsm.salesmanid, 
				tsd.productid, 
				mp.nama_invoice, 
				mss.nama_salesman,
				mss.tipe_sales,
				SUM(tsd.qty_kecil) AS actual_qty
			FROM t_sales_detail tsd
			JOIN t_sales_master tsm ON tsm.no_po = tsd.no_po
			LEFT JOIN m_product mp ON mp.productid = tsd.productid
			JOIN m_sales_salesman mss ON mss.salesmanid = tsm.salesmanid
			WHERE tsm.tanggal = ?
			  AND tsm.retur = 0
			GROUP BY tsm.salesmanid, mss.nama_salesman, mss.tipe_sales, tsd.productid, mp.nama_invoice
		";
		$prodSales = $this->db->query($sqlProdSales, [$date])->result_array();

		foreach ($prodSales as $s) {
			$key = $s['salesmanid'] . '_' . $s['productid'];
			if (isset($prodMap[$key])) {
				$prodMap[$key]['actual_qty'] = (int)$s['actual_qty'];
				if (empty($prodMap[$key]['nama_invoice'])) {
					$prodMap[$key]['nama_invoice'] = $s['nama_invoice'] ?? '';
				}
			} else {
				$prodMap[$key] = [
					'tanggal' => $date,
					'salesmanid' => $s['salesmanid'],
					'nama_salesman' => $s['nama_salesman'] ?? '',
					'tipe_sales' => $s['tipe_sales'] ?? '',
					'product_id' => $s['productid'],
					'nama_invoice' => $s['nama_invoice'] ?? 'Unknown Product',
					'target' => 0,
					'target_qty' => 0,
					'actual_visit' => 0,
					'actual_qty' => (int)$s['actual_qty']
				];
			}
		}

		$missingNamesToFetch = [];
		foreach ($prodMap as $k => $item) {
			if (empty($item['nama_invoice'])) {
				$missingNamesToFetch[$item['product_id']] = true;
			}
		}
		if (!empty($missingNamesToFetch)) {
			$p_names_res = $this->db->select('productid, nama_invoice')
								   ->from('m_product')
								   ->where_in('productid', array_keys($missingNamesToFetch))
								   ->get()->result_array();
			$nameMap = [];
			foreach ($p_names_res as $pn) {
				$nameMap[$pn['productid']] = $pn['nama_invoice'];
			}
			foreach ($prodMap as $k => $item) {
				if (empty($item['nama_invoice']) && isset($nameMap[$item['product_id']])) {
					$prodMap[$k]['nama_invoice'] = $nameMap[$item['product_id']];
				}
			}
		}

		foreach ($prodMap as $dataRow) {
			$this->db->replace('rekap_produk_visit', $dataRow);
		}

		return true;
	}
}