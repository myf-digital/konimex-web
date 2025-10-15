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

	function get_data_send_email($email) {
		$result = [];
		
		$res_oc = $this->db->query("SELECT vocp.* FROM v_outlet_coverage_parma vocp WHERE vocp.send_to = '" . $email . "'");
		$result['outlet_coverage'] = $res_oc->result();

		$res_tcd = $this->db->query("SELECT vtcdp.* FROM v_target_call_daily_parma vtcdp WHERE vtcdp.send_to = '" . $email . "'");
		$result['target_call_daily'] = $res_tcd->result();

		$res_tcm = $this->db->query("SELECT vtcmp.* FROM v_target_call_monthly_parma vtcmp WHERE vtcmp.send_to = '" . $email . "'");
		$result['target_call_monthly'] = $res_tcm->result();

		return $result;
	}
}