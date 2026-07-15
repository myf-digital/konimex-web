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

    public function get_subarea($data)
    {
        $field = " a.* ";
        $table = " ( select subareaid, nama_area as nama_subarea from m_area_subarea where areaid = '".$data['areaid']."' 
                        order by subareaid asc
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
    
    public function getProductivity($data)
    {
        $sqlrecon = "
            REPLACE INTO t_sales_absensi (
                periode, salesmanid, status, checkin, checkout, flag_adjust, keterangan, pjp, 
                effective_call, `call`, extra_call, invalid_call, crc, promo, competitor, `order`, sos, image
            )
            SELECT 
                a.date, 
                a.salesman_id, 
                CASE 
                    WHEN a.type = 'IST' THEN 'S' 
                    WHEN a.type = 'ICT' THEN 'C' 
                    WHEN a.type = 'AHR' THEN 'H'
                    ELSE 'HF' 
                END AS tipe, 
                a.check_in AS checkin, 
                a.check_out AS checkout, 
                IFNULL(b.flag_adjust, 0) AS flag_adjust,
                CONCAT(
                    IFNULL(a.description, ''), 
                    IFNULL(CONCAT('-', a.description_in), ''), 
                    IFNULL(CONCAT('-', a.description_out), '')
                ) AS keterangan, 
                CASE 
                    WHEN a.type = 'ICT' THEN (
                        SELECT COUNT(1) 
                        FROM t_sales_rrk 
                        WHERE periode = a.date AND salesmanid = a.salesman_id
                    ) 
                    WHEN b.flag_adjust = 1 THEN 0
                    ELSE 0 
                END AS _pjp, 
                0 AS _effective_call,
                0 AS _call, 
                0 AS _extra_call, 
                0 AS _invalid_call, 
                0 AS _crc, 
                0 AS _promo, 
                0 AS _competitor, 
                0 AS _order, 
                0 AS _sos, 
                a.image 
            FROM s_absensi a 
            LEFT JOIN t_sales_absensi b 
                ON a.date = b.periode AND a.salesman_id = b.salesmanid 
            WHERE a.date BETWEEN DATE_ADD(?, INTERVAL -3 DAY) AND ?

            UNION

            SELECT 
                a.periode, 
                a.salesmanid, 
                'H' AS status, 
                MIN(a.check_in) AS checkin, 
                MAX(a.check_out) AS checkout, 
                IFNULL(b.flag_adjust, 0) AS flag_adjust,
                '' AS keterangan, 
                CASE 
                    WHEN b.flag_adjust = 0 OR b.flag_adjust = 1 THEN (
                        SELECT COUNT(1) 
                        FROM t_sales_rrk 
                        WHERE periode = a.periode AND salesmanid = a.salesmanid
                    ) 
                    ELSE 0 
                END AS _pjp,
                (
                    SELECT COUNT(1) 
                    FROM t_sales_rrk 
                    WHERE periode = a.periode AND salesmanid = a.salesmanid 
                      AND customerid IN (
                          SELECT customerid 
                          FROM t_sales_master 
                          WHERE tanggal = a.periode AND salesmanid = a.salesmanid
                      )
                ) AS _effective_call, 
                CASE 
                    WHEN b.flag_adjust = 0 OR b.flag_adjust = 1 THEN (
                        SELECT COUNT(1) 
                        FROM t_sales_rrk_trans 
                        WHERE periode = a.periode AND salesmanid = a.salesmanid 
                          AND customerid IN (
                              SELECT customerid 
                              FROM t_sales_rrk 
                              WHERE periode = a.periode AND salesmanid = a.salesmanid
                          )
                          AND customerid NOT IN (
                              SELECT customerid 
                              FROM t_sales_master 
                              WHERE tanggal = a.periode AND salesmanid = a.salesmanid
                          )
                    ) 
                    ELSE 0 
                END AS _call,
                CASE 
                    WHEN b.flag_adjust = 0 OR b.flag_adjust = 1 THEN (
                        SELECT COUNT(1) 
                        FROM t_sales_rrk_trans 
                        WHERE periode = a.periode AND salesmanid = a.salesmanid 
                          AND customerid NOT IN (
                              SELECT customerid 
                              FROM t_sales_rrk 
                              WHERE periode = a.periode AND salesmanid = a.salesmanid
                          )
                    )
                    ELSE (
                        SELECT COUNT(1) 
                        FROM t_sales_rrk_trans 
                        WHERE periode = a.periode AND salesmanid = a.salesmanid
                    )
                END AS _extra_call,
                (
                    SELECT COUNT(1) 
                    FROM t_sales_rrk_trans 
                    WHERE periode = a.periode AND salesmanid = a.salesmanid 
                      AND customerid NOT IN (
                          SELECT customerid 
                          FROM t_sales_rrk 
                          WHERE periode = a.periode AND salesmanid = a.salesmanid
                      )
                      AND customerid NOT IN (
                          SELECT customerid 
                          FROM t_sales_master 
                          WHERE tanggal = a.periode AND salesmanid = a.salesmanid
                      )
                ) AS _invalid_call,
                (
                    SELECT COUNT(1) 
                    FROM t_sales_rrk_trans 
                    WHERE periode = a.periode AND salesmanid = a.salesmanid AND crc_time IS NOT NULL
                ) AS _crc,
                (
                    SELECT COUNT(1) 
                    FROM t_sales_rrk_trans 
                    WHERE periode = a.periode AND salesmanid = a.salesmanid AND promo_time IS NOT NULL
                ) AS _promo,
                (
                    SELECT COUNT(1) 
                    FROM t_sales_rrk_trans 
                    WHERE periode = a.periode AND salesmanid = a.salesmanid AND competitor_time IS NOT NULL
                ) AS _competitor,
                (
                    SELECT COUNT(1) 
                    FROM t_sales_master 
                    WHERE tanggal = a.periode AND salesmanid = a.salesmanid
                ) AS _order,
                (
                    SELECT COUNT(1) 
                    FROM t_sales_rrk_trans 
                    WHERE periode = a.periode AND salesmanid = a.salesmanid AND sos_time IS NOT NULL
                ) AS _sos, 
                '' AS image
            FROM t_sales_rrk_trans a 
            LEFT JOIN t_sales_absensi b 
                ON a.periode = b.periode AND a.salesmanid = b.salesmanid  
            WHERE a.periode BETWEEN DATE_ADD(?, INTERVAL -3 DAY) AND ?
            GROUP BY a.periode, a.salesmanid;
        ";

        $this->db->query($sqlrecon, array(
            $data['start_period'],
            $data['end_period'],
            $data['start_period'],
            $data['end_period']
        ));

        $strquery = "";
        if (!empty($data["restrict_level"])) {
            $restrict_query = get_salesman_restrict($data["usersession"], $data["restrict_level"]);
            if ($restrict_query) {
                $strquery = " AND a.salesmanid IN (" . $restrict_query . ")";
            }
        }

        $regional = "";
        if (!empty($data['regionalid']) && $data['regionalid'] != 'null') {
            $regional = " and a.salesmanid in (select distinct salesmanid from m_salesman_area where regionalid = '".$this->db->escape_str($data['regionalid'])."') ";
        }
        $area = "";
        if (!empty($data['areaid']) && $data['areaid'] != 'null') {
            $area = " and a.salesmanid in (select distinct salesmanid from m_salesman_area where areaid = '".$this->db->escape_str($data['areaid'])."') ";
        }
        $subarea = "";
        if (!empty($data['subareaid']) && $data['subareaid'] != 'null') {
            $subarea = " and a.salesmanid in (select distinct salesmanid from m_salesman_area where subareaid = '".$this->db->escape_str($data['subareaid'])."') ";
        }
        $start = $data['start_period'];
        $end = $data['end_period'];
        $year = date('Y', strtotime($end));
        $month = date('m', strtotime($end));

        $sqlTargets = "
            SELECT
                ar.role_name AS tipe_sales,
                rmt.target_dub,
                rmt.target_hk,
                rmt.target_call_dub,
                rmt.target_call_visit
            FROM role_mapping_target rmt
            JOIN app_role ar ON ar.role_id = rmt.role_id
            WHERE rmt.tahun = ? AND rmt.bulan = ?
        ";
        $targetsResult = $this->db->query($sqlTargets, [$year, (int)$month])->result_array();
        $targetsMap = [];
        foreach ($targetsResult as $t) {
            $targetsMap[strtolower($t['tipe_sales'])] = $t;
        }

        $query = $this->db->query(" 
            SELECT 
                (
                    select group_concat(distinct msa.regionalid separator ', ')
                    from m_salesman_area msa
                    where msa.salesmanid = b.salesmanid
                ) as regionalid,
                (
                    select group_concat(distinct r.nama_regional order by r.nama_regional asc separator ', ')
                    from m_salesman_area msa
                    join m_area_regional r on r.regionalid = msa.regionalid
                    where msa.salesmanid = b.salesmanid
                ) as nama_regional,
                (
                    select group_concat(distinct msa.areaid separator ', ')
                    from m_salesman_area msa
                    where msa.salesmanid = b.salesmanid
                ) as areaid,
                (
                    select group_concat(distinct ar.nama_area order by ar.nama_area asc separator ', ')
                    from m_salesman_area msa
                    join m_area_areasite ar on msa.areaid = ar.areaid
                    where msa.salesmanid = b.salesmanid
                ) as nama_area,
                b.salesmanid,
                b.nama_salesman,
                b.tipe_sales, 
                DATE_FORMAT('$end', '%d') - FLOOR(DATE_FORMAT('$end', '%d') / 7) - (
                    CASE 
                        WHEN DATE_FORMAT('$end', '%d') > 15 THEN (
                            SELECT jml_libur 
                            FROM setup_jumlah_harilibur 
                            WHERE tahun = '$year' AND bulan = '$month'
                        ) 
                        ELSE 0 
                    END
                ) AS 'MEDREP Aktif', 
                (
                    SELECT COUNT(1) 
                    FROM t_sales_absensi 
                    WHERE status = 'H' AND salesmanid = a.salesmanid AND periode BETWEEN '$start' AND '$end'
                ) AS 'MEDREP Hadir',
                (
                    SELECT COUNT(1) 
                    FROM t_sales_absensi 
                    WHERE status = 'C' AND salesmanid = a.salesmanid AND periode BETWEEN '$start' AND '$end'
                ) AS cuti,
                (
                    SELECT COUNT(1) 
                    FROM t_sales_absensi 
                    WHERE status = 'S' AND salesmanid = a.salesmanid AND periode BETWEEN '$start' AND '$end'
                ) AS sakit, 
                ROUND(
                    (
                        COUNT(1) / (
                            DATE_FORMAT('$end', '%d') - FLOOR(DATE_FORMAT('$end', '%d') / 7) - (
                                CASE 
                                    WHEN DATE_FORMAT('$end', '%d') > 25 THEN (
                                        SELECT jml_libur 
                                        FROM setup_jumlah_harilibur 
                                        WHERE tahun = '$year' AND bulan = '$month'
                                    ) 
                                    ELSE 0 
                                END
                            )
                        )
                    ) * 100, 
                    0
                ) AS persentasi,
                (
                    SELECT COUNT(DISTINCT CONCAT(tsrt.customerid, '-', tsrt.periode))
                    FROM t_sales_rrk_trans tsrt
                    JOIN trx_visit_detailing tvd
                        ON tvd.salesmanid = tsrt.salesmanid
                        AND tvd.customerid = tsrt.customerid
                        AND tvd.periode = tsrt.periode
                    JOIN m_customer_ob mco
                        ON mco.salesmanid = tsrt.salesmanid
                        AND mco.customerid = tsrt.customerid
                        AND mco.user_id = tvd.user_id
                    JOIN t_sales_rrk_user tsru
                        ON tsru.salesmanid = tsrt.salesmanid
                        AND tsru.customerid = tsrt.customerid
                        AND tsru.periode = tsrt.periode
                        AND tsru.user_id = tvd.user_id
                    WHERE tsrt.salesmanid = a.salesmanid
                      AND tsrt.periode BETWEEN '$start' AND '$end'
                ) AS call_dub,
                (
                    SELECT COUNT(DISTINCT CONCAT(tsrt.customerid, '-', tsrt.periode))
                    FROM t_sales_rrk_trans tsrt
                    JOIN trx_visit_detailing tvd
                        ON tvd.salesmanid = tsrt.salesmanid
                        AND tvd.customerid = tsrt.customerid
                        AND tvd.periode = tsrt.periode
                    JOIN m_customer_ob mco
                        ON mco.salesmanid = tsrt.salesmanid
                        AND mco.customerid = tsrt.customerid
                        AND mco.user_id = tvd.user_id
                    WHERE tsrt.salesmanid = a.salesmanid
                      AND tsrt.periode BETWEEN '$start' AND '$end'
                ) AS efektif_dub,
                (
                    SELECT COUNT(DISTINCT CONCAT(tsrt.customerid, '-', tsrt.periode))
                    FROM t_sales_rrk_trans tsrt
                    WHERE tsrt.salesmanid = a.salesmanid
                      AND tsrt.periode BETWEEN '$start' AND '$end'
                ) AS call_visit,
                IFNULL((
                    SELECT GROUP_CONCAT(x.reason_rrk SEPARATOR ' , ') 
                    FROM ( 
                        SELECT 
                            z.salesmanid, 
                            CONCAT(y.reason, '(', COUNT(y.reason), ')') AS reason_rrk 
                        FROM t_sales_rrk_trans z 
                        LEFT JOIN t_sales_rrk_reason y ON z.call_reasonid = y.call_reasonid 
                        WHERE z.periode BETWEEN '$start' AND '$end' 
                          AND z.call_reasonid IS NOT NULL 
                        GROUP BY z.salesmanid, y.reason
                    ) x 
                    WHERE x.salesmanid = a.salesmanid 
                    GROUP BY x.salesmanid
                ), '-') AS rrk_keterangan,
                IFNULL((
                    SELECT GROUP_CONCAT(x.reason_detailing SEPARATOR ' , ') 
                    FROM (
                        SELECT 
                            z.salesmanid, 
                            CONCAT(y.reason, '(', COUNT(y.reason), ')') AS reason_detailing
                        FROM t_sales_rrk_trans z 
                        LEFT JOIN trx_visit_detailing y 
                            ON z.periode = y.periode 
                           AND z.customerid = y.customerid 
                           AND z.salesmanid = y.salesmanid 
                        WHERE z.periode BETWEEN '$start' AND '$end' 
                        GROUP BY z.salesmanid, y.reason
                    ) x 
                    WHERE x.salesmanid = a.salesmanid 
                    GROUP BY x.salesmanid
                ), '-') AS rrk_detailing, 
                SUM(e.jumlah_customer) AS jumlah_customer, 
                SUM(e.total_penjualan) AS total_penjualan
            FROM t_sales_absensi a 
            LEFT JOIN m_sales_salesman b ON a.salesmanid = b.salesmanid AND b.aktif = 1
            LEFT JOIN (
                SELECT 
                    salesmanid, 
                    tanggal,
                    COUNT(customerid) AS total_transaksi,
                    COUNT(customerid) AS jumlah_customer,
                    SUM(netto) AS total_penjualan
                FROM t_sales_master 
                WHERE tanggal BETWEEN '$start' AND '$end'
                GROUP BY salesmanid, tanggal
            ) AS e ON a.salesmanid = e.salesmanid AND a.periode = e.tanggal
            WHERE a.periode BETWEEN '$start' AND '$end' 
              AND b.tipe_sales NOT IN ('ADMIN', 'SPV', 'FC') 
              $strquery $subarea $area $regional
            GROUP BY b.salesmanid, b.nama_salesman, b.tipe_sales
            ORDER BY b.nama_salesman ASC;
        ");

        $result = $query->result_array();

        foreach ($result as $k => $row) {
            $role = strtolower($row['tipe_sales']);

            $target_dub_val = isset($targetsMap[$role]) ? (float)$targetsMap[$role]['target_dub'] : 0;
            $target_hk_val = isset($targetsMap[$role]) ? (float)$targetsMap[$role]['target_hk'] : 0;
            $target_call_dub_val = isset($targetsMap[$role]) ? (float)$targetsMap[$role]['target_call_dub'] : 0;
            $target_call_visit_val = isset($targetsMap[$role]) ? (float)$targetsMap[$role]['target_call_visit'] : 0;

            $target_planned = $target_dub_val * $target_call_dub_val;
            $target_visit = $target_hk_val * $target_call_visit_val;

            $result[$k]['target_dub'] = $target_planned;
            $result[$k]['target_visit'] = $target_visit;

            $result[$k]['efektif_dub'] = $target_dub_val > 0 ? round($row['call_dub'] / $target_dub_val, 2) : 0;
            $result[$k]['rata_rata_visit'] = $target_hk_val > 0 ? round($row['call_visit'] / $target_hk_val, 2) : 0;
        }

        return $result;
    }

    function getOrder_salesman($data)
    {
        $strquery = "";
        if (!empty($data["restrict_level"])) {
            $restrict_query = get_salesman_restrict($data["usersession"], $data["restrict_level"]);
            if ($restrict_query) {
                $strquery = " AND sls.salesmanid IN (" . $restrict_query . ")";
            }
        }

        $regional = "";
        if (!empty($data['regionalid']) && $data['regionalid'] != 'null') {
            $regional = " and sls.salesmanid in (select distinct salesmanid from m_salesman_area where regionalid = '".$this->db->escape_str($data['regionalid'])."') ";
        }
        $area = "";
        if (!empty($data['areaid']) && $data['areaid'] != 'null') {
            $area = " and sls.salesmanid in (select distinct salesmanid from m_salesman_area where areaid = '".$this->db->escape_str($data['areaid'])."') ";
        }
        $subarea = "";
        if (!empty($data['subareaid']) && $data['subareaid'] != 'null') {
            $subarea = " and sls.salesmanid in (select distinct salesmanid from m_salesman_area where subareaid = '".$this->db->escape_str($data['subareaid'])."') ";
        }
        $start=$data['start_period'];
        $end=$data['end_period'];
		$query = $this->db->query(" 
                select 
                    sls.tanggal as period,
                    sls.siteid, 
                    sls.salesmanid,
                    salesamn.nama_salesman,
                    sls.customerid,
                    cst.nama_customer,
                    e.nama_class as account,
                    cst.alamat,
                    cst.typeid,
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
                $area $regional $subarea
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
        $strquery = "";
        if (!empty($data["restrict_level"])) {
            $restrict_query = get_salesman_restrict($data["usersession"], $data["restrict_level"]);
            if ($restrict_query) {
                $strquery = " AND sls.salesmanid IN (" . $restrict_query . ")";
            }
        }

        $regional = "";
        if (!empty($data['regionalid']) && $data['regionalid'] != 'null') {
            $regional = " and sls.salesmanid in (select distinct salesmanid from m_salesman_area where regionalid = '".$this->db->escape_str($data['regionalid'])."') ";
        }
        $area = "";
        if (!empty($data['areaid']) && $data['areaid'] != 'null') {
            $area = " and sls.salesmanid in (select distinct salesmanid from m_salesman_area where areaid = '".$this->db->escape_str($data['areaid'])."') ";
        }
        $subarea = "";
        if (!empty($data['subareaid']) && $data['subareaid'] != 'null') {
            $subarea = " and sls.salesmanid in (select distinct salesmanid from m_salesman_area where subareaid = '".$this->db->escape_str($data['subareaid'])."') ";
        }
        $start=$data['start_period'];
        $end=$data['end_period'];
		$query = $this->db->query(" 
                select 
                    sls.periode,
                    sls.siteid, 
                    sls.salesmanid,
                    salesamn.nama_salesman,
                    sls.customerid,
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
                $area $regional $subarea
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
        $strquery = "";
        if (!empty($data["restrict_level"])) {
            $restrict_query = get_salesman_restrict($data["usersession"], $data["restrict_level"]);
            if ($restrict_query) {
                $strquery = " AND b.salesmanid IN (" . $restrict_query . ")";
            }
        }

        $regional = "";
        if (!empty($data['regionalid']) && $data['regionalid'] != 'null') {
            $regional = " and a.salesmanid in (select distinct salesmanid from m_salesman_area where regionalid = '".$this->db->escape_str($data['regionalid'])."') ";
        }
        $area = "";
        if (!empty($data['areaid']) && $data['areaid'] != 'null') {
            $area = " and a.salesmanid in (select distinct salesmanid from m_salesman_area where areaid = '".$this->db->escape_str($data['areaid'])."') ";
        }
        $subarea = "";
        if (!empty($data['subareaid']) && $data['subareaid'] != 'null') {
            $subarea = " and a.salesmanid in (select distinct salesmanid from m_salesman_area where subareaid = '".$this->db->escape_str($data['subareaid'])."') ";
        }
        $start=$data['start_period'];
        $end=$data['end_period'];
		$query = $this->db->query(" 
                select
                    a.periode,
                    a.salesmanid,
                    b.nama_salesman,
                    a.customerid,
                    c.nama_customer,
                    c.alamat,
                    c.typeid,
                    (
                        select group_concat(distinct msa.regionalid separator ', ')
                        from m_salesman_area msa
                        where msa.salesmanid = b.salesmanid
                    ) as regionalid,
                    (
                        select group_concat(distinct r.nama_regional order by r.nama_regional asc separator ', ')
                        from m_salesman_area msa
                        join m_area_regional r on r.regionalid = msa.regionalid
                        where msa.salesmanid = b.salesmanid
                    ) as nama_regional,
                    (
                        select group_concat(distinct msa.areaid separator ', ')
                        from m_salesman_area msa
                        where msa.salesmanid = b.salesmanid
                    ) as areaid,
                    (
                        select group_concat(distinct ar.nama_area order by ar.nama_area asc separator ', ')
                        from m_salesman_area msa
                        join m_area_areasite ar on msa.areaid = ar.areaid
                        where msa.salesmanid = b.salesmanid
                    ) as nama_area,
                    c.typeid as channel,
                    e.nama_class as account,
                    DATE_FORMAT(a.check_in, '%H:%i:%s') check_in,
                    DATE_FORMAT(a.check_out, '%H:%i:%s') check_out,
                    timediff(DATE_FORMAT(a.check_out, '%H:%i:%s'),DATE_FORMAT(a.check_in, '%H:%i:%s')) lama_kunjungan,
                    (select reason from t_sales_rrk_reason where call_reasonid=a.call_reasonid) alasan,
                    REGEXP_REPLACE(a.keterangan, '\n', ' ') keterangan,
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
                where a.periode between '".$start."' and '".$end."' 
                and b.tipe_sales not in ('ADMIN','SPV','FC') 
                and b.aktif = 1 $strquery
                $area $regional $subarea

                union all

                select
                    a.periode,
                    a.salesmanid,
                    b.nama_salesman,
                    a.customerid,
                    c.nama_customer,
                    c.alamat,
                    c.typeid,
                    (
                        select group_concat(distinct msa.regionalid separator ', ')
                        from m_salesman_area msa
                        where msa.salesmanid = b.salesmanid
                    ) as regionalid,
                    (
                        select group_concat(distinct r.nama_regional order by r.nama_regional asc separator ', ')
                        from m_salesman_area msa
                        join m_area_regional r on r.regionalid = msa.regionalid
                        where msa.salesmanid = b.salesmanid
                    ) as nama_regional,
                    (
                        select group_concat(distinct msa.areaid separator ', ')
                        from m_salesman_area msa
                        where msa.salesmanid = b.salesmanid
                    ) as areaid,
                    (
                        select group_concat(distinct ar.nama_area order by ar.nama_area asc separator ', ')
                        from m_salesman_area msa
                        join m_area_areasite ar on msa.areaid = ar.areaid
                        where msa.salesmanid = b.salesmanid
                    ) as nama_area,
                    c.typeid as channel,
                    e.nama_class as account,
                    0 check_in,
                    0 check_out,
                    0 lama_kunjungan,
                    '' alasan,
                    '' keterangan,
                    'FJP Tidak Terkunjungi' as flag
                from t_sales_rrk a
                left join m_sales_salesman b on a.salesmanid = b.salesmanid
                left join m_customer c on a.customerid= c.customerid 
                left join m_customer_class e on e.classid=c.classid
                where a.periode between '".$start."' and '".$end."' 
                and a.customerid not in (select customerid from t_sales_rrk_trans where periode between '".$start."' and '".$end."')
                and b.tipe_sales not in ('ADMIN','SPV','FC') 
                and b.aktif = 1 $strquery
                $area $regional $subarea
                ;
            ");
        return $query->result_array();
    }

    function get_detailing_parma($data)
    {
        $strquery = "";
        if (!empty($data["restrict_level"])) {
            $restrict_query = get_salesman_restrict($data["usersession"], $data["restrict_level"]);
            if ($restrict_query) {
                $strquery = " AND b.salesmanid IN (" . $restrict_query . ")";
            }
        }

        $regional = "";
        if (!empty($data['regionalid']) && $data['regionalid'] != 'null') {
            $regional = " and a.salesmanid in (select distinct salesmanid from m_salesman_area where regionalid = '".$this->db->escape_str($data['regionalid'])."') ";
        }
        $area = "";
        if (!empty($data['areaid']) && $data['areaid'] != 'null') {
            $area = " and a.salesmanid in (select distinct salesmanid from m_salesman_area where areaid = '".$this->db->escape_str($data['areaid'])."') ";
        }
        $subarea = "";
        if (!empty($data['subareaid']) && $data['subareaid'] != 'null') {
            $subarea = " and a.salesmanid in (select distinct salesmanid from m_salesman_area where subareaid = '".$this->db->escape_str($data['subareaid'])."') ";
        }
        $start=$data['start_period'];
        $end=$data['end_period'];
		$query = $this->db->query("
            select
                a.periode,
                a.siteid,
                a.salesmanid,
                a.salesman_name,
                a.customerid,
                a.professional_name,
                rp.spesialisasi,
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
                c.nama_customer,c.typeid as channel,c.nama_account as account,
                (
                    select group_concat(distinct ar.nama_area order by ar.nama_area asc separator ', ')
                    from m_salesman_area msa
                    join m_area_areasite ar on msa.areaid = ar.areaid
                    where msa.salesmanid = a.salesmanid
                ) as city,
                CASE
                    WHEN a.status = 5 THEN 'Tidak Valid'
                    WHEN a.status = 3 THEN 'Valid'
                    WHEN a.status = 2 THEN 'Belum Valid'
                    ELSE 'Butuh Verifikasi'
                END as status_label,
                COALESCE(
                    (
                        SELECT GROUP_CONCAT(DISTINCT CONCAT(mp.productid, ' - ', mp.nama_invoice) ORDER BY mp.nama_invoice SEPARATOR ', ')
                        FROM m_product mp
                        WHERE FIND_IN_SET(mp.productid, a.array_product) > 0
                    ),
                    a.array_product
                ) as products
            from trx_visit_detailing a
            left join m_sales_salesman b on a.salesmanid=b.salesmanid
            left join v_outlet_all c on a.customerid=c.customerid
            left join (
                select 
                    a.id,
                    a.nama_professional,
                    b.name as spesialisasi
                from ref_professional a
                left join ref_spesialisasi b on b.id = a.spesialisasi_id
            ) as rp on rp.id = a.user_id
            where a.periode between '".$start."' and '".$end."'
            $strquery
            $area $regional $subarea
            ");
        return $query->result_array();
    }

    function get_progress_listing($data)
    {
        $strquery = "";
        if (!empty($data["restrict_level"])) {
            $restrict_query = get_salesman_restrict($data["usersession"], $data["restrict_level"]);
            if ($restrict_query) {
                $strquery = " AND tpl.salesmanid IN (" . $restrict_query . ")";
            }
        }

        $where = " 1=1 ";
        if (!empty($data['start_period']) && !empty($data['end_period'])) {
            $where .= " AND tpl.periode BETWEEN '".$data['start_period']."' AND LAST_DAY('".$data['end_period']."')";
        }
        if (!empty($data['regionalid']) && $data['regionalid'] != 'null') {
            $where .= " AND tpl.salesmanid IN (select distinct salesmanid from m_salesman_area where regionalid = '".$this->db->escape_str($data['regionalid'])."')";
        }
        if (!empty($data['areaid']) && $data['areaid'] != 'null') {
            $where .= " AND tpl.salesmanid IN (select distinct salesmanid from m_salesman_area where areaid = '".$this->db->escape_str($data['areaid'])."')";
        }
        if (!empty($data['subareaid']) && $data['subareaid'] != 'null') {
            $where .= " AND tpl.salesmanid IN (select distinct salesmanid from m_salesman_area where subareaid = '".$this->db->escape_str($data['subareaid'])."')";
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
        SELECT 
            mss.nama_salesman, 
            tsa.salesmanid, 
            (
                select group_concat(distinct ar.nama_area order by ar.nama_area asc separator ', ')
                from m_salesman_area msa
                join m_area_areasite ar on msa.areaid = ar.areaid
                where msa.salesmanid = tsa.salesmanid
            ) as nama_area,
            tsa.status, 
            tsa.periode, 
            ap.start_time, 
            ap.start_image, 
            ap.end_time, 
            ap.end_image
        FROM t_sales_absensi tsa
            LEFT JOIN m_sales_salesman mss ON mss.salesmanid = tsa.salesmanid 
            left join attendance_parma ap on tsa.salesmanid=ap.salesmanid and tsa.periode=ap.periode 
            WHERE ".$where . $strquery ."
            order by tsa.periode, tsa.salesmanid 
        ");
        return $query->result_array();
    }

    function get_daily_target_call($data)
    {
        $strquery = "";
        if (!empty($data["restrict_level"])) {
            $restrict_query = get_salesman_restrict($data["usersession"], $data["restrict_level"]);
            if ($restrict_query) {
                $strquery = " AND a.salesmanid IN (" . $restrict_query . ")";
            }
        }

        $where = " 1=1 ";
        if (isset($data['start_period']) && isset($data['end_period'])) {
            $where .= " AND a.periode BETWEEN '".$data['start_period']."' AND '".$data['end_period']."'";
        }
        if (isset($data['regionalid']) && $data['regionalid'] != 'null') {
            $where .= " AND a.salesmanid IN (select distinct salesmanid from m_salesman_area where regionalid = '".$this->db->escape_str($data['regionalid'])."')";
        }
        if (isset($data['areaid']) && $data['areaid'] != 'null') {
            $where .= " AND a.salesmanid IN (select distinct salesmanid from m_salesman_area where areaid = '".$this->db->escape_str($data['areaid'])."')";
        }
        if (isset($data['subareaid']) && $data['subareaid'] != 'null') {
            $where .= " AND a.salesmanid IN (select distinct salesmanid from m_salesman_area where subareaid = '".$this->db->escape_str($data['subareaid'])."')";
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
                    c.nama_salesman AS nama_salesman,
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
                 left join (
                    select 
                        s.salesmanid,
                        s.nama_salesman,
                        (
                            select group_concat(distinct ar.nama_area order by ar.nama_area asc separator ', ')
                            from m_salesman_area msa
                            join m_area_areasite ar on msa.areaid = ar.areaid
                            where msa.salesmanid = s.salesmanid
                        ) as nama_area,
                        (
                            select group_concat(distinct msa.areaid separator ', ')
                            from m_salesman_area msa
                            where msa.salesmanid = s.salesmanid
                        ) as areaid
                    from m_sales_salesman s
                    where s.tipe_sales = 'MEDREP'
                ) c on (c.salesmanid = a.salesmanid))
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
                    (FIND_IN_SET(e.areaid, replace(c.areaid, ' ', '')) > 0))
            WHERE ".$where . $strquery ."
            ) x
            group by
                x.periode,
                x.salesmanid,
                x.nama_salesman,
                x.nama_area,
                x.email) z
        ");
        return $query->result_array();
        
    }

}
