<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_dub_model extends CI_Model
{
    public function get_date_week($data)
    {
        $rawPeriode = !empty($data['periode']) ? substr(trim((string) $data['periode']), 0, 7) : date('Y-m');
        $monthTimestamp = strtotime($rawPeriode . '-01');
        if ($monthTimestamp === false) {
            $monthTimestamp = time();
        }
        $yearMonth = date('Y-m', $monthTimestamp);
        $lastDayOfMonth = date('t', $monthTimestamp);

        $week = !empty($data['week']) ? strtolower(trim((string) $data['week'])) : 'all';
        switch ($week) {
            case '1':
                $startPeriode = "{$yearMonth}-01";
                $endPeriode   = "{$yearMonth}-07";
                break;
            case '2':
                $startPeriode = "{$yearMonth}-08";
                $endPeriode   = "{$yearMonth}-14";
                break;
            case '3':
                $startPeriode = "{$yearMonth}-15";
                $endPeriode   = "{$yearMonth}-21";
                break;
            case '4':
                $startPeriode = "{$yearMonth}-22";
                $endPeriode   = "{$yearMonth}-{$lastDayOfMonth}";
                break;
            default:
                $startPeriode = "{$yearMonth}-01";
                $endPeriode   = "{$yearMonth}-{$lastDayOfMonth}";
                break;
        }

        return [$startPeriode, $endPeriode];
    }

    private function get_sales_roles()
    {
        $roleRow = $this->db->query("SELECT value FROM ref_param_global WHERE key_param = 'key_role_sales' LIMIT 1")->row_array();
        if (!empty($roleRow['value'])) {
            return array_values(array_filter(array_map('trim', explode('|', $roleRow['value']))));
        }
        return [];
    }

    public function get_salesman($data)
    {
        $usersession = $data['usersession'] ?? '';
        $restrictLevel = $data['restrict_level'] ?? '';

        $where = "WHERE s.aktif = 1 AND LOWER(TRIM(s.nama_salesman)) NOT LIKE '%vacant%'";

        if (!empty($usersession) && !empty($restrictLevel)) {
            $restrictQuery = get_salesman_restrict($usersession, $restrictLevel);
            if ($restrictQuery) {
                $where .= " AND s.salesmanid IN ({$restrictQuery})";
            }
        }

        $roles = $this->get_sales_roles();
        $params = [];
        if (!empty($roles)) {
            $placeholders = implode(',', array_fill(0, count($roles), '?'));
            $where .= " AND s.tipe_sales IN ({$placeholders})";
            $params = array_merge($params, $roles);
        } else {
            $where .= " AND s.tipe_sales NOT IN ('ADMIN', 'SPV', 'FC')";
        }

        $sql = "
            SELECT
                s.salesmanid,
                s.nama_salesman,
                s.tipe_sales,
                COALESCE(NULLIF(area.nama_subarea, ''), NULLIF(area.nama_area, ''), NULLIF(area.nama_regional, ''), '-') AS wilayah
            FROM m_sales_salesman s
            LEFT JOIN (
                SELECT
                    msa.salesmanid,
                    GROUP_CONCAT(DISTINCT sa.nama_area ORDER BY sa.nama_area ASC SEPARATOR ', ') AS nama_subarea,
                    GROUP_CONCAT(DISTINCT ar.nama_area ORDER BY ar.nama_area ASC SEPARATOR ', ') AS nama_area,
                    GROUP_CONCAT(DISTINCT r.nama_regional ORDER BY r.nama_regional ASC SEPARATOR ', ') AS nama_regional
                FROM m_salesman_area msa
                LEFT JOIN m_area_subarea sa ON msa.subareaid = sa.subareaid
                LEFT JOIN m_area_areasite ar ON msa.areaid = ar.areaid
                LEFT JOIN m_area_regional r ON msa.regionalid = r.regionalid
                GROUP BY msa.salesmanid
            ) area ON area.salesmanid = s.salesmanid
            {$where}
            ORDER BY s.nama_salesman ASC, s.salesmanid ASC
        ";

        return $this->db->query($sql, $params)->result_array();
    }

    public function get_salesman_dub_achievement($salesmanId, $targetDub, $targetCallVisit, $startDate, $endDate)
    {
        $limit = max(0, (int) round((float) $targetDub));
        if (empty($salesmanId) || $limit <= 0) {
            return [
                'percentage_dub' => 0,
                'source_dub'     => '-',
                'data_dub'       => [],
            ];
        }

        $obMeta = $this->db->query(
            "SELECT COUNT(*) AS cnt, DATE_FORMAT(MAX(created_date), '%Y-%m-%d') AS max_dt
             FROM m_customer_ob
             WHERE salesmanid = ? AND user_id > 0",
            [$salesmanId]
        )->row_array();

        $obCount = (int) ($obMeta['cnt'] ?? 0);
        $obMaxDt = $obMeta['max_dt'] ?? null;

        $useCustomerOb = ($obCount > 0 && ($obMaxDt === null || $obMaxDt <= $endDate));
        $dubRows = [];
        $sourceDub = '-';

        if (!$useCustomerOb) {
            $sqlLatestReq = "
                SELECT rd.req_no
                FROM req_dub rd
                WHERE rd.status = 3
                    AND (
                        rd.salesmanid = ?
                        OR EXISTS (
                            SELECT 1
                            FROM req_dub_detail rdd_chk
                            WHERE rdd_chk.req_no = rd.req_no
                                AND rdd_chk.salesmanid = ?
                                AND rdd_chk.user_id > 0
                        )
                    )
                    AND COALESCE(DATE(rd.modified_date), rd.periode, DATE(rd.created_date)) <= ?
                ORDER BY COALESCE(rd.modified_date, rd.created_date) DESC, rd.req_no DESC
                LIMIT 1
            ";
            $latestReq = $this->db->query($sqlLatestReq, [$salesmanId, $salesmanId, $endDate])->row_array();

            if (!empty($latestReq['req_no'])) {
                $sourceDub = 'Request DUB #' . (int) $latestReq['req_no'];
                $sqlReqDubDetail = "
                    SELECT
                        rdd.customerid,
                        MAX(mc.nama_customer) AS nama_customer,
                        rdd.user_id,
                        MAX(rp.nama_professional) AS nama_professional,
                        MAX(rp.type) AS tipe_pic,
                        MAX(rs.name) AS spesialisasi,
                        COALESCE(MAX(tvd_cnt.actual_call), 0) AS actual_call
                    FROM req_dub_detail rdd
                    JOIN req_dub rd ON rd.req_no = rdd.req_no
                    LEFT JOIN m_customer mc ON mc.customerid = rdd.customerid
                        AND (mc.customerid_m = '' OR mc.customerid_m IS NULL)
                    LEFT JOIN ref_professional rp ON rp.id = rdd.user_id
                    LEFT JOIN ref_spesialisasi rs ON rs.id = rp.spesialisasi_id
                    LEFT JOIN (
                        SELECT customerid, user_id, COUNT(*) AS actual_call
                        FROM trx_visit_detailing
                        WHERE salesmanid = ? AND periode BETWEEN ? AND ?
                        GROUP BY customerid, user_id
                    ) tvd_cnt ON tvd_cnt.customerid = rdd.customerid AND tvd_cnt.user_id = rdd.user_id
                    WHERE rdd.req_no = ? AND rdd.salesmanid = ? AND rdd.user_id > 0
                    GROUP BY rdd.customerid, rdd.user_id
                    ORDER BY MAX(COALESCE(rdd.created_date, rd.modified_date, rd.created_date)) DESC,
                        rdd.customerid ASC,
                        rdd.user_id ASC
                    LIMIT {$limit}
                ";
                $dubRows = $this->db->query($sqlReqDubDetail, [
                    $salesmanId,
                    $startDate,
                    $endDate,
                    $latestReq['req_no'],
                    $salesmanId,
                ])->result_array();
            } elseif ($obCount > 0) {
                $useCustomerOb = true;
            }
        }

        if ($useCustomerOb) {
            $sourceDub = 'Master DUB Aktif';
            $sqlCustomerOb = "
                SELECT
                    mco.customerid,
                    mc.nama_customer,
                    mco.user_id,
                    rp.nama_professional,
                    rp.type AS tipe_pic,
                    rs.name AS spesialisasi,
                    COALESCE(tvd_cnt.actual_call, 0) AS actual_call
                FROM m_customer_ob mco
                LEFT JOIN m_customer mc ON mc.customerid = mco.customerid
                    AND (mc.customerid_m = '' OR mc.customerid_m IS NULL)
                LEFT JOIN ref_professional rp ON rp.id = mco.user_id
                LEFT JOIN ref_spesialisasi rs ON rs.id = rp.spesialisasi_id
                LEFT JOIN (
                    SELECT customerid, user_id, COUNT(*) AS actual_call
                    FROM trx_visit_detailing
                    WHERE salesmanid = ? AND periode BETWEEN ? AND ?
                    GROUP BY customerid, user_id
                ) tvd_cnt ON tvd_cnt.customerid = mco.customerid AND tvd_cnt.user_id = mco.user_id
                WHERE mco.salesmanid = ? AND mco.user_id > 0
                ORDER BY mco.created_date DESC, mco.customerid ASC, mco.user_id ASC
                LIMIT {$limit}
            ";
            $dubRows = $this->db->query($sqlCustomerOb, [
                $salesmanId,
                $startDate,
                $endDate,
                $salesmanId,
            ])->result_array();
        }

        $targetPerDokter = ($targetDub > 0 && $targetCallVisit > 0)
            ? ((float) $targetCallVisit / (float) $targetDub)
            : 0.0;
        $targetCallRounded = round($targetPerDokter, 2);

        $sumCappedRatio = 0.0;
        $dataDub = [];
        foreach ($dubRows as $dRow) {
            $actualCall = (int) ($dRow['actual_call'] ?? 0);
            $cappedRatio = $targetPerDokter > 0 ? min(1.0, $actualCall / $targetPerDokter) : 0.0;
            $sumCappedRatio += $cappedRatio;

            $dataDub[] = [
                'customerid'        => $dRow['customerid'],
                'nama_customer'     => $dRow['nama_customer'],
                'user_id'           => (int) $dRow['user_id'],
                'nama_professional' => $dRow['nama_professional'],
                'tipe_pic'          => $dRow['tipe_pic'] ?? '',
                'spesialisasi'      => $dRow['spesialisasi'] ?? '',
                'actual_call'       => $actualCall,
                'target_call'       => $targetCallRounded,
                'percentage_call'   => round($cappedRatio, 2),
            ];
        }

        $percentageDub = $targetDub > 0 ? round($sumCappedRatio / (float) $targetDub, 2) : 0;

        return [
            'percentage_dub' => $percentageDub,
            'source_dub'     => $sourceDub,
            'data_dub'       => $dataDub,
        ];
    }

    public function load_report($data)
    {
        $periode = !empty($data['periode']) ? substr(trim((string) $data['periode']), 0, 7) : date('Y-m');
        $parts = explode('-', $periode);
        $year = isset($parts[0]) ? (int) $parts[0] : (int) date('Y');
        $month = isset($parts[1]) ? (int) $parts[1] : (int) date('m');

        if (!empty($data['week']) && strtolower((string) $data['week']) !== 'all') {
            [$startDate, $endDate] = $this->get_date_week([
                'periode' => sprintf('%04d-%02d', $year, $month),
                'week'    => $data['week'],
            ]);
        } else {
            $startDate = sprintf('%04d-%02d-01', $year, $month);
            $endDate = date('Y-m-t', strtotime($startDate));
        }

        $sqlTargets = "
            SELECT
                ar.role_name AS tipe_sales,
                rmt.target_dub,
                rmt.target_call_visit
            FROM role_mapping_target rmt
            JOIN app_role ar ON ar.role_id = rmt.role_id
            WHERE rmt.tahun = ? AND rmt.bulan = ?
        ";
        $targetsRes = $this->db->query($sqlTargets, [$year, $month])->result_array();
        $targetsMap = [];
        foreach ($targetsRes as $t) {
            $targetsMap[strtolower(trim((string) $t['tipe_sales']))] = $t;
        }

        $selectedSalesman = !empty($data['salesmanid'])
            ? trim((string) $data['salesmanid'])
            : '';

        if ($selectedSalesman === '') {
            return [
                'periode'    => sprintf('%04d-%02d', $year, $month),
                'week'       => !empty($data['week']) ? (string) $data['week'] : 'all',
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'summary'    => [],
                'rows'       => [],
            ];
        }

        if (strtolower($selectedSalesman) === 'all') {
            $salesmen = $this->get_salesman($data);
        } else {
            $whereSm = "WHERE s.salesmanid = ?";
            $paramsSm = [$selectedSalesman];

            if (!empty($data['usersession']) && !empty($data['restrict_level'])) {
                $restrictQuery = get_salesman_restrict($data['usersession'], $data['restrict_level']);
                if ($restrictQuery) {
                    $whereSm .= " AND s.salesmanid IN ({$restrictQuery})";
                }
            }

            $salesmen = $this->db->query("
                SELECT
                    s.salesmanid,
                    s.nama_salesman,
                    s.tipe_sales,
                    COALESCE(NULLIF(area.nama_subarea, ''), NULLIF(area.nama_area, ''), NULLIF(area.nama_regional, ''), '-') AS wilayah
                FROM m_sales_salesman s
                LEFT JOIN (
                    SELECT
                        msa.salesmanid,
                        GROUP_CONCAT(DISTINCT sa.nama_area ORDER BY sa.nama_area ASC SEPARATOR ', ') AS nama_subarea,
                        GROUP_CONCAT(DISTINCT ar.nama_area ORDER BY ar.nama_area ASC SEPARATOR ', ') AS nama_area,
                        GROUP_CONCAT(DISTINCT r.nama_regional ORDER BY r.nama_regional ASC SEPARATOR ', ') AS nama_regional
                    FROM m_salesman_area msa
                    LEFT JOIN m_area_subarea sa ON msa.subareaid = sa.subareaid
                    LEFT JOIN m_area_areasite ar ON msa.areaid = ar.areaid
                    LEFT JOIN m_area_regional r ON msa.regionalid = r.regionalid
                    GROUP BY msa.salesmanid
                ) area ON area.salesmanid = s.salesmanid
                {$whereSm}
                LIMIT 1
            ", $paramsSm)->result_array();
        }

        $summaryList = [];
        $detailRows = [];

        foreach ($salesmen as $sm) {
            $roleKey = strtolower(trim((string) ($sm['tipe_sales'] ?? '')));
            $targetDub = isset($targetsMap[$roleKey]) ? (float) $targetsMap[$roleKey]['target_dub'] : 0.0;
            $targetCallVisit = isset($targetsMap[$roleKey]) ? (float) $targetsMap[$roleKey]['target_call_visit'] : 0.0;

            $dubMetrics = $this->get_salesman_dub_achievement(
                $sm['salesmanid'],
                $targetDub,
                $targetCallVisit,
                $startDate,
                $endDate
            );

            $dataDub = $dubMetrics['data_dub'];

            $totalActualCall = 0;
            foreach ($dataDub as $item) {
                $totalActualCall += (int) ($item['actual_call'] ?? 0);
                $detailRows[] = [
                    'salesmanid'        => $sm['salesmanid'],
                    'nama_salesman'     => $sm['nama_salesman'],
                    'tipe_sales'        => $sm['tipe_sales'],
                    'wilayah'           => $sm['wilayah'],
                    'percentage_dub'    => $dubMetrics['percentage_dub'],
                    'source_dub'        => $dubMetrics['source_dub'],
                    'customerid'        => $item['customerid'],
                    'nama_customer'     => $item['nama_customer'],
                    'user_id'           => $item['user_id'],
                    'nama_professional' => $item['nama_professional'],
                    'tipe_pic'          => $item['tipe_pic'],
                    'spesialisasi'      => $item['spesialisasi'],
                    'target_call'       => $item['target_call'],
                    'actual_call'       => $item['actual_call'],
                    'percentage_call'   => $item['percentage_call'],
                ];
            }

            $targetPerDokter = ($targetDub > 0 && $targetCallVisit > 0)
                ? round($targetCallVisit / $targetDub, 2)
                : 0;

            $summaryList[] = [
                'salesmanid'      => $sm['salesmanid'],
                'nama_salesman'   => $sm['nama_salesman'],
                'tipe_sales'      => $sm['tipe_sales'],
                'wilayah'         => $sm['wilayah'],
                'target_dub'      => (int) round($targetDub),
                'registered_dub'  => count($dataDub),
                'target_per_doc'  => $targetPerDokter,
                'total_actual'    => $totalActualCall,
                'percentage_dub'  => $dubMetrics['percentage_dub'],
                'source_dub'      => $dubMetrics['source_dub'],
            ];
        }

        return [
            'periode'    => sprintf('%04d-%02d', $year, $month),
            'week'       => !empty($data['week']) ? (string) $data['week'] : 'all',
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'summary'    => $summaryList,
            'rows'       => $detailRows,
        ];
    }
}

