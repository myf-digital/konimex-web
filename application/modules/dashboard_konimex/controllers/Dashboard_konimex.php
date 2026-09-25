<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_konimex extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Dashboard_konimex_model', 'dashboard_konimex');
    }

    public function index()
    {
        $data['regional_list'] = $this->dashboard_konimex->get_regional_list();
        $this->template->show($this, 'v_dashboard_main', $data);
    }

    /**
     * Endpoint JSON: Summary 7 KPI Utama
     */
    public function load_kpi()
    {
        $data = param_input();
        $result = $this->dashboard_konimex->load_summary_kpi($data);
        responseJSON($result);
    }

    /**
     * Endpoint JSON: Trend Bulanan (Jan - Des)
     */
    public function load_trend()
    {
        $data = param_input();
        $result = $this->dashboard_konimex->load_trend_monthly($data);
        responseJSON($result);
    }

    /**
     * Endpoint Cascading Filter: Get Area (ASM) by Regional (SM)
     */
    public function get_area_by_regional()
    {
        $regionalid = $this->input->post('regionalid');
        $result = $this->dashboard_konimex->get_area_list($regionalid);
        responseJSON($result);
    }

    /**
     * Endpoint Cascading Filter: Get Subarea (ASS/MRC) by Area (ASM)
     */
    public function get_subarea_by_area()
    {
        $areaid = $this->input->post('areaid');
        $result = $this->dashboard_konimex->get_subarea_list($areaid);
        responseJSON($result);
    }

    /**
     * Endpoint Cascading Filter: Get Salesman (TPE)
     */
    public function get_salesman_by_filter()
    {
        $data = param_input();
        $result = $this->dashboard_konimex->get_salesman_list($data);
        responseJSON($result);
    }

    /**
     * Endpoint JSON: Level 2 Breakdown Area & Performa TPE
     */
    public function load_breakdown_area()
    {
        $data = param_input();
        $result = $this->dashboard_konimex->load_breakdown_area($data);
        responseJSON($result);
    }

    /**
     * Endpoint JSON: Level 2 Breakdown Channel & Spesialisasi
     */
    public function load_breakdown_channel_spesialis()
    {
        $data = param_input();
        $result = $this->dashboard_konimex->load_breakdown_channel_spesialis($data);
        responseJSON($result);
    }

    /**
     * Endpoint JSON: Level 2 Breakdown Target & Realisasi Produk Wajib
     */
    public function load_breakdown_produk()
    {
        $data = param_input();
        $result = $this->dashboard_konimex->load_breakdown_produk($data);
        responseJSON($result);
    }

    /**
     * Endpoint JSON: Level 3 Detail Aktivitas Harian TPE (Log Kunjungan, Detailing, Presensi)
     */
    public function load_detail_tpe()
    {
        $data = param_input();
        $result = $this->dashboard_konimex->load_detail_tpe_activity($data);
        responseJSON($result);
    }

    /**
     * Endpoint Export Excel: Breakdown Area & Performa TPE
     */
    public function export_excel_area()
    {
        $tahun = $this->input->get('tahun') ?? date('Y');
        $bulan = $this->input->get('bulan') ?? date('m');
        $data = [
            'tahun' => $tahun,
            'bulan' => $bulan,
            'regionalid' => $this->input->get('regionalid'),
            'areaid' => $this->input->get('areaid'),
            'subareaid' => $this->input->get('subareaid'),
            'salesmanid' => $this->input->get('salesmanid')
        ];

        $rows = $this->dashboard_konimex->load_breakdown_area($data);

        $filename = "Breakdown_Performa_TPE_{$tahun}_{$bulan}.xls";
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo '<table border="1">';
        echo '<tr style="background-color: #2c3e50; color: #fff;">
                <th>No</th>
                <th>Area SM (Regional)</th>
                <th>Area ASM</th>
                <th>ASS / MRC</th>
                <th>NIK / Salesman ID</th>
                <th>Nama Salesman</th>
                <th>Tipe</th>
                <th>Hadir</th>
                <th>Total HK</th>
                <th>% Hadir</th>
                <th>Total Call</th>
                <th>Total EC</th>
                <th>% EC</th>
                <th>Act Kat A</th>
                <th>Tgt Kat A</th>
                <th>% Kat A</th>
                <th>Act User Wajib</th>
                <th>Tgt User Wajib</th>
                <th>% User Wajib</th>
                <th>Total Detailing</th>
                <th>Act Sales Qty</th>
                <th>Tgt Sales Qty</th>
                <th>% Sales</th>
              </tr>';

        $no = 1;
        foreach ($rows as $r) {
            echo "<tr>
                    <td>{$no}</td>
                    <td>" . htmlspecialchars($r['nama_regional'] ?? '') . "</td>
                    <td>" . htmlspecialchars($r['nama_area'] ?? '') . "</td>
                    <td>" . htmlspecialchars($r['nama_subarea'] ?? '') . "</td>
                    <td>" . htmlspecialchars($r['salesmanid'] ?? '') . "</td>
                    <td>" . htmlspecialchars($r['nama_salesman'] ?? '') . "</td>
                    <td>" . htmlspecialchars($r['tipe_sales'] ?? '') . "</td>
                    <td>{$r['total_hadir']}</td>
                    <td>{$r['total_hari']}</td>
                    <td>{$r['pct_absensi']}%</td>
                    <td>{$r['total_call']}</td>
                    <td>{$r['total_ec']}</td>
                    <td>{$r['pct_ec']}%</td>
                    <td>{$r['act_kat_a']}</td>
                    <td>{$r['tgt_kat_a']}</td>
                    <td>{$r['pct_kat_a']}%</td>
                    <td>{$r['act_user_wajib']}</td>
                    <td>{$r['tgt_user_wajib']}</td>
                    <td>{$r['pct_user_wajib']}%</td>
                    <td>{$r['total_detailing']}</td>
                    <td>{$r['act_selling']}</td>
                    <td>{$r['tgt_selling']}</td>
                    <td>{$r['pct_selling']}%</td>
                  </tr>";
            $no++;
        }
        echo '</table>';
        exit;
    }
}
