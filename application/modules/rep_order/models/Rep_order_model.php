<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_order_model extends CI_Model
{

    public function load($data)
    {

        if ($data["restrict_level"]=='4'){
            $strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$data["usersession"]."')
                                                )";
        }
        else if ($data["restrict_level"]=='3'){
            $strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where areaid in (select distinct b.areaid from  
                                            app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                            where a.username='".$data["usersession"]."')
                                                )";
        }
        else if ($data["restrict_level"]=='2'){
            $strquery = " and a.salesmanid in (select salesmanid from m_sales_salesman where regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$data["usersession"]."')
                                                ) ";
        }
        else {
            $strquery = "";
        }

        $regional = $data['regionalid'] != '' ? ' and b.regionalid="'.$data['regionalid'].'" ' : '';
        $area = $data['areaid'] != '' ? ' and b.areaid="'.$data['areaid'].'" ' : '';
        $subarea = $data['subareaid'] != '' ? ' and b.subareaid="'.$data['subareaid'].'" ' : '';

        $field = " a.* ";
        $table = " (
                select
                    z.salesmanid,
                    b.nama_salesman,
                    b.tipe_sales,
                    c.nama_area city,
                    sum(z.pjp) as _jadwal,
                    sum(z.call) as _call,
                    sum(z.extra_call) as _extra_call,
                    sum(z.crc) as _crc,
                    (select sum(tsm.netto) from t_sales_master tsm 
                    where tsm.tanggal between '".(@$data["get_date1"] ??today())."' and '".(@$data["get_date2"] ??today())."' 
                    and tsm.salesmanid=z.salesmanid) _order
                from t_sales_absensi z
                left join m_sales_salesman b on z.salesmanid=b.salesmanid
                left join m_area_subarea c on c.subareaid=b.subareaid
                where z.periode between '".(@$data["get_date1"] ??today())."' and '".(@$data["get_date2"] ??today())."' ".$regional.$area.$subarea.$strquery."
                group by z.salesmanid, b.nama_salesman
                ) a
            ";

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
        $table = " ( select subareaid, nama_area from m_area_subarea where areaid = '".$data['areaid']."' 
                        order by subareaid asc
                    ) as a";
        return easy_pagging($data, $field, $table);
    }

    function get_order($siteid,$customerid,$salesmanid,$get_date1,$get_date2) {
        $return = '\'<table class="table table-striped table-bordered table-condensed">\'+
                    \'<thead>\'+
                    \'<tr>\'+
                    \'<th style="white-space: nowrap;padding-left:10px">Product ID </th>\'+
                    \'<th style="white-space: nowrap;padding-left:10px" >Nama Invoice</th>\'+
                    \'<th style="white-space: nowrap;text-align:right;padding-right:10px;" >QTY PCS</th>\'+
                    \'<th style="white-space: nowrap;text-align:right;padding-right:10px;" >Selling Price</th>\'+
                    \'<th style="white-space: nowrap;text-align:right;padding-right:10px;" >Gross</th>\'+
                    \'<th style="white-space: nowrap;text-align:right;padding-right:10px;" >Discount</th>\'+
                    \'<th style="white-space: nowrap;text-align:right;padding-right:10px;" >Net</th>\'+	
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
                sum(case when dtl.flag_bonus = 0 or dtl.flag_bonus is null then 'JUAL' else 'BONUS' end) as statu_order,
                sum(case when dtl.flag_bonus = 0 or dtl.flag_bonus is null then dtl.qty_kecil else dtl.qty_bonus end) as qty_jual_in_pcs,
                sum(case when dtl.flag_bonus = 0 or dtl.flag_bonus is null then dtl.qty_kecil/product.isi_besar else dtl.qty_bonus/product.isi_besar end) as qty_jual_in_carton,
                dtl.h_jual,
                sum(case when dtl.flag_bonus = 0 or dtl.flag_bonus is null then dtl.qty_kecil*dtl.h_jual else 0 end) as total_bruto,
                dtl.disc_cabang,
                dtl.disc_prinsipal,
                dtl.disc_xtra,
                dtl.disc_cod,
                SUM(dtl.rp_cabang) AS rp_cabang,
                SUM(dtl.rp_prinsipal) AS rp_prinsipal,
                SUM(dtl.rp_xtra) AS rp_xtra,
                sum(dtl.rp_cod) as rp_cod,
                sum(case when dtl.flag_bonus = 0 or dtl.flag_bonus is null then dtl.rp_cabang+dtl.rp_prinsipal+dtl.rp_xtra+dtl.rp_cod else 0 end) as total_discount,
                sum(case when dtl.flag_bonus = 0 or dtl.flag_bonus is null then (dtl.qty_kecil*dtl.h_jual) - (dtl.rp_cabang+dtl.rp_prinsipal+dtl.rp_xtra+dtl.rp_cod) else 0 end) as total_netto,
                sls.no_po,
                concat('".URL_IMAGE."', sls.url_img_po) as url_img_po
            from t_sales_master sls
            left join t_sales_detail dtl on sls.siteid = dtl.siteid and sls.no_po = dtl.no_po
            left join m_customer cst on sls.siteid = cst.siteid and sls.customerid = cst.customerid and sls.salesmanid = cst.salesmanid
            left join m_sales_salesman salesamn on sls.siteid = salesamn.siteid and sls.salesmanid = salesamn.salesmanid
            left join m_product product on dtl.productid = product.productid 
            where sls.siteid = '".$siteid."' AND 
                sls.salesmanid = '".$salesmanid."' AND
                sls.retur = 0 AND
                date(sls.tanggal) between '".$get_date1."' and '".$get_date2."'
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

    function get_order_all($data) {

        $salesmanid = $data['salesmanid'];
        $get_date1 = $data['startdate1'];
        $get_date2 = $data['startdate2'];
        
        $q_detail = $this->db->query("
            select 
                sls.salesmanid,
                salesamn.nama_salesman,
                salesamn.tipe_sales,
                sls.customerid,
                cst.nama_customer,
                cst.alamat,
                sls.no_sales,
                sls.tanggal,
                dtl.productid,
                product.nama_invoice,
                sum(case when dtl.flag_bonus = 0 or dtl.flag_bonus is null then 'JUAL' else 'BONUS' end) as statu_order,
                sum(case when dtl.flag_bonus = 0 or dtl.flag_bonus is null then dtl.qty_kecil else dtl.qty_bonus end) as qty_jual_in_pcs,
                sum(case when dtl.flag_bonus = 0 or dtl.flag_bonus is null then dtl.qty_kecil/product.isi_besar else dtl.qty_bonus/product.isi_besar end) as qty_jual_in_carton,
                dtl.h_jual,
                sum(case when dtl.flag_bonus = 0 or dtl.flag_bonus is null then dtl.qty_kecil*dtl.h_jual else 0 end) as total_bruto,
                dtl.disc_cabang,
                dtl.disc_prinsipal,
                dtl.disc_xtra,
                dtl.disc_cod,
                SUM(dtl.rp_cabang) AS rp_cabang,
                SUM(dtl.rp_prinsipal) AS rp_prinsipal,
                SUM(dtl.rp_xtra) AS rp_xtra,
                sum(dtl.rp_cod) as rp_cod,
                sum(case when dtl.flag_bonus = 0 or dtl.flag_bonus is null then dtl.rp_cabang+dtl.rp_prinsipal+dtl.rp_xtra+dtl.rp_cod else 0 end) as total_discount,
                sum(case when dtl.flag_bonus = 0 or dtl.flag_bonus is null then (dtl.qty_kecil*dtl.h_jual) - (dtl.rp_cabang+dtl.rp_prinsipal+dtl.rp_xtra+dtl.rp_cod) else 0 end) as total_netto,
                sls.no_po,
                sls.status_po,
                concat('".URL_IMAGE."', sls.url_img_po) as url_img_po
            from t_sales_master sls
            left join t_sales_detail dtl on sls.siteid = dtl.siteid and sls.no_po = dtl.no_po
            left join m_customer cst on sls.siteid = cst.siteid and sls.customerid = cst.customerid and sls.salesmanid = cst.salesmanid
            left join m_sales_salesman salesamn on sls.siteid = salesamn.siteid and sls.salesmanid = salesamn.salesmanid
            left join m_product product on dtl.productid = product.productid 
            where sls.salesmanid = '".$salesmanid."' AND
                sls.retur = 0 AND
                date(sls.tanggal) between '".$get_date1."' and '".$get_date2."'
            group by 
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

        return $q_detail->result();
    }

    function get_order_all_xls($data) {

        $salesmanid = $data['salesmanid'];
        $startdate1 = $data['startdate1'];
        $startdate2 = $data['startdate2'];
        
        $q_detail = $this->db->query("
            select 
                sls.salesmanid,
                salesamn.nama_salesman,
                salesamn.tipe_sales,
                sls.customerid,
                cst.nama_customer,
                cst.alamat,
                cst.telp, 
                areasite.nama_area as area,
                sls.no_sales,
                sls.tanggal,
                dtl.productid,
                product.nama_invoice,
                sum(case when dtl.flag_bonus = 0 or dtl.flag_bonus is null then 'JUAL' else 'BONUS' end) as statu_order,
                sum(case when dtl.flag_bonus = 0 or dtl.flag_bonus is null then dtl.qty_kecil else dtl.qty_bonus end) as qty_jual_in_pcs,
                sum(case when dtl.flag_bonus = 0 or dtl.flag_bonus is null then dtl.qty_kecil/product.isi_besar else dtl.qty_bonus/product.isi_besar end) as qty_jual_in_carton,
                dtl.h_jual,
                sum(case when dtl.flag_bonus = 0 or dtl.flag_bonus is null then dtl.qty_kecil*dtl.h_jual else 0 end) as total_bruto,
                dtl.disc_cabang,
                dtl.disc_prinsipal,
                dtl.disc_xtra,
                dtl.disc_cod,
                SUM(dtl.rp_cabang) AS rp_cabang,
                SUM(dtl.rp_prinsipal) AS rp_prinsipal,
                SUM(dtl.rp_xtra) AS rp_xtra,
                sum(dtl.rp_cod) as rp_cod,
                sum(case when dtl.flag_bonus = 0 or dtl.flag_bonus is null then dtl.rp_cabang+dtl.rp_prinsipal+dtl.rp_xtra+dtl.rp_cod else 0 end) as total_discount,
                sum(case when dtl.flag_bonus = 0 or dtl.flag_bonus is null then (dtl.qty_kecil*dtl.h_jual) - (dtl.rp_cabang+dtl.rp_prinsipal+dtl.rp_xtra+dtl.rp_cod) else 0 end) as total_netto,
                sls.no_po,
                sls.status_po,
                concat('".URL_IMAGE."', sls.url_img_po) as url_img_po
            from t_sales_master sls
            left join t_sales_detail dtl on sls.siteid = dtl.siteid and sls.no_po = dtl.no_po
            left join m_customer cst on sls.siteid = cst.siteid and sls.customerid = cst.customerid and sls.salesmanid = cst.salesmanid
            left join m_sales_salesman salesamn on sls.siteid = salesamn.siteid and sls.salesmanid = salesamn.salesmanid
            left join m_product product on dtl.productid = product.productid
            left join m_area_areasite areasite on areasite.areaid = cst.areaid 
            where sls.salesmanid = '".$salesmanid."' AND sls.retur = 0
                AND date(sls.tanggal) between '".$startdate1."' and '".$startdate2."'
            group by 
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
        return $q_detail->result();	
    }

    function get_order_all_salesman_xls($data) {

        $startdate1 = $data['start'];
        $startdate2 = $data['end'];

        if ($data["restrict_level"]=='4'){
            $strquery = " and salesamn.salesmanid in (select salesmanid from m_sales_salesman where subareaid in (select distinct b.subareaid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$data["usersession"]."')
                                                )";
        }
        else if ($data["restrict_level"]=='3'){
            $strquery = " and salesamn.salesmanid in (select salesmanid from m_sales_salesman where areaid in (select distinct b.areaid from  
                                            app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                            where a.username='".$data["usersession"]."')
                                                )";
        }
        else if ($data["restrict_level"]=='2'){
            $strquery = " and salesamn.salesmanid in (select salesmanid from m_sales_salesman where regionalid in (select distinct b.regionalid from  
                                                app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                                                where a.username='".$data["usersession"]."')
                                                ) ";
        }
        else {
            $strquery = "";
        }

        $regional = $data['regionalid'] != 'null' ? ' and salesamn.regionalid="'.$data['regionalid'].'" ' : '';
        $area = $data['areaid'] != 'null' ? ' and salesamn.areaid="'.$data['areaid'].'" ' : '';
        $subarea = $data['subareaid'] != 'null' ? ' and salesamn.subareaid="'.$data['subareaid'].'" ' : '';
        
        $q_detail = $this->db->query("
            select 
                sls.salesmanid,
                salesamn.nama_salesman,
                salesamn.tipe_sales,
                sls.customerid,
                cst.nama_customer,
                cst.alamat,
                cst.telp, 
                areasite.nama_area as area,
                sls.no_sales,
                sls.tanggal,
                dtl.productid,
                product.nama_invoice,
                sum(case when dtl.flag_bonus = 0 or dtl.flag_bonus is null then 'JUAL' else 'BONUS' end) as statu_order,
                sum(case when dtl.flag_bonus = 0 or dtl.flag_bonus is null then dtl.qty_kecil else dtl.qty_bonus end) as qty_jual_in_pcs,
                sum(case when dtl.flag_bonus = 0 or dtl.flag_bonus is null then dtl.qty_kecil/product.isi_besar else dtl.qty_bonus/product.isi_besar end) as qty_jual_in_carton,
                dtl.h_jual,
                sum(case when dtl.flag_bonus = 0 or dtl.flag_bonus is null then dtl.qty_kecil*dtl.h_jual else 0 end) as total_bruto,
                dtl.disc_cabang,
                dtl.disc_prinsipal,
                dtl.disc_xtra,
                dtl.disc_cod,
                SUM(dtl.rp_cabang) AS rp_cabang,
                SUM(dtl.rp_prinsipal) AS rp_prinsipal,
                SUM(dtl.rp_xtra) AS rp_xtra,
                sum(dtl.rp_cod) as rp_cod,
                sum(case when dtl.flag_bonus = 0 or dtl.flag_bonus is null then dtl.rp_cabang+dtl.rp_prinsipal+dtl.rp_xtra+dtl.rp_cod else 0 end) as total_discount,
                sum(case when dtl.flag_bonus = 0 or dtl.flag_bonus is null then (dtl.qty_kecil*dtl.h_jual) - (dtl.rp_cabang+dtl.rp_prinsipal+dtl.rp_xtra+dtl.rp_cod) else 0 end) as total_netto,
                sls.no_po,
                sls.status_po,
                concat('".URL_IMAGE."', sls.url_img_po) as url_img_po
            from t_sales_master sls
            left join t_sales_detail dtl on sls.siteid = dtl.siteid and sls.no_po = dtl.no_po
            left join m_customer cst on sls.siteid = cst.siteid and sls.customerid = cst.customerid and sls.salesmanid = cst.salesmanid
            left join m_sales_salesman salesamn on sls.siteid = salesamn.siteid and sls.salesmanid = salesamn.salesmanid
            left join m_product product on dtl.productid = product.productid
            left join m_area_areasite areasite on areasite.areaid = cst.areaid 
            where sls.retur = 0 AND date(sls.tanggal) between '".$startdate1."' and '".$startdate2."' ".$regional.$area.$subarea.$strquery."
            group by 
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
        return $q_detail->result();   
    }		

    function get_tagihan_all($salesmanid,$get_date1,$get_date2) {

        $sqltagihan = "
                select
                    a.salesmanid,
                    a.customerid,
                    b.nama_customer,
                    a.no_sales,
                    a.no_ink,
                    c.nama_salesman,
                    c.tipe_sales,
                    case when a.retur=1 then concat('-',a.bayar_tunai) else bayar_tunai end bayar_tunai, 
                    case when a.retur=1 then concat('-',a.bayar) else bayar end bayar
                from t_ar_ink_detail a
                left join m_customer b on a.customerid=b.customerid 
                left join m_sales_salesman c on a.salesmanid=c.salesmanid
                where a.salesmanid = '".$salesmanid."' and a.tgl_ink between '".$get_date1."' and '".$get_date2."'
            ";
        
        $data = $this->db->query($sqltagihan)->result_array();

        $return ='<div class="container-table">
                    <table class="table table-bordered table-condensed fixed-table">
                    <tbody>
                    <tr>
                    <th style="width: 250px">Customer ID </th>
                    <th style="width: 200px">Customer Name </th>
                    <th style="width: 600px;">No Bill</th>
                    <th style="width: 600px;">No Faktur / Bill Doc</th>
                    <th style="width: 200px;">Salesman Name</th>
                    <th style="width: 150px;">Sales Type</th>
                    <th style="width: 200px; text-align: right;" >Bill Value</th>
                    <th style="width: 200px; text-align: right;" >Pay</th>
                    <th style="width: 200px; text-align: right;" >Total</th>
                    </tr>
                    </tbody>
                    </table>
                    </div>
                    <div class="container-table-content">
                    <table class="table table-striped table-bordered table-condensed fixed-table">
                    <tbody>';

        $total_tagihan = 0;
        $total_tunai = 0;
        $total_all = 0; 	
        foreach ($data as $value) {

            $total = $value['bayar_tunai'];
            
            $total_tagihan = $total_tagihan + $value['bayar'];
            $total_tunai = $total_tunai + $value['bayar_tunai'];
            
            $total_all = $total_all + $total; 
            
            $return .= '<tr>
    					<td style="width: 250px;">'.$value['customerid'].'</td>
    					<td style="width: 200px;">'.$value['nama_customer'].'</td>
    					<td style="width: 600px;">'.$value['no_ink'].'</td>
    					<td style="width: 600px;">'.$value['no_sales'].'</td>
                        <td style="width: 200px;">'.$value['nama_salesman'].'</td>
                        <td style="width: 150px;">'.$value['tipe_sales'].'</td>
    					<td style="width: 200px; text-align: right;">'.number_format($value['bayar'],2).'</td>
    					<td style="width: 200px; text-align: right;">'.number_format($value['bayar_tunai'],2).'</td>
    					<td style="width: 200px; text-align: right;">'.number_format($total,2).'</td>
    					</tr>';
        }
        $return .= '<tr>
    				<th style="width: 200px;text-align: right;" colspan="6">Total</th>
    				<th style="width: 200px; text-align: right;">'.number_format($total_tagihan,2).'</th>
    				<th style="width: 200px; text-align: right;">'.number_format($total_tunai,2).'</th>
    				<th style="width: 200px; text-align: right;">'.number_format($total_all,2).'</th>
    				</tr>
    				</tbody>
                    </table>
                    </div>
                    <div class="box-footer"><a id="btn-home-form" href="javascript:void(0)" onclick="savexlstagihan(\''.$salesmanid.'\');" class="btn btn-success fa fa-download"> Save Excel</a></div>';
                    $return .= '<script type="text/javascript">
                                const common = new Common();
                                let paramsession = common.getCookie("session");
                
                                function savexlstagihan(salesmanid) {
                                    var startdate1 = $("#get_date1");
                                    var startdate2 = $("#get_date2");
                                    common.direct("rep_order/savexls_tagihan_all/"+salesmanid+"/"+startdate1.val()+"/"+startdate2.val());
                                }

                                $(".container-table-content").on("scroll", function() {
                                    $(".container-table").scrollLeft($(this).scrollLeft());
                                });
                                $(".container-table").on("scroll", function() {
                                    $(".container-table-content").scrollLeft($(this).scrollLeft());
                                });
                
                            </script>';                
        return $return;
    }

    function get_tagihan_all_xls($data) {

        $salesmanid = $data['salesmanid'];
        $startdate1 = $data['startdate1'];
        $startdate2 = $data['startdate2'];
        
        $sqltagihan = "
                select
                    a.salesmanid,
                    a.customerid,
                    b.nama_customer,a.no_sales,
                    a.no_ink,
                    c.nama_salesman,
                    c.tipe_sales, 
                    case when a.retur=1 then concat('-',a.bayar_tunai) else bayar_tunai end bayar_tunai, 
                    case when a.retur=1 then concat('-',a.bayar) else bayar end bayar
                from t_ar_ink_detail a
                left join m_customer b on a.customerid=b.customerid 
                left join m_sales_salesman c on a.salesmanid=c.salesmanid
                where a.salesmanid = '".$salesmanid."' and a.tgl_ink between '".$startdate1."' and '".$startdate2."'
            ";
        
        return $this->db->query($sqltagihan)->result_array();	
    }		

	function get_fancy_order() {
		$tanggal = $_POST['tanggal'];
		$no_po = $_POST['no_po'];
		$salesmanid = $_POST['salesmanid'];
	
		$sql = "
            select
                concat('".URL_IMAGE."', tsm.url_img_po) as url_img_po,
                tsm.no_po,
                mc.nama_customer
            from t_sales_master tsm
            left join m_customer mc on mc.customerid = tsm.customerid
            where tsm.tanggal = ? and tsm.no_po = ? and tsm.salesmanid = ?
        ";
		$result_array = $this->db->query($sql, [$tanggal, $no_po, $salesmanid]);
		$response = $result_array->result();
		return $response;	
	}
}
