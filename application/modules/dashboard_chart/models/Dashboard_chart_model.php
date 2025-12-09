<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_chart_model extends CI_Model
{
    public function load($data)
    {
        $productivity = $this->loadProductivity($data);
        $performance = $this->loadPerformance($data);
        $presensi = $this->loadPresensi($data);
        $parma_coverage = $this->loadParmaCoverage($data);

        return [
            'productivity' => $productivity,
            'performance' => $performance,
            'presensi' => $presensi,
            'parma_coverage' => $parma_coverage,
        ];
    }

    public function loadPerformance($data)
    {
        $start = $data['start'] ?? date("Y-m-d");
        $end = $data['end'] ?? date("Y-m-d");

        if ($data["restrict_level"] == '4') {
			$strquery = " AND b.salesmanid IN (SELECT salesmanid FROM m_sales_salesman WHERE subareaid IN (SELECT distinct b.subareaid FROM  
                app_resource a LEFT JOIN app_restrict_location b ON a.resource_id=b.resource_id 
                WHERE a.username='".$data["usersession"]."') AND tipe_sales='PAR' AND aktif=1
            ) ";
		} else if ($data["restrict_level"] == '3') {
			$strquery = " AND b.salesmanid IN (SELECT salesmanid FROM m_sales_salesman WHERE areaid IN (SELECT distinct b.areaid FROM  
                app_resource a LEFT JOIN app_restrict_location b ON a.resource_id=b.resource_id 
                WHERE a.username='".$data["usersession"]."') AND tipe_sales='PAR' AND aktif=1
            ) ";
		} else if ($data["restrict_level" ]== '2') {
			$strquery = " AND b.salesmanid IN (SELECT salesmanid FROM m_sales_salesman WHERE regionalid IN (SELECT distinct b.regionalid FROM  
                app_resource a LEFT JOIN app_restrict_location b ON a.resource_id=b.resource_id 
                WHERE a.username='".$data["usersession"]."') AND tipe_sales='PAR' AND aktif=1
            ) ";
		} else {
			$strquery = " AND b.salesmanid IN (SELECT salesmanid FROM m_sales_salesman WHERE tipe_sales='PAR' AND aktif=1)";
		}

		$query = "
			SELECT 
                b.salesmanid,
                b.siteid,
                b.nama_salesman,
                b.tipe_sales,
                c.nama_area AS city,
                COALESCE(rrk._jadwal, 0) AS _jadwal,
                COALESCE(cal._call, 0) AS _call,
                COALESCE(exc._extra_call, 0) AS _extra_call,
                COALESCE(crc._crc, 0) AS _crc,
                COALESCE(ord._order, 0) AS _order,
                COALESCE(ord.total_order, 0) AS total_order
            FROM m_sales_salesman b
            LEFT JOIN (
                SELECT 
                    salesmanid,
                    COUNT(*) AS _jadwal
                FROM t_sales_rrk
                WHERE periode BETWEEN '$start' AND '$end'
                GROUP BY salesmanid
            ) rrk ON rrk.salesmanid = b.salesmanid
            LEFT JOIN (
                SELECT 
                    tr.salesmanid,
                    COUNT(*) AS _call
                FROM t_sales_rrk_trans tr
                JOIN t_sales_rrk r 
                    ON r.salesmanid = tr.salesmanid 
                    AND r.periode = tr.periode 
                    AND r.customerid = tr.customerid
                WHERE tr.periode BETWEEN '$start' AND '$end'
                GROUP BY tr.salesmanid
            ) cal ON cal.salesmanid = b.salesmanid
            LEFT JOIN (
                SELECT 
                    tr.salesmanid,
                    COUNT(*) AS _extra_call
                FROM t_sales_rrk_trans tr
                LEFT JOIN t_sales_rrk r 
                    ON r.salesmanid = tr.salesmanid 
                    AND r.periode = tr.periode 
                    AND r.customerid = tr.customerid
                WHERE tr.periode BETWEEN '$start' AND '$end'
                AND r.customerid IS NULL
                GROUP BY tr.salesmanid
            ) exc ON exc.salesmanid = b.salesmanid
            LEFT JOIN (
                SELECT 
                    salesmanid,
                    COUNT(*) AS _crc
                FROM t_sales_rrk_trans
                WHERE periode BETWEEN '$start' AND '$end'
                AND crc_time IS NOT NULL
                GROUP BY salesmanid
            ) crc ON crc.salesmanid = b.salesmanid
            LEFT JOIN (
                SELECT 
                    tr.salesmanid,
                    COUNT(*) AS _order,
                    SUM(tsm.netto) AS total_order
                FROM t_sales_rrk_trans tr
                JOIN t_sales_master tsm 
                    ON tr.periode = tsm.tanggal
                    AND tr.salesmanid = tsm.salesmanid
                    AND tr.customerid = tsm.customerid
                WHERE tr.periode BETWEEN '$start' AND '$end'
                AND tsm.bruto > 0
                GROUP BY tr.salesmanid
            ) ord ON ord.salesmanid = b.salesmanid
            LEFT JOIN m_area_areasite c ON b.areaid = c.areaid
            WHERE b.tipe_sales='PAR' AND b.aktif = 1 AND b.salesmanid NOT IN ('PAR100','PAR101') $strquery
            ORDER BY b.salesmanid
        ";
		$data = $this->db->query($query)->result_array();

        $result = [
            'labels' => [],
            'data_schedule' => [],
            'data_call' => [],
            'data_extra' => [],
            'data_crc' => [],
            'data_order' => [],
            'extraInfo' => [],
        ];
        foreach ($data as $d) {
            $result['labels'][] = $d['salesmanid'];
            $result['data_schedule'][] = $d['_jadwal'];
            $result['data_call'][] = $d['_call'];
            $result['data_extra'][] = $d['_extra_call'];
            $result['data_crc'][] = $d['_crc'];
            $result['data_order'][] = $d['_order'];
            $result['extraInfo'][] = [
                'nama_salesman' => $d['nama_salesman'],
                'city' => $d['city'],
                'total_order' => number_format($d['total_order'],0, '.', ','),
            ];
        }

        return $result;
    }

    public function loadProductivity($data)
    {
        $start = $data['start'] ?? date("Y-m-d");
        $end = $data['end'] ?? date("Y-m-d");
        $year = date('Y', strtotime($end));
        $month = date('m', strtotime($end));

        if ($data['restrict_level'] == '4') {
            $strquery = " AND a.salesmanid IN (SELECT salesmanid FROM m_sales_salesman WHERE subareaid IN (SELECT distinct b.subareaid FROM  
                app_resource a LEFT JOIN app_restrict_location b ON  a.resource_id=b.resource_id 
                WHERE a.username='".$data['usersession']."')
            )";
        } else if ($data['restrict_level'] == '3') {
            $strquery = " AND a.salesmanid IN (SELECT salesmanid FROM m_sales_salesman WHERE areaid IN (SELECT distinct b.areaid FROM  
                app_resource a LEFT JOIN app_restrict_location b ON  a.resource_id=b.resource_id 
                WHERE a.username='".$data['usersession']."')
            )";
        } else if ($data['restrict_level'] == '2') {
            $strquery = " AND a.salesmanid IN (SELECT salesmanid FROM m_sales_salesman WHERE regionalid IN (SELECT distinct b.regionalid FROM  
                app_resource a LEFT JOIN app_restrict_location b ON  a.resource_id=b.resource_id 
                WHERE a.username='".$data['usersession']."')
            ) ";
        } else {
            $strquery = "";
        }
        
		$query = "
			SELECT
                b.salesmanid,
                b.nama_salesman,
                b.tipe_sales,
                c.nama_area AS city,
				date_format('$end','%d')-FLOOR(date_format('$end','%d')/7)-(
                    CASE WHEN date_format('$end','%d') > 15 
                    then (SELECT jml_libur FROM setup_jumlah_harilibur WHERE tahun='$year' AND bulan='$month') else 0 end
                ) AS 'aktif', 
                (
                    SELECT count(1)
                    FROM t_sales_absensi
                    WHERE status='H' AND salesmanid=a.salesmanid AND periode BETWEEN '".$start."' AND '".$end."'
                ) AS 'hadir',
                (SELECT count(1) FROM t_sales_absensi WHERE status='C' AND salesmanid=a.salesmanid AND periode BETWEEN '".$start."' AND '".$end."') AS cuti,
                (SELECT count(1) FROM t_sales_absensi WHERE status='S' AND salesmanid=a.salesmanid AND periode BETWEEN '".$start."' AND '".$end."') AS sakit, 
                sum(a.pjp) as pjp,
                sum(a.call) as `call`,
                IFNULL((
                    SELECT GROUP_CONCAT(x.reason_rrk SEPARATOR ' , ')
                    FROM ( 
                        SELECT z.salesmanid, CONCAT(y.reason, '(', count(y.reason), ')') AS reason_rrk 
                        FROM t_sales_rrk_trans z LEFT JOIN t_sales_rrk_reason y ON z.call_reasonid=y.call_reasonid 
                        WHERE z.periode BETWEEN '".$start."' AND '".$end."' 
                        AND z.call_reasonid is not null 
                        GROUP BY z.salesmanid,y.reason
                    ) x WHERE x.salesmanid=a.salesmanid GROUP BY x.salesmanid
                ),'-') AS rrk_keterangan,
                IFNULL((
                    SELECT GROUP_CONCAT(x.reason_detailing SEPARATOR ' , ')
                    FROM (
                        SELECT z.salesmanid, CONCAT(y.reason, '(', count(y.reason), ')') AS reason_detailing
                        FROM t_sales_rrk_trans z
                        LEFT JOIN trx_visit_detailing y ON z.periode=y.periode AND z.customerid=y.customerid AND z.salesmanid=y.salesmanid 
                        WHERE z.periode BETWEEN '".$start."' AND '".$end."' 
                        GROUP BY z.salesmanid,y.reason
                    ) x WHERE x.salesmanid=a.salesmanid GROUP BY x.salesmanid
                ),'-') AS rrk_detailing
            FROM t_sales_absensi a
            LEFT JOIN m_sales_salesman b ON a.salesmanid=b.salesmanid AND b.salesmanid NOT IN ('PAR100', 'PAR101') AND b.aktif=1
            LEFT JOIN m_area_areasite c ON c.areaid=b.areaid LEFT JOIN m_area_regional d ON d.regionalid=c.regionalid
            LEFT JOIN (
                SELECT salesmanid,tanggal,
                COUNT(customerid) AS total_transaksi,
                COUNT(customerid) AS jumlah_customer,
                SUM(netto) AS total_penjualan
                FROM t_sales_master WHERE tanggal BETWEEN '".$start."' AND '".$end."'
                GROUP BY salesmanid, tanggal
            ) AS e ON a.salesmanid=e.salesmanid AND a.periode=e.tanggal
            WHERE a.periode BETWEEN '".$start."' AND '".$end."' AND b.tipe_sales NOT IN ('ADMIN','SPV','FC') 
            $strquery
            GROUP BY d.regionalid, d.nama_regional, c.areaid, c.nama_area, b.salesmanid, b.nama_salesman, b.tipe_sales
            ORDER BY d.regionalid ASC, c.areaid ASC, b.tipe_sales ASC, b.nama_salesman ASC							
		";
        $data = $this->db->query($query)->result_array();

        $result = [
            'labels' => [],
            'data' => [],
            'extraInfo' => [],
        ];
        foreach ($data as $d) {
            $result['labels'][] = $d['salesmanid'];
            $result['data'][] = $d['pjp'] ? number_format($d['call']/$d['pjp']*100, 2, '.', ',') : 0;
            $result['extraInfo'][] = [
                'salesmanid' => $d['salesmanid'],
                'nama_salesman' => $d['nama_salesman'],
                'city' => $d['city'],
                'rrk_keterangan' => $d['rrk_keterangan'],
                'rrk_detailing' => $d['rrk_detailing'],
                'kehadiran' => number_format($d['hadir']/$d['aktif']*100, 2, '.', ','),
                'sakit' => $d['sakit'],
                'cuti' => $d['cuti'],
            ];
        }

        return $result;
    }

    public function loadPresensi($data)
    {
        $start = $data['start'] ?? date("Y-m-d");
        $end = $data['end'] ?? date("Y-m-d");

        if ($data['restrict_level'] == '4') {
			$strquery = " AND a.salesmanid IN (SELECT salesmanid FROM m_sales_salesman WHERE subareaid IN (SELECT distinct b.subareaid FROM  
                app_resource a LEFT JOIN app_restrict_location b ON a.resource_id=b.resource_id 
                WHERE a.username='".$data['usersession']."')
            )";
		} else if ($data['restrict_level'] == '3') {
			$strquery = " AND a.salesmanid IN (SELECT salesmanid FROM m_sales_salesman WHERE areaid IN (SELECT distinct b.areaid FROM  
                app_resource a LEFT JOIN app_restrict_location b ON a.resource_id=b.resource_id 
                WHERE a.username='".$data['usersession']."')
            )";
		} else if ($data['restrict_level'] == '2') {
			$strquery = " AND a.salesmanid IN (SELECT salesmanid FROM m_sales_salesman WHERE regionalid IN (SELECT distinct b.regionalid FROM  
                app_resource a LEFT JOIN app_restrict_location b ON a.resource_id=b.resource_id 
                WHERE a.username='".$data['usersession']."')
            ) ";
		} else {
			$strquery = "";
		}

        $query = " 
            SELECT
                a.periode,
                a.salesmanid,
                b.nama_salesman,
                b.nama_area AS city,
                a.start_time,
                a.start_image,
                a.start_keterangan,
                a.end_time,
                a.end_keterangan
            FROM attendance_parma a
            LEFT JOIN v_gff_info b ON a.salesmanid=b.salesmanid 
            WHERE a.periode BETWEEN '$start' AND '$end' AND a.salesmanid NOT IN ('PAR100', 'PAR101')
            $strquery
            ORDER BY a.periode DESC
        ";
		$data = $this->db->query($query)->result_array();

        $group = [];
        $labels = [];
        foreach ($data as $d) {
            $date = $d['periode'];
            $sid  = $d['salesmanid'];

            if (!isset($group[$date])) {
                $group[$date] = [];
                $labels[] = $date;
            }

            $group[$date][$sid] = [
                'salesmanid'     => $sid,
                'nama_salesman'  => $d['nama_salesman'],
                'city'           => $d['city'],
                'checkin'        => format_time($d['start_time']),
                'checkout'       => format_time($d['end_time']),
                'durasi_jam'     => cal_duration_date($d['start_time'],$d['end_time']),
            ];
        }
        return [
            'labels' => $labels,
            'data'   => $group
        ];
    }

    public function loadParmaCoverage($data)
    {
        if ($data['restrict_level'] == '4') {
			$strquery = " AND a.salesmanid IN (SELECT salesmanid FROM m_sales_salesman WHERE subareaid IN (SELECT distinct b.subareaid FROM  
                app_resource a LEFT JOIN app_restrict_location b ON a.resource_id=b.resource_id 
                WHERE a.username='".$data['usersession']."')
            )";
		} else if ($data['restrict_level'] == '3') {
			$strquery = " AND a.salesmanid IN (SELECT salesmanid FROM m_sales_salesman WHERE areaid IN (SELECT distinct b.areaid FROM  
                app_resource a LEFT JOIN app_restrict_location b ON a.resource_id=b.resource_id 
                WHERE a.username='".$data['usersession']."')
            )";
		} else if ($data['restrict_level'] == '2') {
			$strquery = " AND a.salesmanid IN (SELECT salesmanid FROM m_sales_salesman WHERE regionalid IN (SELECT distinct b.regionalid FROM  
                app_resource a LEFT JOIN app_restrict_location b ON a.resource_id=b.resource_id 
                WHERE a.username='".$data['usersession']."')
            ) ";
		} else {
			$strquery = "";
		}

        $query = "
            SELECT 
                a.regionalid,
                a.nama_regional,
                a.salesmanid,
                a.nama_salesman,
                a.nama_area
            FROM v_gff_info a
            WHERE a.tipe_sales='PAR' AND a.salesmanid NOT IN ('PAR100','PAR101')
            $strquery
            ORDER BY a.nama_regional, a.salesmanid;
        ";
        $data = $this->db->query($query)->result_array();

        $result = [
            'labels' => [],
            'counts' => [],
            'details' => [],
            'total_parma' => 0,
        ];
        $group = [];
        foreach ($data as $d) {
            $reg = $d['nama_regional'];

            if (!isset($group[$reg])) {
                $group[$reg] = [
                    'count' => 0,
                    'salesman' => []
                ];
            }

            $group[$reg]['count']++;

            $group[$reg]['salesman'][] = [
                'salesmanid'   => $d['salesmanid'],
                'nama'         => $d['nama_salesman'],
                'city'         => $d['nama_area']
            ];
        }
        foreach ($group as $regional => $g) {
            $result['labels'][]  = $regional;
            $result['counts'][]  = $g['count'];
            $result['details'][] = $g['salesman'];
        }
        $result['total_parma'] = array_sum($result['counts']);
        
        return $result;
    }
}
