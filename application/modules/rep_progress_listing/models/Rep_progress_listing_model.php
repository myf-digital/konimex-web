<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_progress_listing_model extends CI_Model
{
    public function load($data)
    {
        $sql = $this->sql_progress_listing($data);
        $field = " a.* ";
        $table = " (".$sql.") a";
        return easy_pagging($data, $field, $table);
    }

    public function progress_listing($data)
    {
        $sql = $this->sql_progress_listing($data);
        return $this->db->query($sql)->result_array();
    }

    function get_progress_listing($siteid, $customerid, $salesmanid, $get_date1, $get_date2) {
        $return = '\'<table class="table table-striped table-bordered table-condensed">\'+
                    \'<thead>\'+
                    \'<tr>\'+
                    \'<th rowspan="2" style="white-space:nowrap;padding-left:10px;vertical-align:middle;">Brand ID</th>\'+
                    \'<th rowspan="2" style="white-space:nowrap;padding-left:10px;vertical-align:middle;">Nama Brand</th>\'+
                    \'<th rowspan="2" style="white-space:nowrap;padding-left:10px;vertical-align:middle;">Progress</th>\'+
                    \'<th rowspan="10" style="white-space: nowrap;text-align:center;padding-right:10px;">Sign Memo User</th>\'+
                    \'</tr>\'+
                    \'<tr>\'+
                    \'<th style="white-space: nowrap;text-align:right;padding-right:10px;">Ambil Dokumen Registrasi</th>\'+
                    \'<th style="white-space: nowrap;text-align:right;padding-right:10px;">Melengkapi Dokumen Registrasi</th>\'+
                    \'<th style="white-space: nowrap;text-align:right;padding-right:10px;">Dokter 1</th>\'+	
                    \'<th style="white-space: nowrap;text-align:right;padding-right:10px;">Dokter 2</th>\'+	
                    \'<th style="white-space: nowrap;text-align:right;padding-right:10px;">Dokter 3</th>\'+	
                    \'<th style="white-space: nowrap;text-align:right;padding-right:10px;">Dokter 4</th>\'+	
                    \'<th style="white-space: nowrap;text-align:right;padding-right:10px;">Dokter 5</th>\'+	
                    \'<th style="white-space: nowrap;text-align:right;padding-right:10px;">Dokumen Registrasi Lengkap</th>\'+	
                    \'</tr>\'+
                    \'</thead>\'+
                    \'<tbody>\'+';
        
        $q_detail = $this->sql_progress_listing_detail([
            'siteid' => $siteid,
            'salesmanid' => $salesmanid,
            'customerid' => $customerid,
            'startdate' => $get_date1,
            'enddate' => $get_date2,
        ]);

        foreach ($q_detail as $v_detail) {
            $return .= '\'<tr>\'+
                        \'<td style="white-space: nowrap;padding-left:10px;">'.$v_detail['brandid'].'</td>\'+
                        \'<td style="white-space: nowrap;padding-left:10px;">'.$v_detail['brand'].'</td>\'+
                        \'<td style="white-space: nowrap;padding-left:10px;">'.$v_detail['progress'].'</td>\'+
                        \'<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.$this->signFormat($v_detail['ambil_dok_registrasi']).'</td>\'+
                        \'<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.$this->signFormat($v_detail['melengkapi_dok_registrasi']).'</td>\'+
                        \'<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.$this->signFormat($v_detail['sign_dokter_1']).'</td>\'+
                        \'<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.$this->signFormat($v_detail['sign_dokter_2']).'</td>\'+
                        \'<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.$this->signFormat($v_detail['sign_dokter_3']).'</td>\'+
                        \'<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.$this->signFormat($v_detail['sign_dokter_4']).'</td>\'+
                        \'<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.$this->signFormat($v_detail['sign_dokter_5']).'</td>\'+
                        \'<td style="white-space: nowrap;text-align:right;padding-right:10px;">'.$this->signFormat($v_detail['dok_registrasi_lengkap']).'</td>\'+
                        \'</tr>\'+';
        }
        $return .= '\'</tbody>\'+ \'</table>\'+';
        return $return;
    }

    function sql_progress_listing($data) {
        $restrict_query = get_salesman_restrict($data["usersession"], $data["restrict_level"]);
        if ($restrict_query){
            $strquery = " and mss.salesmanid in (" . $restrict_query . ")";
        } else {
            $strquery = "";
        }

        $sql = "select
                distinct tpl.siteid,
                tpl.periode,
                tpl.salesmanid,
                tpl.nama_salesman,
                mss.tipe_sales,
                tpl.customerid,
                tpl.nama_customer,
                mc.kode_outlet,
                (
                    select count(1)
                    from trx_progress_listing
                    where siteid=tpl.siteid and periode=tpl.periode
                    and salesmanid=tpl.salesmanid and customerid=tpl.customerid
                ) AS _brand
            from trx_progress_listing tpl
            left join m_sales_salesman mss on mss.salesmanid=tpl.salesmanid
            left join m_customer mc on mc.customerid=tpl.customerid
            where tpl.periode between '".(@$data["get_date1"] ??today())."' and '".(@$data["get_date2"] ??today())."' ".$strquery."
            group by tpl.siteid, tpl.periode, tpl.salesmanid, tpl.customerid
        ";
        return $sql;
    }

	function sql_progress_listing_detail($data) {
        $siteid = $data['siteid'] ?? null;
        $salesmanid = $data['salesmanid'] ?? null;
        $customerid = $data['customerid'] ?? null;
        $start = $data['startdate'] ?? null;
        $end = $data['enddate'] ?? null;

        $where = '';
        $param = [$start, $end];
        if ($siteid) {
            $param[] = $siteid;
            $where .= " AND siteid = ?";
        }
        if ($salesmanid) {
            $param[] = $salesmanid;
            $where .= " AND salesmanid = ?";
        }
        if ($customerid) {
            $param[] = $customerid;
            $where .= " AND customerid = ?";
        }

        $sql = "
            SELECT t.*
            FROM trx_progress_listing t
            JOIN (
                SELECT siteid, salesmanid, customerid, brandid, MAX(periode) AS max_periode
                FROM trx_progress_listing
                WHERE DATE(periode) BETWEEN ? AND ?
                ".$where."
                GROUP BY siteid, salesmanid, customerid, brandid
            ) latest
                ON t.siteid = latest.siteid
                AND t.salesmanid = latest.salesmanid
                AND t.customerid = latest.customerid
                AND t.brandid = latest.brandid
                AND t.periode = latest.max_periode;
        ";
        return $this->db->query($sql, $param)->result_array();
	}

    function signFormat($value) {
        if ($value && $value == 1) return 'Sudah';
        return '';
    }
}
