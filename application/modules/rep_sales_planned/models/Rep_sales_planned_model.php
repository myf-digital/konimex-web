<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_sales_planned_model extends CI_Model
{
	function getSalesRekapPjp($salesmanid) {

		$query = $this->db->query("select minggu, hari, count(*) as jml from t_sales_setup_rrk where salesmanid='".$salesmanid."' group by minggu, hari");
		return $query->result_array();
	}

	function getSales($salesmanid) {

		$query = $this->db->query("
            select
                s.salesmanid,
                s.nama_salesman,
                s.tipe_sales as posisi,
                s.aktif,
                s.supervisorid,
                mss.nama_salesman as supervisor,
                r.nama_regional,
                a.nama_area,
                ss.nama_area as nama_subarea
            from m_sales_salesman s
            left join m_sales_salesman mss on mss.salesmanid = s.supervisorid
            left join m_area_regional r on r.regionalid = s.regionalid
            left join m_area_areasite a on a.areaid = s.areaid
            left join m_area_subarea ss on ss.subareaid = s.subareaid
            where s.salesmanid='".$salesmanid."'"
        );
		return $query->row();
	}

	function getSalesPlannedDetail($salesmanid, $month = null, $year = null) {
		$where_periode = "";
		if (!empty($month) && !empty($year)) {
			$periode_prefix = $year . "-" . str_pad($month, 2, "0", STR_PAD_LEFT);
			$where_periode = " AND a.periode LIKE '" . $this->db->escape_str($periode_prefix) . "%' ";
		} else {
			$periode_prefix = date("Y-m");
			$where_periode = " AND a.periode LIKE '" . $this->db->escape_str($periode_prefix) . "%' ";
        }

		$sql = "
			SELECT 
				a.periode,
				a.customerid,
				mc.nama_customer as outlet,
				mc.kode_outlet,
				a.user_id,
				p.nama_professional as user_name
			FROM t_sales_rrk_user a
			LEFT JOIN m_customer mc ON mc.customerid = a.customerid
			LEFT JOIN ref_professional p ON a.user_id = p.id
			WHERE a.salesmanid = ? $where_periode
			ORDER BY a.periode ASC
		";
		$query = $this->db->query($sql, array($salesmanid));
		return $query->result_array();
	}

    public function load($data)
    {
		$strquery = "";
        if (!empty($data['salesmanid'])) {
            $strquery .= " and a.salesmanid IN (".$data['salesmanid'].") ";
        }

        $sql = "
                select
                    a.req_no,
                    a.siteid,
                    a.periode,
                    a.salesmanid,
                    a.keterangan,
                    a.status,
                    a.reason,
                    a.created_by,
                    a.created_date,
                    a.modified_by,
                    a.modified_date,
                    b.nama_salesman
                from req_pjp_daily a
                left join m_sales_salesman b on a.salesmanid = b.salesmanid
                where a.siteid = 'KNX01' $strquery
                order by
                case a.status
                    when 1 then 1
                    when 3 then 2
                    when 5 then 3
                    else 4
                end, a.req_no desc
            ";

		$result = $this->db->query($sql);
		return $result->result_array();
    }
}