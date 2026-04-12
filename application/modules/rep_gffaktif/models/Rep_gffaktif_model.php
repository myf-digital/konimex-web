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
        $table = " ( select subareaid, nama_area from m_area_subarea where regionalid = '".$data['regionalid']."' 
                        order by subareaid asc
                    ) as a";
        return easy_pagging($data, $field, $table);
    }

	function get_header($periode) {
		
		$query = $this->db->query("call days_of_month('".$periode."');");

		return $query->result_array();
		
	}
	
	function get_salesman($periode,$until,$position,$idjabatan,$usersession,$restrictlevel,$regionalid,$areaid) {

		/*if ($idjabatan=='2' or $idjabatan=='3')
			$strquery = " and a.salesmanid in (select distinct b.salesmanid from mapping_ram_aas a join mapping_sales_aas_aam b on a.aas_aam_tss_tsm=b.aas_aam_tss_tsm where a.ram_rsm = '".$usersession."') ";
		else if($idjabatan=='16' or $idjabatan=='17'){
			$strquery = " and a.salesmanid in (select salesmanid from mapping_sales_aas_aam where aas_aam_tss_tsm='".$usersession."') ";
		}else{
            $strquery = "";
        }*/
        if ($restrictlevel=='4'){
            $strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."')
                                                )";
        }
        else if ($restrictlevel=='3'){
            $strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where areaid in (select distinct b.areaid from  
                                            app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                            where a.username='".$usersession."')
                                                )";
        }
        else if ($restrictlevel=='2'){
            $strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."')
                                                ) ";
        }
        else {
            $strquery = "";
        }

        if ($position=='null'){
            $val = '%';
        }else{
            $val = $position;
        }

		$regional = $regionalid != 'null' ? ' and d.regionalid="'.$regionalid.'" ' : '';
        $area = $areaid != 'null' ? ' and b.areaid="'.$areaid.'" ' : '';

		$q = $this->db->query("
                                select a.salesmanid, a.nama_salesman, a.tipe_sales,d.nama_regional,c.nama_area,b.nama_area city from 
                                m_sales_salesman a left join m_area_subarea b on b.subareaid = a.subareaid 
                                left join m_area_areasite c on c.areaid=a.areaid
                                left join m_area_regional d on d.regionalid = a.regionalid
                                where a.salesmanid in (select distinct salesmanid from t_sales_absensi
                                                     where a.tipe_sales like '$val' and periode>=DATE_FORMAT('".$periode."','%Y-%m-%d') and periode<=DATE_FORMAT('".$until."','%Y-%m-%d')) ".$regional.$area.$strquery."
                                order by a.tipe_sales, d.regionalid,c.areaid,b.subareaid;            
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
        if ($restrictlevel=='4'){
            $strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."')
                                                )";
        }
        else if ($restrictlevel=='3'){
            $strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where areaid in (select distinct b.areaid from  
                                            app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                            where a.username='".$usersession."')
                                                )";
        }
        else if ($restrictlevel=='2'){
            $strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."')
                                                ) ";
        }
        else {
            $strquery = "";
        }

        if($position=='null'){
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
        if ($restrictlevel=='4'){
            $strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."')
                                                )";
        }
        else if ($restrictlevel=='3'){
            $strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where areaid in (select distinct b.areaid from  
                                            app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                            where a.username='".$usersession."')
                                                )";
        }
        else if ($restrictlevel=='2'){
            $strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."')
                                                ) ";
        }
        else {
            $strquery = "";
        }

        if($position=='null'){
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
        if ($restrictlevel=='4'){
            $strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."')
                                                )";
        }
        else if ($restrictlevel=='3'){
            $strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where areaid in (select distinct b.areaid from  
                                            app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                            where a.username='".$usersession."')
                                                )";
        }
        else if ($restrictlevel=='2'){
            $strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$usersession."')
                                                ) ";
        }
        else {
            $strquery = "";
        }

        if($position=='null'){
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
        if ($data['restrict_level'] == '4') {
            $strquery = " AND tsa.salesmanid IN (
                            SELECT salesmanid
                            FROM m_sales_salesman
                            WHERE subareaid IN (
                                SELECT DISTINCT b.subareaid
                                FROM app_resource a
                                LEFT JOIN app_restrict_location b ON a.resource_id = b.resource_id 
                                WHERE a.username='".$data['usersession']."'
                            )
                        )";
        } else if ($data['restrict_level'] == '3') {
            $strquery = " AND tsa.salesmanid IN (
                            SELECT salesmanid
                            FROM m_sales_salesman
                            WHERE areaid IN (
                                SELECT DISTINCT b.areaid
                                FROM app_resource a
                                LEFT JOIN app_restrict_location b ON a.resource_id = b.resource_id 
                                WHERE a.username='".$data['usersession']."'
                            )
                        )";
        } else if ($data['restrict_level'] == '2') {
            $strquery = " AND tsa.salesmanid IN (
                            SELECT salesmanid
                            FROM m_sales_salesman
                            WHERE regionalid IN (
                                SELECT DISTINCT b.regionalid
                                FROM app_resource a
                                LEFT JOIN app_restrict_location b ON a.resource_id=b.resource_id 
                                WHERE a.username='".$data['usersession']."'
                            )
                        ) ";
        } else $strquery = "";

        $where = "";
        if (isset($data['start_period']) && isset($data['end_period'])) {
            $where .= " tsa.periode BETWEEN '".$data['start_period']."' AND LAST_DAY('".$data['end_period']."')";
        }
        if (isset($data['regionalid']) && $data['regionalid'] != 'null') {
            $where .= " AND mss.regionalid ='".$data['regionalid']."'";
        }
        if (isset($data['areaid']) && $data['areaid'] != 'null') {
            $where .= " AND mss.areaid ='".$data['areaid']."'";
        }
        
		$query = $this->db->query("
        SELECT mss.nama_salesman, mss.salesmanid, mss.nama_area,tsa.status, tsa.periode, ap.start_time, 
                ap.start_image, ap.end_time, ap.end_image
        FROM t_sales_absensi tsa 
            LEFT JOIN v_gff_info mss ON mss.salesmanid = tsa.salesmanid 
            left join attendance_parma ap on tsa.salesmanid=ap.salesmanid and tsa.periode=ap.periode 
            WHERE ".$where . $strquery ."and mss.nama_salesman NOT LIKE '%Tester%'
            order by tsa.periode, tsa.salesmanid 
        ");
        return $query->result_array();
    }    
}
