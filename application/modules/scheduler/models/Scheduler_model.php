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

		$sqlVisits = "
			SELECT 
				tvd.salesmanid,
				mss.nama_salesman,
				mss.tipe_sales,
				tvd.customerid,
				mc.nama_customer,
				tvd.user_id AS professional_id,
				rp.nama_professional,
				tsrt.check_in,
				tsrt.check_out,
				tsrt.keterangan,
				tvd.array_product,
				CASE WHEN mco.customerid IS NOT NULL THEN 1 ELSE 0 END AS is_planned
			FROM trx_visit_detailing tvd
			JOIN t_sales_rrk_trans tsrt ON tsrt.salesmanid = tvd.salesmanid
				AND tsrt.customerid = tvd.customerid
				AND tsrt.periode = tvd.periode
			JOIN m_sales_salesman mss ON mss.salesmanid = tvd.salesmanid
			LEFT JOIN m_customer mc ON mc.customerid = tvd.customerid
			LEFT JOIN ref_professional rp ON rp.id = tvd.user_id
			LEFT JOIN m_customer_ob mco ON mco.salesmanid = tsrt.salesmanid
				AND mco.customerid = tsrt.customerid
				AND mco.user_id = tvd.user_id
			WHERE DATE(tsrt.check_in) = ?
		";
		$visits = $this->db->query($sqlVisits, [$date])->result_array();

		$salesmanVisits = [];
		foreach ($visits as $v) {
			$salesmanVisits[$v['salesmanid']][] = $v;
		}

		foreach ($salesmenTargets as $sm) {
			$smId = $sm['salesmanid'];

			if ($sm['target_dub'] === null) {
				continue;
			}

			if (!isset($salesmanVisits[$smId])) {
				continue;
			}
			
			$targetRow = [
				'tanggal' => $date,
				'salesmanid' => $smId,
				'nama_salesman' => $sm['nama_salesman'] ?? '',
				'tipe_sales' => $sm['tipe_sales'] ?? '',
				'customerid' => null,
				'nama_customer' => null,
				'professional_id' => null,
				'nama_professional' => null,
				'check_in' => null,
				'check_out' => null,
				'duration' => null,
				'keterangan' => null,
				'array_product' => null,
				'target_dub' => (int)$sm['target_dub'],
				'target_hk' => (int)$sm['target_hk'],
				'target_call_dub' => (int)$sm['target_call_dub'] * (int)$sm['target_dub'],
				'target_call_visit' => (int)$sm['target_call_visit'] * (int)$sm['target_hk'],
				'actual_call_planned' => 0,
				'actual_call_visit' => 0
			];
			$this->db->replace('rekap_dub_visit', $targetRow);

			foreach ($salesmanVisits[$smId] as $v) {
				$visitRow = [
					'tanggal' => $date,
					'salesmanid' => $smId,
					'nama_salesman' => $v['nama_salesman'] ?? '',
					'tipe_sales' => $v['tipe_sales'] ?? '',
					'customerid' => $v['customerid'],
					'nama_customer' => $v['nama_customer'] ?? '',
					'professional_id' => !empty($v['professional_id']) ? (int)$v['professional_id'] : null,
					'nama_professional' => $v['nama_professional'] ?? '',
					'check_in' => $v['check_in'],
					'check_out' => $v['check_out'],
					'duration' => $this->calculate_php_duration($v['check_in'], $v['check_out']),
					'keterangan' => $v['keterangan'],
					'array_product' => $v['array_product'],
					'target_dub' => 0,
					'target_hk' => 0,
					'target_call_dub' => 0,
					'target_call_visit' => 0,
					'actual_call_planned' => (int)$v['is_planned'],
					'actual_call_visit' => 1
				];
				$this->db->replace('rekap_dub_visit', $visitRow);
			}
		}

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

		$specTargetsMap = [];
		foreach ($specTargets as $t) {
			$specTargetsMap[$t['salesmanid']][$t['spesialisasi_id']] = (int)$t['target'];
		}

		$sqlSpecVisits = "
			SELECT 
				tvd.salesmanid,
				mss.nama_salesman,
				mss.tipe_sales,
				rp.spesialisasi_id,
				rs.name AS nama_spesialisasi,
				tvd.customerid,
				mc.nama_customer,
				tvd.user_id AS professional_id,
				rp.nama_professional,
				tsrt.check_in,
				tsrt.check_out,
				tsrt.keterangan,
				tvd.array_product
			FROM trx_visit_detailing tvd
			JOIN ref_professional rp ON rp.id = tvd.user_id
			LEFT JOIN ref_spesialisasi rs ON rs.id = rp.spesialisasi_id
			JOIN t_sales_rrk_trans tsrt ON tsrt.salesmanid = tvd.salesmanid
				AND tsrt.customerid = tvd.customerid
				AND tsrt.periode = tvd.periode
			JOIN m_sales_salesman mss ON mss.salesmanid = tvd.salesmanid
			LEFT JOIN m_customer mc ON mc.customerid = tvd.customerid
			WHERE DATE(tsrt.check_in) = ?
		";
		$specVisits = $this->db->query($sqlSpecVisits, [$date])->result_array();

		$salesmanSpecVisits = [];
		foreach ($specVisits as $v) {
			$sp_id = $v['spesialisasi_id'];
			if (empty($sp_id)) continue;
			$smId = $v['salesmanid'];

			if (isset($specTargetsMap[$smId][$sp_id])) {
				$salesmanSpecVisits[$smId][$sp_id][] = $v;
			}
		}

		foreach ($specTargetsMap as $smId => $specs) {
			foreach ($specs as $spId => $targetVal) {
				if (!isset($salesmanSpecVisits[$smId][$spId])) {
					continue;
				}

				$smName = '';
				$smType = '';
				$specName = '';
				foreach ($specTargets as $t) {
					if ($t['salesmanid'] == $smId && $t['spesialisasi_id'] == $spId) {
						$smName = $t['nama_salesman'];
						$smType = $t['tipe_sales'];
						$specName = $t['nama_spesialisasi'];
						break;
					}
				}

				$targetRow = [
					'tanggal' => $date,
					'salesmanid' => $smId,
					'nama_salesman' => $smName,
					'tipe_sales' => $smType,
					'spesialisasi_id' => $spId,
					'nama_spesialisasi' => $specName,
					'customerid' => null,
					'nama_customer' => null,
					'professional_id' => null,
					'nama_professional' => null,
					'check_in' => null,
					'check_out' => null,
					'duration' => null,
					'keterangan' => null,
					'array_product' => null,
					'target' => $targetVal,
					'actual' => 0
				];
				$this->db->replace('rekap_spesialis_visit', $targetRow);

				foreach ($salesmanSpecVisits[$smId][$spId] as $v) {
					$visitRow = [
						'tanggal' => $date,
						'salesmanid' => $smId,
						'nama_salesman' => $v['nama_salesman'] ?? '',
						'tipe_sales' => $v['tipe_sales'] ?? '',
						'spesialisasi_id' => $spId,
						'nama_spesialisasi' => $v['nama_spesialisasi'] ?? '',
						'customerid' => $v['customerid'],
						'nama_customer' => $v['nama_customer'] ?? '',
						'professional_id' => !empty($v['professional_id']) ? (int)$v['professional_id'] : null,
						'nama_professional' => $v['nama_professional'] ?? '',
						'check_in' => $v['check_in'],
						'check_out' => $v['check_out'],
						'duration' => $this->calculate_php_duration($v['check_in'], $v['check_out']),
						'keterangan' => $v['keterangan'],
						'array_product' => $v['array_product'],
						'target' => 0,
						'actual' => 1
					];
					$this->db->replace('rekap_spesialis_visit', $visitRow);
				}
			}
		}

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

		$prodTargetsMap = [];
		foreach ($prodTargets as $t) {
			$prodTargetsMap[$t['salesmanid']][$t['product_id']] = [
				'nama_invoice' => $t['nama_invoice'],
				'nama_salesman' => $t['nama_salesman'],
				'tipe_sales' => $t['tipe_sales'],
				'target' => (int)$t['target'],
				'target_qty' => (int)$t['target_qty']
			];
		}

		$sqlProdVisits = "
			SELECT 
				tvd.salesmanid,
				mss.nama_salesman,
				mss.tipe_sales,
				tvd.array_product,
				tvd.customerid,
				mc.nama_customer,
				tvd.user_id AS professional_id,
				rp.nama_professional,
				tsrt.check_in,
				tsrt.check_out,
				tsrt.keterangan
			FROM trx_visit_detailing tvd
			JOIN t_sales_rrk_trans tsrt ON tsrt.salesmanid = tvd.salesmanid
				AND tsrt.customerid = tvd.customerid
				AND tsrt.periode = tvd.periode
			JOIN m_sales_salesman mss ON mss.salesmanid = tvd.salesmanid
			LEFT JOIN m_customer mc ON mc.customerid = tvd.customerid
			LEFT JOIN ref_professional rp ON rp.id = tvd.user_id
			WHERE DATE(tsrt.check_in) = ?
		";
		$prodVisits = $this->db->query($sqlProdVisits, [$date])->result_array();

		$salesmanProdVisits = [];
		foreach ($prodVisits as $v) {
			if (empty($v['array_product'])) continue;
			$smId = $v['salesmanid'];
			$p_ids = explode(',', $v['array_product']);
			foreach ($p_ids as $p_id) {
				$p_id = trim($p_id);
				if ($p_id === '') continue;

				if (isset($prodTargetsMap[$smId][$p_id])) {
					$salesmanProdVisits[$smId][$p_id][] = $v;
				}
			}
		}

		$sqlProdSales = "
			SELECT 
				tsm.salesmanid, 
				mss.nama_salesman,
				mss.tipe_sales,
				tsd.productid AS product_id, 
				mp.nama_invoice, 
				tsm.customerid,
				mc.nama_customer,
				tsm.no_po,
				SUM(tsd.qty_kecil) AS actual_qty,
				tsm.tanggal AS check_in
			FROM t_sales_detail tsd
			JOIN t_sales_master tsm ON tsm.no_po = tsd.no_po
			LEFT JOIN m_product mp ON mp.productid = tsd.productid
			JOIN m_sales_salesman mss ON mss.salesmanid = tsm.salesmanid
			LEFT JOIN m_customer mc ON mc.customerid = tsm.customerid
			WHERE tsm.tanggal = ?
			  AND tsm.retur = 0
			GROUP BY tsm.salesmanid, mss.nama_salesman, mss.tipe_sales, tsd.productid, mp.nama_invoice, tsm.customerid, mc.nama_customer, tsm.no_po, tsm.tanggal
		";
		$prodSales = $this->db->query($sqlProdSales, [$date])->result_array();

		$salesmanProdSales = [];
		foreach ($prodSales as $s) {
			$smId = $s['salesmanid'];
			$p_id = $s['product_id'];
			
			if (isset($prodTargetsMap[$smId][$p_id])) {
				$salesmanProdSales[$smId][$p_id][] = $s;
			}
		}

		foreach ($prodTargetsMap as $smId => $prods) {
			foreach ($prods as $pId => $t) {
				$hasVisits = isset($salesmanProdVisits[$smId][$pId]);
				$hasSales = isset($salesmanProdSales[$smId][$pId]);

				if (!$hasVisits && !$hasSales) {
					continue;
				}

				$targetRow = [
					'tanggal' => $date,
					'salesmanid' => $smId,
					'nama_salesman' => $t['nama_salesman'] ?? '',
					'tipe_sales' => $t['tipe_sales'] ?? '',
					'product_id' => $pId,
					'nama_invoice' => $t['nama_invoice'] ?? '',
					'customerid' => null,
					'nama_customer' => null,
					'professional_id' => null,
					'nama_professional' => null,
					'check_in' => null,
					'check_out' => null,
					'duration' => null,
					'keterangan' => null,
					'target' => $t['target'],
					'target_qty' => $t['target_qty'],
					'actual_visit' => 0,
					'actual_qty' => 0
				];
				$this->db->replace('rekap_produk_visit', $targetRow);

				if ($hasVisits) {
					foreach ($salesmanProdVisits[$smId][$pId] as $v) {
						$visitRow = [
							'tanggal' => $date,
							'salesmanid' => $smId,
							'nama_salesman' => $v['nama_salesman'] ?? '',
							'tipe_sales' => $v['tipe_sales'] ?? '',
							'product_id' => $pId,
							'nama_invoice' => $t['nama_invoice'] ?? '',
							'customerid' => $v['customerid'],
							'nama_customer' => $v['nama_customer'] ?? '',
							'professional_id' => !empty($v['professional_id']) ? (int)$v['professional_id'] : null,
							'nama_professional' => $v['nama_professional'] ?? '',
							'check_in' => $v['check_in'],
							'check_out' => $v['check_out'],
							'duration' => $this->calculate_php_duration($v['check_in'], $v['check_out']),
							'keterangan' => $v['keterangan'],
							'target' => 0,
							'target_qty' => 0,
							'actual_visit' => 1,
							'actual_qty' => 0
						];
						$this->db->replace('rekap_produk_visit', $visitRow);
					}
				}

				if ($hasSales) {
					foreach ($salesmanProdSales[$smId][$pId] as $s) {
						$salesRow = [
							'tanggal' => $date,
							'salesmanid' => $smId,
							'nama_salesman' => $s['nama_salesman'] ?? '',
							'tipe_sales' => $s['tipe_sales'] ?? '',
							'product_id' => $pId,
							'nama_invoice' => $t['nama_invoice'] ?? '',
							'customerid' => $s['customerid'],
							'nama_customer' => $s['nama_customer'] ?? '',
							'professional_id' => null,
							'nama_professional' => '',
							'check_in' => $s['check_in'],
							'check_out' => null,
							'duration' => null,
							'keterangan' => 'Sales PO: ' . ($s['no_po'] ?? ''),
							'target' => 0,
							'target_qty' => 0,
							'actual_visit' => 0,
							'actual_qty' => (int)$s['actual_qty']
						];
						$this->db->replace('rekap_produk_visit', $salesRow);
					}
				}
			}
		}

		return true;
	}

	private function calculate_php_duration($start, $end) {
		if (empty($start) || empty($end)) return null;
		$diff = strtotime($end) - strtotime($start);
		if ($diff < 0) return null;
		$h = floor($diff / 3600);
		$m = floor(($diff % 3600) / 60);
		$s = $diff % 60;
		return sprintf('%02d:%02d:%02d', $h, $m, $s);
	}
}