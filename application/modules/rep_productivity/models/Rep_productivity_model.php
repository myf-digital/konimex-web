<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_productivity_model extends CI_Model
{

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
    
    function getProductivity($data)
    {

        if ($data['restrict_level']=='4'){
            $strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$data['usersession']."')
                                                )";
        }
        else if ($data['restrict_level']=='3'){
            $strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where areaid in (select distinct b.areaid from  
                                            app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                            where a.username='".$data['usersession']."')
                                                )";
        }
        else if ($data['restrict_level']=='2'){
            $strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$data['usersession']."')
                                                ) ";
        }
        else {
            $strquery = "";
        }

        $regional = $data['regionalid'] != 'null' ? ' and b.regionalid="'.$data['regionalid'].'" ' : '';
        $area = $data['areaid'] != 'null' ? ' and b.areaid="'.$data['areaid'].'" ' : '';
        $periode=$data['periode'];
        $year=$data['year'];
        $month=$data['month'];
        $tipesales=$data['tipe_sales'] != 'null' ? ' and b.tipe_sales ="'.$data['tipe_sales'].'"' : '';
		//if ($tipesales==''){$tipesales='%';} else {$tipesales=$data['tipe_sales'];}
		$query = $this->db->query(" 
									select e.regionalid, e.nama_regional, d.areaid, d.nama_area, c.subareaid, c.nama_area city,
                                            b.salesmanid,b.nama_salesman,b.tipe_sales, 
											date_format('$periode','%d')-FLOOR(date_format('$periode','%d')/7)-(case when date_format('$periode','%d') > 15 
                                            then (select jml_libur from setup_jumlah_harilibur where tahun='$year' and bulan='$month') else 0 end) as 'GFF Aktif', 
                                            (select count(1) from t_sales_absensi where status='H' and salesmanid=a.salesmanid and periode between '".$year."-".$month."-01' and LAST_DAY('".$year."-".$month."-01')) as 'GFF Hadir',
                                            (select count(1) from t_sales_absensi where status='C' and salesmanid=a.salesmanid and periode between '".$year."-".$month."-01' and LAST_DAY('".$year."-".$month."-01')) as cuti,
                                            (select count(1) from t_sales_absensi where status='S' and salesmanid=a.salesmanid and periode between '".$year."-".$month."-01' and LAST_DAY('".$year."-".$month."-01')) as sakit, 
                                            round((count(1)/(date_format('$periode','%d')-FLOOR(date_format('$periode','%d')/7)-(case when date_format('$periode','%d') > 25 
                                            then (select jml_libur from setup_jumlah_harilibur where tahun='$year' and bulan='$month') else 0 end)))*100,0) as persentasi,
                                            sum(a.pjp) as pjp, sum(a.call) as `call`, sum(a.extra_call) as extra_call,
                                            ifnull((select sum(qty_sos_gsk)/sum(qty_sos_competitor)*100 as sos_gsk 
                                            from rekap_sos_detail a join m_customer x on a.customerid=x.customerid 
                                            left join m_customer_class y on x.classid=y.classid
                                            where a.tahun='$year' and a.bulan='$month' and a.salesmanid=b.salesmanid and a.type_sos='P'
                                            and y.typeid = 'HYPERMARKET'),'0') as sos_gsk_hyp,
                                            ifnull((select sum(qty_sos_gsk)/sum(qty_sos_competitor)*100 as sos_gsk 
                                            from rekap_sos_detail a join m_customer x on a.customerid=x.customerid 
                                            left join m_customer_class y on x.classid=y.classid
                                            where a.tahun='$year' and a.bulan='$month' and a.salesmanid=b.salesmanid and a.type_sos='P'
                                            and y.typeid = 'MTI'),'0') as sos_gsk_mti,
                                            ifnull((select sum(qty_sos_gsk)/sum(qty_sos_competitor)*100 as sos_gsk 
                                            from rekap_sos_detail a join m_customer x on a.customerid=x.customerid 
                                            left join m_customer_class y on x.classid=y.classid
                                            where a.tahun='$year' and a.bulan='$month' and a.salesmanid=b.salesmanid and a.type_sos='P'
                                            and y.typeid = 'SUPERMARKET'),'0') as sos_gsk_spm,
                                            ifnull((select sum(qty_sos_gsk)/sum(qty_sos_competitor)*100 as sos_gsk 
                                            from rekap_sos_detail a join m_customer x on a.customerid=x.customerid 
                                            left join m_customer_class y on x.classid=y.classid
                                            where a.tahun='$year' and a.bulan='$month' and a.salesmanid=b.salesmanid and a.type_sos='P'
                                            and y.typeid = 'MINIMARKET'),'0') as sos_gsk_mini,
                                            ifnull((select sum(qty_sos_gsk)/sum(qty_sos_competitor)*100 as sos_gsk 
                                            from rekap_sos_detail a join m_customer x on a.customerid=x.customerid 
                                            left join m_customer_class y on x.classid=y.classid
                                            where a.tahun='$year' and a.bulan='$month' and a.salesmanid=b.salesmanid and a.type_sos='P'
                                            and y.typeid = 'MODERN PHARMA'),'0') as sos_gsk_mph,
                                            ifnull((select sum(qty_sos_gsk)/sum(qty_sos_competitor)*100 as sos_gsk 
                                            from rekap_sos_detail a join m_customer x on a.customerid=x.customerid 
                                            left join m_customer_class y on x.classid=y.classid
                                            where a.tahun='$year' and a.bulan='$month' and a.salesmanid=b.salesmanid and a.type_sos='P'
                                            and y.typeid in ('MINIMARKET','SUPERMARKET','MTI','HYPERMARKET','MODERN PHARMA')),'0') as sos_gsk_total,
                                            ifnull((
                                            select GROUP_CONCAT(x.reason_rrk SEPARATOR ' , ') from ( 
                                               SELECT z.salesmanid, CONCAT(y.reason, '(', count(y.reason), ')') AS reason_rrk 
                                               from t_sales_rrk_trans z left join t_sales_rrk_reason y on z.call_reasonid=y.call_reasonid 
                                               where z.periode between '".$year."-".$month."-01' and LAST_DAY('".$year."-".$month."-01') 
                                               and z.call_reasonid is not null 
                                               group by z.salesmanid,y.reason ) x where x.salesmanid=a.salesmanid GROUP BY x.salesmanid
                                            ),'-') as rrk_keterangan
									from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid and b.aktif=1 left join m_area_subarea c on b.subareaid=c.subareaid 
									left join m_area_areasite d on c.areaid=d.areaid left join m_area_regional e on e.regionalid=d.regionalid 
									where a.periode between '".$year."-".$month."-01' and LAST_DAY('".$year."-".$month."-01') and b.tipe_sales not in ('ADMIN','FC') $tipesales
                                    $strquery $area $regional
									group by e.regionalid, e.nama_regional, d.areaid, d.nama_area, c.subareaid, c.nama_area, b.salesmanid,b.nama_salesman,b.tipe_sales
									order by regionalid asc, areaid asc, subareaid asc, tipe_sales asc, nama_salesman asc;								
									");
        return $query->result_array();
    }

    function getOrder_salesman($data)
    {

        if ($data['restrict_level']=='4'){
            $strquery = " and sls.salesmanid in (select salesmanid from m_sales_salesman where subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$data['usersession']."')
                                                )";
        }
        else if ($data['restrict_level']=='3'){
            $strquery = " and sls.salesmanid in (select salesmanid from m_sales_salesman where areaid in (select distinct b.areaid from  
                                            app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                            where a.username='".$data['usersession']."')
                                                )";
        }
        else if ($data['restrict_level']=='2'){
            $strquery = " and sls.salesmanid in (select salesmanid from m_sales_salesman where regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$data['usersession']."')
                                                ) ";
        }
        else {
            $strquery = "";
        }

        $regional = $data['regionalid'] != 'null' ? ' and salesamn.regionalid="'.$data['regionalid'].'" ' : '';
        $area = $data['areaid'] != 'null' ? ' and salesamn.areaid="'.$data['areaid'].'" ' : '';
        $periode=$data['periode'];
        $year=$data['year'];
        $month=$data['month'];
        $tipesales=$data['tipe_sales'] != 'null' ? ' and salesamn.tipe_sales ="'.$data['tipe_sales'].'"' : '';
		//if ($tipesales==''){$tipesales='%';} else {$tipesales=$data['tipe_sales'];}
		$query = $this->db->query(" 
									select 
									   sls.tanggal as period,
									   sls.siteid, 
									   sls.salesmanid,
									   salesamn.nama_salesman,
									   sls.customerid,
									   cst.kode_outlet,
									   cst.nama_customer,
									   e.nama_class as account,
									   cst.alamat,
                                       sls.no_po,
									   dtl.productid,
									   product.nama_invoice,
									   sum(case when dtl.flag_bonus = 0 or dtl.flag_bonus is null then dtl.qty_kecil else dtl.qty_bonus end) as qty_jual_in_pcs,
									   dtl.h_jual,
									   sum(case when dtl.flag_bonus = 0 or dtl.flag_bonus is null then (dtl.qty_kecil*dtl.h_jual) - (dtl.rp_cabang+dtl.rp_prinsipal+dtl.rp_xtra+dtl.rp_cod) else 0 end) as total_netto
									from 
									t_sales_master sls left join
									t_sales_detail dtl on sls.siteid = dtl.siteid and sls.no_po = dtl.no_po left JOIN
									m_customer_ob cstob on sls.customerid = cstob.customerid and sls.salesmanid = cstob.salesmanid left JOIN
									m_customer cst on sls.siteid = cst.siteid and sls.customerid = cst.customerid left JOIN
									m_sales_salesman salesamn on sls.siteid = salesamn.siteid and sls.salesmanid = salesamn.salesmanid left JOIN  
									m_product product on dtl.productid = product.productid 
                                    left join m_customer_class e on e.classid=cst.classid
									where sls.tanggal between '".$year."-".$month."-01' and LAST_DAY('".$year."-".$month."-01') and salesamn.tipe_sales not in ('ADMIN','FC') $tipesales $strquery
									$area $regional
									group by sls.siteid, 
										   sls.salesmanid,
										   salesamn.nama_salesman,
										   sls.customerid,
										   cst.nama_customer,
										   cst.alamat,
										   dtl.productid,
										   product.nama_invoice,dtl.h_jual;
									");
        return $query->result_array();
    }

    function getVisit_salesman($data)
    {

        if ($data['restrict_level']=='4'){
            $strquery = " and b.salesmanid in (select salesmanid from m_sales_salesman where subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$data['usersession']."')
                                                )";
        }
        else if ($data['restrict_level']=='3'){
            $strquery = " and b.salesmanid in (select salesmanid from m_sales_salesman where areaid in (select distinct b.areaid from  
                                            app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                            where a.username='".$data['usersession']."')
                                                )";
        }
        else if ($data['restrict_level']=='2'){
            $strquery = " and b.salesmanid in (select salesmanid from m_sales_salesman where regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$data['usersession']."')
                                                ) ";
        }
        else {
            $strquery = "";
        }

        $regional = $data['regionalid'] != 'null' ? ' and b.regionalid="'.$data['regionalid'].'" ' : '';
        $area = $data['areaid'] != 'null' ? ' and b.areaid="'.$data['areaid'].'" ' : '';
        $periode=$data['periode'];
        $year=$data['year'];
        $month=$data['month'];
        $tipesales=$data['tipe_sales'] != 'null' ? ' and b.tipe_sales ="'.$data['tipe_sales'].'"' : '';
		$query = $this->db->query(" 
									select a.periode, a.salesmanid, a.nama_salesman, a.customerid, c.kode_outlet, c.nama_customer, c.alamat, d.nama_area, 
                                            c.typeid as channel, e.nama_class as account,
											DATE_FORMAT(a.check_in, '%H:%i:%s') check_in, DATE_FORMAT(a.check_out, '%H:%i:%s') check_out,
											timediff(DATE_FORMAT(a.check_out, '%H:%i:%s'),DATE_FORMAT(a.check_in, '%H:%i:%s')) lama_kunjungan, 
											(select reason from t_sales_rrk_reason where call_reasonid=a.call_reasonid) alasan, REGEXP_REPLACE(a.keterangan, '\n', ' ') keterangan
									from t_sales_rrk_trans a
									left join m_sales_salesman b on a.salesmanid = b.salesmanid
									left join m_customer c on a.customerid= c.customerid 
                                    left join m_customer_class e on e.classid=c.classid
									left join m_area_subarea d on c.subareaid = d.subareaid
									where a.periode between '".$year."-".$month."-01' and LAST_DAY('".$year."-".$month."-01') 
									and b.tipe_sales not in ('ADMIN','FC') $tipesales $strquery
									$area $regional
									");
        return $query->result_array();
    }

}
