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
        $table = " ( select areaid, nama_area from m_area_areasite where regionalid = '".$data['regionalid']."' 
                        order by areaid asc
                    ) as a";
        return easy_pagging($data, $field, $table);
    }
    
    function getProductivity($data)
    {
		$sqlrecon = " 
                    replace into t_sales_absensi (periode, salesmanid, status, checkin, checkout, flag_adjust, keterangan, pjp, 
												effective_call, `call`, extra_call, invalid_call, crc, promo, competitor, `order`, sos, image)
					select a.date, a.salesman_id, case when a.type='IST' then 'S' 
													   when a.type='ICT' then 'C' 
													   when a.type='AHR' then 'H'
													   else 'HF' end tipe, 
							check_in checkin, check_out checkout, ifnull(b.flag_adjust,0) as flag_adjust,
							concat(ifnull(a.description,''),ifnull(concat('-',a.description_in),''),ifnull(concat('-',a.description_out),'')) as keterangan, 
							case when a.type='ICT' then (select count(1) from t_sales_rrk where periode=a.date and salesmanid=a.salesman_id) 
							when b.flag_adjust=1 then 0
							else 0 
							end _pjp, 0 _effective_call,
							0 _call, 0 _extra_call, 0 _invalid_call,0 _crc, 0 _promo, 0 _competitor, 0 _order, 0 _sos, a.image 
					from s_absensi a left join t_sales_absensi b on a.date=b.periode and a.salesman_id =b.salesmanid 
					where a.date between DATE_ADD(?, INTERVAL -3 DAY) and ?
					union
					select a.periode,a.salesmanid,'H' status, min(a.check_in) checkin, max(a.check_out) checkout, ifnull(b.flag_adjust,0) as flag_adjust,'' keterangan, 
						case when b.flag_adjust=0 or b.flag_adjust=1 then (select count(1) from t_sales_rrk where periode=a.periode and salesmanid=a.salesmanid) else 0 end _pjp,
						(select count(1) from t_sales_rrk where periode=a.periode and salesmanid=a.salesmanid 
							and customerid in (select customerid from t_sales_master where tanggal=a.periode and salesmanid=a.salesmanid)) as _effective_call, 
						case when b.flag_adjust=0 or b.flag_adjust=1 then 
								(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid 
								and customerid in (select customerid from t_sales_rrk where periode=a.periode and salesmanid=a.salesmanid)
								and customerid not in (select customerid from t_sales_master where tanggal=a.periode and salesmanid=a.salesmanid)) 
							else 0 end _call,
						case when b.flag_adjust=0 or b.flag_adjust=1 then 
							(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid 
							and customerid not in (select customerid from t_sales_rrk where periode=a.periode and salesmanid=a.salesmanid))
							else (select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid)
						end _extra_call,
							(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid 
							and customerid not in (select customerid from t_sales_rrk where periode=a.periode and salesmanid=a.salesmanid)
							and customerid not in (select customerid from t_sales_master where tanggal=a.periode and salesmanid=a.salesmanid))
						as _invalid_call,
					(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and crc_time is not null) _crc,
					(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and promo_time is not null) _promo,
					(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and competitor_time is not null) _competitor,
					(select count(1) from t_sales_master where tanggal=a.periode and salesmanid=a.salesmanid) _order,
					(select count(1) from t_sales_rrk_trans where periode=a.periode and salesmanid=a.salesmanid and sos_time is not null) _sos, '' image
					from t_sales_rrk_trans a left join t_sales_absensi b on a.periode=b.periode and a.salesmanid=b.salesmanid  
					where a.periode between  DATE_ADD(?, INTERVAL -3 DAY) and ?
					group by a.periode,a.salesmanid;
                    ";
			$res_ss = $this->db->query($sqlrecon, array($data['start_period'],$data['end_period'],$data['start_period'],$data['end_period']));
	

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
        $start=$data['start_period'];
        $end=$data['end_period'];
        $year=date('Y', strtotime($end));
        $month=date('m', strtotime($end));
        
        //$tipesales=$data['tipe_sales'] != 'null' ? ' and b.tipe_sales ="'.$data['tipe_sales'].'"' : '';
		//if ($tipesales==''){$tipesales='%';} else {$tipesales=$data['tipe_sales'];}
		$query = $this->db->query(" 
									select d.regionalid, d.nama_regional, c.areaid, c.nama_area, c.nama_area city,
                                            b.salesmanid,b.nama_salesman,b.tipe_sales, 
											date_format('$end','%d')-FLOOR(date_format('$end','%d')/7)-(case when date_format('$end','%d') > 15 
                                            then (select jml_libur from setup_jumlah_harilibur where tahun='$year' and bulan='$month') else 0 end) as 'MEDREP Aktif', 
                                            (select count(1) from t_sales_absensi where status='H' and salesmanid=a.salesmanid and periode between '".$start."' and '".$end."') as 'MEDREP Hadir',
                                            (select count(1) from t_sales_absensi where status='C' and salesmanid=a.salesmanid and periode between '".$start."' and '".$end."') as cuti,
                                            (select count(1) from t_sales_absensi where status='S' and salesmanid=a.salesmanid and periode between '".$start."' and '".$end."') as sakit, 
                                            round((count(1)/(date_format('$end','%d')-FLOOR(date_format('$end','%d')/7)-(case when date_format('$end','%d') > 25 
                                            then (select jml_libur from setup_jumlah_harilibur where tahun='$year' and bulan='$month') else 0 end)))*100,0) as persentasi,
                                            sum(a.pjp) as pjp, sum(a.effective_call) as effective_call, sum(a.call) as `call`, sum(a.extra_call) as extra_call, sum(a.invalid_call) as invalid_call,
                                            ifnull((
                                            select GROUP_CONCAT(x.reason_rrk SEPARATOR ' , ') from ( 
                                               SELECT z.salesmanid, CONCAT(y.reason, '(', count(y.reason), ')') AS reason_rrk 
                                               from t_sales_rrk_trans z left join t_sales_rrk_reason y on z.call_reasonid=y.call_reasonid 
                                               where z.periode between '".$start."' and '".$end."' 
                                               and z.call_reasonid is not null 
                                               group by z.salesmanid,y.reason ) x where x.salesmanid=a.salesmanid GROUP BY x.salesmanid
                                            ),'-') as rrk_keterangan,
                                            ifnull((
                                            select GROUP_CONCAT(x.reason_detailing SEPARATOR ' , ') from (
                                               SELECT z.salesmanid, CONCAT(y.reason, '(', count(y.reason), ')') AS reason_detailing
                                               from t_sales_rrk_trans z left join trx_visit_detailing y on z.periode=y.periode and
                                                    z.customerid=y.customerid and z.salesmanid=y.salesmanid 
                                               where z.periode between '".$start."' and '".$end."' 
                                               group by z.salesmanid,y.reason ) x where x.salesmanid=a.salesmanid GROUP BY x.salesmanid
                                            ),'-') as rrk_detailing, sum(e.jumlah_customer) as jumlah_customer, sum(e.total_penjualan) as total_penjualan
									from t_sales_absensi a left join m_sales_salesman b on a.salesmanid=b.salesmanid and b.aktif=1
									left join m_area_areasite c on c.areaid=b.areaid left join m_area_regional d on d.regionalid=c.regionalid
                                    left join (SELECT salesmanid,tanggal,
										  COUNT(customerid) AS total_transaksi,
										  COUNT(customerid) AS jumlah_customer,
										  SUM(netto) AS total_penjualan
										FROM t_sales_master where tanggal between '".$start."' and '".$end."'
										group by salesmanid,tanggal) as e on a.salesmanid=e.salesmanid and a.periode =e.tanggal
									where a.periode between '".$start."' and '".$end."' 
                                        and b.tipe_sales not in ('ADMIN','SPV','FC') 
                                    $strquery $area $regional
									group by d.regionalid, d.nama_regional, c.areaid, c.nama_area, b.salesmanid,b.nama_salesman,b.tipe_sales
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
        //$end=$data['periode'];
        $start=$data['start_period'];
        $end=$data['end_period'];
        //$tipesales=$data['tipe_sales'] != 'null' ? ' and salesamn.tipe_sales ="'.$data['tipe_sales'].'"' : '';
		//if ($tipesales==''){$tipesales='%';} else {$tipesales=$data['tipe_sales'];}
		$query = $this->db->query(" 
									select 
									   sls.tanggal as period,
									   sls.siteid, 
									   sls.salesmanid,
									   salesamn.nama_salesman,
									   sls.customerid,
									   cst.latest_jjid,
									   cst.cust_id_map,
									   cst.nama_customer,
									   e.nama_class as account,
									   cst.alamat,
                                       dtl.no_po,
                                       dtl.no_sales,
									   dtl.productid,
									   product.nama_invoice,
									   sum(dtl.qty_kecil) as qty_jual_in_pcs,
									   dtl.h_jual,
									   sum(dtl.qty_kecil*dtl.h_jual) as total_netto,
                                       case when dtl.status_send='3' then 'Delivered'
                                            when dtl.status_send='2' then 'Sales Order'
                                            Else 'Purchase Order'
                                        end as status
									from 
									t_sales_master sls left join
									t_sales_detail dtl on sls.siteid = dtl.siteid and sls.no_po = dtl.no_po left JOIN
									m_customer_ob cstob on sls.customerid = cstob.customerid and sls.salesmanid = cstob.salesmanid left JOIN
									m_customer cst on sls.siteid = cst.siteid and sls.customerid = cst.customerid left JOIN
									m_sales_salesman salesamn on sls.siteid = salesamn.siteid and sls.salesmanid = salesamn.salesmanid left JOIN  
									m_product product on dtl.productid = product.productid 
                                    left join m_customer_class e on e.classid=cst.classid
									where sls.tanggal between '".$start."' and '".$end."' 
                                        and salesamn.tipe_sales not in ('ADMIN','SPV','FC') $strquery
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

    function getcrc_salesman($data)
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
        $start=$data['start_period'];
        $end=$data['end_period'];
        //$tipesales=$data['tipe_sales'] != 'null' ? ' and salesamn.tipe_sales ="'.$data['tipe_sales'].'"' : '';
		//if ($tipesales==''){$tipesales='%';} else {$tipesales=$data['tipe_sales'];}
		$query = $this->db->query(" 
									select 
									   sls.periode,
									   sls.siteid, 
									   sls.salesmanid,
									   salesamn.nama_salesman,
									   sls.customerid,
									   cst.latest_jjid,
									   cst.nama_customer,
									   e.nama_class as account,
									   sls.productid,
									   product.nama_invoice,
                                       product.nama_brand,
									   case when sls.qty_akhir = 0 then sls.stock_buffer else sls.qty_akhir end qty_akhir
									from 
									t_sales_crc sls left join
									m_customer_ob cstob on sls.customerid = cstob.customerid and sls.salesmanid = cstob.salesmanid left JOIN
									m_customer cst on sls.customerid = cst.customerid left JOIN
									m_sales_salesman salesamn on sls.salesmanid = salesamn.salesmanid left JOIN  
									m_product product on sls.productid = product.productid 
                                    left join m_customer_class e on e.classid=cst.classid
									where sls.periode between '".$start."' and '".$end."' 
                                    and salesamn.tipe_sales not in ('ADMIN','SPV','FC') $strquery
									$area $regional
									group by sls.siteid, 
										   sls.salesmanid,
										   salesamn.nama_salesman,
										   sls.customerid,
										   cst.nama_customer,
										   cst.alamat,
										   sls.productid,
										   product.nama_invoice;
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
        $start=$data['start_period'];
        $end=$data['end_period'];
        //$tipesales=$data['tipe_sales'] != 'null' ? ' and b.tipe_sales ="'.$data['tipe_sales'].'"' : '';
		$query = $this->db->query(" 
                                    select a.periode, a.salesmanid, b.nama_salesman, a.customerid, c.latest_jjid, c.nama_customer, c.alamat, d.nama_area, 
                                            c.typeid as channel, e.nama_class as account,
                                            DATE_FORMAT(a.check_in, '%H:%i:%s') check_in, DATE_FORMAT(a.check_out, '%H:%i:%s') check_out,
                                            timediff(DATE_FORMAT(a.check_out, '%H:%i:%s'),DATE_FORMAT(a.check_in, '%H:%i:%s')) lama_kunjungan, 
                                            (select reason from t_sales_rrk_reason where call_reasonid=a.call_reasonid) alasan, REGEXP_REPLACE(a.keterangan, '\n', ' ') keterangan,
                                            case when 
                                                (select count(customerid) from t_sales_rrk where periode=a.periode and customerid=a.customerid)=1 and
                                                (select count(customerid) from t_sales_master where tanggal=a.periode and customerid=a.customerid)=1 
                                                then 'Effective Call' 
                                                when 
                                                (select count(customerid) from t_sales_rrk where periode=a.periode and customerid=a.customerid)=0 and
                                                (select count(customerid) from t_sales_master where tanggal=a.periode and customerid=a.customerid)=1 
                                                then 'Extra Call' 
                                                when 
                                                (select count(customerid) from t_sales_rrk where periode=a.periode and customerid=a.customerid)=0 and
                                                (select count(customerid) from t_sales_master where tanggal=a.periode and customerid=a.customerid)=0
                                                then 'Extra Call' 
                                                when 
                                                (select count(customerid) from t_sales_rrk where periode=a.periode and customerid=a.customerid)=1 and
                                                (select count(customerid) from t_sales_master where tanggal=a.periode and customerid=a.customerid)=0
                                                then 'Call' 
                                            end as flag
                                    from t_sales_rrk_trans a
                                    left join m_sales_salesman b on a.salesmanid = b.salesmanid
                                    left join m_customer c on a.customerid= c.customerid 
                                    left join m_customer_class e on e.classid=c.classid
                                    left join m_area_areasite d on c.areaid = d.areaid
                                    where a.periode between '".$start."' and '".$end."' 
									and b.tipe_sales not in ('ADMIN','SPV','FC') 
                                    and b.aktif = 1 $strquery
									$area $regional
                                    union all
                                    select a.periode, a.salesmanid, b.nama_salesman, a.customerid, c.latest_jjid, c.nama_customer, c.alamat, d.nama_area, 
                                            c.typeid as channel, e.nama_class as account,
                                            0 check_in, 0 check_out,
                                            0 lama_kunjungan, 
                                            '' alasan, '' keterangan,
                                            'FJP Tidak Terkunjungi' as flag
                                    from t_sales_rrk a
                                    left join m_sales_salesman b on a.salesmanid = b.salesmanid
                                    left join m_customer c on a.customerid= c.customerid 
                                    left join m_customer_class e on e.classid=c.classid
                                    left join m_area_areasite d on c.areaid = d.areaid
									where a.periode between '".$start."' and '".$end."' 
                                    and a.customerid not in (select customerid from t_sales_rrk_trans where periode between '".$start."' and '".$end."')
                                    and b.tipe_sales not in ('ADMIN','SPV','FC') 
                                    and b.aktif = 1 $strquery
									$area $regional 
                                    ;
									");
        return $query->result_array();
    }

    function get_detailing_parma($data)
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
        $start=$data['start_period'];
        $end=$data['end_period'];
        //$tipesales=$data['tipe_sales'] != 'null' ? ' and b.tipe_sales ="'.$data['tipe_sales'].'"' : '';
		$query = $this->db->query(" select
                                        a.periode,
                                        a.siteid,
                                        a.salesmanid,
                                        a.salesman_name,
                                        a.customerid,
                                        c.latest_jjid,
                                        a.professional_name,
                                        a.array_product,
                                        a.keterangan,
                                        a.start_detailing,
                                        a.end_detailing,
                                        a.status,
                                        a.reason,
                                        a.latitude_cell,
                                        a.longitude_cell,
                                        concat('".URL_IMAGE."', a.url_img_detailing) as url_img_detailing,
                                        concat('".URL_IMAGE."', a.url_file_serahterima) as url_file_serahterima,
                                        b.nama_salesman,
                                        c.nama_customer,c.typeid as channel,c.nama_account as account,b.nama_area as city,
                                        CASE
                                            WHEN a.status = 5 THEN 'Tidak Valid'
                                            WHEN a.status = 3 THEN 'Valid'
                                            WHEN a.status = 2 THEN 'Belum Valid'
                                            ELSE 'Butuh Verifikasi'
                                        END as status_label,
                                        COALESCE(
                                            (
                                                SELECT GROUP_CONCAT(DISTINCT CONCAT(mp.productid, ' - ', mp.nama_invoice) ORDER BY mp.nama_invoice SEPARATOR ',')
                                                FROM m_product mp
                                                WHERE FIND_IN_SET(mp.productid, a.array_product) > 0
                                            ),
                                            a.array_product
                                        ) as brands
                                    from trx_visit_detailing a
                                    left join v_gff_info b on a.salesmanid=b.salesmanid
                                    left join v_outlet_all c on a.customerid=c.customerid
                                    where a.periode between '".$start."' and '".$end."'
                                            $strquery
                                            $area $regional;");
        return $query->result_array();
    }

    function get_progress_listing($data)
    {
        if ($data['restrict_level'] == '4') {
            $strquery = " AND tpl.salesmanid IN (
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
            $strquery = " AND tpl.salesmanid IN (
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
            $strquery = " AND tpl.salesmanid IN (
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
            $where .= " tpl.periode BETWEEN '".$data['start_period']."' AND LAST_DAY('".$data['end_period']."')";
        }
        if (isset($data['regionalid']) && $data['regionalid'] != 'null') {
            $where .= " AND mss.regionalid ='".$data['regionalid']."'";
        }
        if (isset($data['areaid']) && $data['areaid'] != 'null') {
            $where .= " AND mss.areaid ='".$data['areaid']."'";
        }

		$query = $this->db->query("
            SELECT t.*
            FROM trx_progress_listing t
            JOIN (
                SELECT tpl.siteid, tpl.salesmanid, tpl.customerid, tpl.brandid, MAX(tpl.periode) AS max_periode
                FROM trx_progress_listing tpl
                LEFT JOIN m_sales_salesman mss ON mss.salesmanid = tpl.salesmanid
                WHERE ".$where . $strquery ."
                GROUP BY tpl.siteid, tpl.salesmanid, tpl.customerid, tpl.brandid
            ) latest
                ON t.siteid = latest.siteid
                AND t.salesmanid = latest.salesmanid
                AND t.customerid = latest.customerid
                AND t.brandid = latest.brandid
                AND t.periode = latest.max_periode;
        ");
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
        SELECT mss.nama_salesman, mss.salesmanid, mss.nama_area,tsa.status, tsa.periode, ap.start_time, ap.start_image, ap.end_time, ap.end_image
        FROM t_sales_absensi tsa 
            LEFT JOIN v_gff_info mss ON mss.salesmanid = tsa.salesmanid 
            left join attendance_parma ap on tsa.salesmanid=ap.salesmanid and tsa.periode=ap.periode 
            WHERE ".$where . $strquery ."
            order by tsa.periode, tsa.salesmanid 
        ");
        return $query->result_array();
    }

    function get_daily_target_call($data)
    {
        if ($data['restrict_level'] == '4') {
            $strquery = " AND a.salesmanid IN (
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
            $strquery = " AND a.salesmanid IN (
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
            $strquery = " AND a.salesmanid IN (
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
            $where .= " a.periode BETWEEN '".$data['start_period']."' AND '".$data['end_period']."'";
        }
        if (isset($data['regionalid']) && $data['regionalid'] != 'null') {
            $where .= " AND c.regionalid ='".$data['regionalid']."'";
        }
        if (isset($data['areaid']) && $data['areaid'] != 'null') {
            $where .= " AND c.areaid ='".$data['areaid']."'";
        }

		$query = $this->db->query("
            select
                z.periode AS periode,
                z.salesmanid AS parma,
                z.nama_salesman AS nama_parma,
                z.nama_area AS nama_area,
                z.send_to AS send_to,
                z.target_call AS target_call,
                z.Call AS `Call`,
                z.ExtraCall AS ExtraCall,
                z.Call + z.ExtraCall AS actual_call
            from
                (
                select
                    x.periode AS periode,
                    x.salesmanid AS salesmanid,
                    x.nama_salesman AS nama_salesman,
                    x.nama_area AS nama_area,
                    x.email AS send_to,
                    (
                    select
                        count(1)
                    from
                        t_sales_rrk
                    where
                        t_sales_rrk.salesmanid = x.salesmanid
                        and t_sales_rrk.periode = x.periode) AS target_call,
                    sum(case when x.flag = 'Call' then x.jml_outlet else 0 end) AS `Call`,
                sum(case when x.flag = 'ExtraCall' then x.jml_outlet else 0 end) AS ExtraCall
            from
                (
                select
                    a.periode AS periode,
                    a.salesmanid AS salesmanid,
                    a.nama_salesman AS nama_salesman,
                    c.nama_area AS nama_area,
                    e.email AS email,
                    a.customerid AS customerid,
                    b.nama_customer AS nama_customer,
                    b.typeid AS cluster,
                    b.nama_area AS nama_area_outlet,
                    case
                        when d.customerid is not null then 'Call'
                        else 'ExtraCall'
                    end AS flag,
                    1 AS jml_outlet
                from
                    ((((t_sales_rrk_trans a
                left join v_outlet_all b on
                    (b.customerid = a.customerid))
                left join v_gff_info c on
                    (c.salesmanid = a.salesmanid and c.tipe_sales = 'MEDREP'))
                left join t_sales_rrk d on
                    (d.customerid = a.customerid and d.salesmanid = a.salesmanid and d.periode = a.periode))
                left join (
                    select
                        m.username AS username,
                        m.name AS name,
                        m.email AS email,
                        n.areaid AS areaid,
                        o.nama_area AS nama_area
                    from
                        ((app_resource m
                    left join app_restrict_location n on
                        (m.resource_id = n.resource_id))
                    left join m_area_areasite o on
                        (o.areaid = n.areaid))
                    where
                        m.idjabatan = 24) e on
                    (e.areaid = c.areaid))
            WHERE ".$where . $strquery ."
            ) x
            group by
                x.periode,
                x.salesmanid,
                x.nama_salesman) z
        ");
        return $query->result_array();
        
    }

}
