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
        $area = $data['areaid'] != 'null' ? ' and b.subareaid="'.$data['areaid'].'" ' : '';
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

}
