<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_gffaktif_model extends CI_Model
{

    /*public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('mapping_promo_active');
        if (!empty($id)) {
            $data['idpromo'] = $id;
        }
        $data["created_by"] = $data["usersession"];
        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $data["created_date"] = $datetime->datetime;
        unset($data["usersession"]);

        return $this->db->insert('mapping_promo_active', $data);
    }*/

    public function load($data)
    {
        $field = " a.* ";
        $table = " ( select a.*, b.nama_class account from mapping_promo_active a left join m_customer_class b on a.classid=b.classid where promo <> 'Promo GSK' ) as a";
        return easy_pagging($data, $field, $table);
    }

    public function get_regional($data)
    {

        $field = " a.* ";
        $table = " ( select regionalid, nama_regional from m_area_regional 
                        order by regionalid asc
                    ) as a";
        return easy_pagging($data, $field, $table);
    }

    public function get_area($data)
    {
        $field = " a.* ";
        $table = " ( select areaid, nama_area from m_area_areasite where regionalid = '".$data['regionalid']."' 
                        order by areaid asc
                    ) as a";
        return easy_pagging($data, $field, $table);
    }

    public function get_city($data)
    {
        $field = " a.* ";
        $where = " 1=1 ";
        if (!empty($data['areaid'])) {
            $where .= " and areaid = '".$data['areaid']."' ";
        } else if (!empty($data['regionalid'])) {
            $where .= " and regionalid = '".$data['regionalid']."' ";
        }
        $table = " ( select subareaid, nama_area from m_area_subarea where $where order by nama_area asc
                    ) as a";
        return easy_pagging($data, $field, $table);
    }

	function get_header($periode) {
		
		$query = $this->db->query("call days_of_month('".$periode."');");

		return $query->result_array();
		
	}
	
	function get_salesman($periode,$until,$position,$idjabatan,$usersession,$restrictlevel,$regionalid,$areaid,$subareaid=null) {

        $strquery = "";
        if (!empty($restrictlevel)) {
            $restrict_query = get_salesman_restrict($usersession, $restrictlevel);
            if ($restrict_query) {
                $strquery = " and a.salesmanid in (" . $restrict_query . ")";
            }
        }

        if (empty($position) || $position == 'null') {
            $val = '%';
        }else{
            $val = $position;
        }

        $regional = "";
        if (!empty($regionalid) && $regionalid != 'null') {
            $regional = " and a.salesmanid in (select distinct salesmanid from m_salesman_area where regionalid = '".$this->db->escape_str($regionalid)."') ";
        }
        $area = "";
        if (!empty($areaid) && $areaid != 'null') {
            $area = " and a.salesmanid in (select distinct salesmanid from m_salesman_area where areaid = '".$this->db->escape_str($areaid)."') ";
        }
        $subarea = "";
        if (!empty($subareaid) && $subareaid != 'null') {
            $subarea = " and a.salesmanid in (select distinct salesmanid from m_salesman_area where subareaid = '".$this->db->escape_str($subareaid)."') ";
        }

		$q = $this->db->query("
                                select 
                                    a.salesmanid, 
                                    a.nama_salesman, 
                                    a.tipe_sales,
                                    (
                                        select group_concat(distinct r.nama_regional order by r.nama_regional asc separator ', ')
                                        from m_salesman_area msa
                                        join m_area_regional r on r.regionalid = msa.regionalid
                                        where msa.salesmanid = a.salesmanid
                                    ) as nama_regional,
                                    (
                                        select group_concat(distinct ar.nama_area order by ar.nama_area asc separator ', ')
                                        from m_salesman_area msa
                                        join m_area_areasite ar on msa.areaid = ar.areaid
                                        where msa.salesmanid = a.salesmanid
                                    ) as nama_area,
                                    (
                                        select group_concat(distinct sa.nama_area order by sa.nama_area asc separator ', ')
                                        from m_salesman_area msa
                                        join m_area_subarea sa on msa.subareaid = sa.subareaid
                                        where msa.salesmanid = a.salesmanid
                                    ) as nama_subarea 
                                from m_sales_salesman a
                                where a.salesmanid in (select distinct salesmanid from t_sales_absensi
                                                     where a.tipe_sales like '$val' and periode>=DATE_FORMAT('".$periode."','%Y-%m-%d') and periode<=DATE_FORMAT('".$until."','%Y-%m-%d')) ".$regional.$area.$subarea.$strquery."
                                order by a.tipe_sales;            
                            ");
		return $q->result_array();
	}

	function get_salesman_aktif($salesmanid,$date) {

		$query = $this->db->query("
			select periode, salesmanid, status, case when status='H' then 1 else 0 end aktif from t_sales_absensi
			where salesmanid='".$salesmanid."' and DATE_FORMAT(periode,'%Y-%m-%d') = '".$date."';
				   ");
		return $query->result_array();
	}

    function get_salesman_aktif_new($salesmanid,$date) {

        $query = $this->db->query("
            select periode, salesmanid, status, case when status='H' then 1 else 0 end aktif from t_sales_absensi
            where salesmanid='".$salesmanid."' and DATE_FORMAT(periode,'%Y-%m-%d') = '".$date."';
                   ");
        return $query->row();
    }

    function get_salesman_non_aktif($sids, $periode, $until) {

        $sids = join('","',$sids);

        $sql = 'select a.periode, a.salesmanid, a.status, a.keterangan, a.image, b.nama_salesman, b.tipe_sales from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid where a.status <> "H" and a.salesmanid in ("'.$sids.'") and periode between "'.$periode.'" and "'.$until.'" ';

        $query = $this->db->query($sql);

        return $query->result_array();
    }

    function get_salesman_aktif_sum($salesmanid,$date1,$date2) {
		$query = $this->db->query("
			select sum(a.aktif) sumaktif,sum(a.hf) sumhf,sum(a.s) sums,sum(a.c) sumc from (
            select periode, salesmanid, status, 
            case when status='H' then 1 else 0 end aktif,  
            case when status='HF' then 1 else 0 end hf,
            case when status='S' then 1 else 0 end s,
            case when status='C' then 1 else 0 end c
            from t_sales_absensi
			where salesmanid='".$salesmanid."' and periode>=DATE_FORMAT('".$date1."','%Y-%m-%d') and periode<=DATE_FORMAT('".$date2."','%Y-%m-%d')) a");

		return $query->result_array();
	}

    function get_salesman_aktif_sum_new($salesmanid,$date1,$date2) {
        $query = $this->db->query("
            select sum(a.aktif) sumaktif,sum(a.hf) sumhf,sum(a.s) sums,sum(a.c) sumc from (
            select periode, salesmanid, status, 
            case when status='H' then 1 else 0 end aktif,  
            case when status='HF' then 1 else 0 end hf,
            case when status='S' then 1 else 0 end s,
            case when status='C' then 1 else 0 end c
            from t_sales_absensi
            where salesmanid='".$salesmanid."' and periode>=DATE_FORMAT('".$date1."','%Y-%m-%d') and periode<=DATE_FORMAT('".$date2."','%Y-%m-%d')) a");

        return $query->row();
    }

    function get_salesman_sum_daily($date1,$position,$restrictlevel,$usersession) {
        $strquery = "";
        if (!empty($restrictlevel)) {
            $restrict_query = get_salesman_restrict($usersession, $restrictlevel);
            if ($restrict_query) {
                $strquery = " and a.salesmanid in (" . $restrict_query . ")";
            }
        }

        if(empty($position) || $position=='null'){
            $val='%';
        }else{
            $val=$position;
        }
		$query = $this->db->query("
			select sum(a.aktif) sumaktif,sum(a.hf) sumhf,sum(a.s) sums,sum(a.c) sumc from (
            select a.periode, a.salesmanid, a.status, 
            case when a.status='H' then 1 else 0 end aktif,  
            case when a.status='HF' then 1 else 0 end hf,
            case when a.status='S' then 1 else 0 end s,
            case when a.status='C' then 1 else 0 end c
            from t_sales_absensi a join m_sales_salesman b on a.salesmanid=b.salesmanid
			where b.tipe_sales like '$val' and a.periode=DATE_FORMAT('".$date1."','%Y-%m-%d') $strquery) a");
		return $query->result_array();
	}

    function get_salesman_sum_daily_new($date1,$position,$restrictlevel,$usersession) {
        $strquery = "";
        if (!empty($restrictlevel)) {
            $restrict_query = get_salesman_restrict($usersession, $restrictlevel);
            if ($restrict_query) {
                $strquery = " and a.salesmanid in (" . $restrict_query . ")";
            }
        }

        if(empty($position) || $position=='null'){
            $val='%';
        }else{
            $val=$position;
        }
        $query = $this->db->query("
            select sum(a.aktif) sumaktif,sum(a.hf) sumhf,sum(a.s) sums,sum(a.c) sumc from (
            select a.periode, a.salesmanid, a.status, 
            case when a.status='H' then 1 else 0 end aktif,  
            case when a.status='HF' then 1 else 0 end hf,
            case when a.status='S' then 1 else 0 end s,
            case when a.status='C' then 1 else 0 end c
            from t_sales_absensi a join m_sales_salesman b on a.salesmanid=b.salesmanid
            where b.tipe_sales like '$val' and a.periode=DATE_FORMAT('".$date1."','%Y-%m-%d') $strquery) a");
        return $query->row();
    }

    function get_salesman_sum_periode($date1,$date2,$position,$restrictlevel,$usersession) {
        $strquery = "";
        if (!empty($restrictlevel)) {
            $restrict_query = get_salesman_restrict($usersession, $restrictlevel);
            if ($restrict_query) {
                $strquery = " and a.salesmanid in (" . $restrict_query . ")";
            }
        }

        if(empty($position) || $position=='null'){
            $val='%';
        }else{
            $val=$position;
        }
		$query = $this->db->query("
			select sum(a.aktif) sumaktif,sum(a.hf) sumhf,sum(a.s) sums,sum(a.c) sumc from (
            select a.periode, a.salesmanid, a.status, 
            case when a.status='H' then 1 else 0 end aktif,  
            case when a.status='HF' then 1 else 0 end hf,
            case when a.status='S' then 1 else 0 end s,
            case when a.status='C' then 1 else 0 end c
            from t_sales_absensi a join m_sales_salesman b on a.salesmanid=b.salesmanid
			where b.tipe_sales like '$val' and a.periode>=DATE_FORMAT('".$date1."','%Y-%m-%d') and a.periode<=DATE_FORMAT('".$date2."','%Y-%m-%d') $strquery) a");
		return $query->result_array();
	}

    function get_attendance_parma($data)
    {
        $strquery = "";
        if (!empty($data["restrict_level"])) {
            $restrict_query = get_salesman_restrict($data["usersession"], $data["restrict_level"]);
            if ($restrict_query) {
                $strquery = " AND tsa.salesmanid IN (" . $restrict_query . ")";
            }
        }

        $where = " 1=1 ";
        if (!empty($data['start_period']) && !empty($data['end_period'])) {
            $where .= " AND tsa.periode BETWEEN '".$data['start_period']."' AND LAST_DAY('".$data['end_period']."')";
        }
        if (!empty($data['regionalid']) && $data['regionalid'] != 'null') {
            $where .= " AND tsa.salesmanid IN (select distinct salesmanid from m_salesman_area where regionalid = '".$this->db->escape_str($data['regionalid'])."')";
        }
        if (!empty($data['areaid']) && $data['areaid'] != 'null') {
            $where .= " AND tsa.salesmanid IN (select distinct salesmanid from m_salesman_area where areaid = '".$this->db->escape_str($data['areaid'])."')";
        }
        if (!empty($data['subareaid']) && $data['subareaid'] != 'null') {
            $where .= " AND tsa.salesmanid IN (select distinct salesmanid from m_salesman_area where subareaid = '".$this->db->escape_str($data['subareaid'])."')";
        }
        
		$query = $this->db->query("
        SELECT mss.nama_salesman, mss.salesmanid, mss.nama_area,tsa.status, tsa.periode, ap.start_time, 
                ap.start_image, ap.end_time, ap.end_image
        FROM t_sales_absensi tsa 
            LEFT JOIN v_gff_info mss ON mss.salesmanid = tsa.salesmanid 
            left join attendance_parma ap on tsa.salesmanid=ap.salesmanid and tsa.periode=ap.periode 
            WHERE ".$where . $strquery ."
            order by tsa.periode, tsa.salesmanid 
        ");
        return $query->result_array();
    }    
}
