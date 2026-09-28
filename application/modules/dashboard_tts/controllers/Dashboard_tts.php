<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_tts extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $username = strtolower($_SESSION['user_login'] ?? $_SESSION['user_credential'] ?? '');
        if ($username !== 'hartanto') {
            return redirect('app_dashboard');
        }
        $this->load->model('Dashboard_tts_model', 'dashboard_tts');
    }

    public function index()
    {
        $data['regional_list'] = $this->dashboard_tts->get_regional_list();
        $this->template->show($this, 'content', $data);
    }

    public function load_kpi()
    {
        session_write_close();
        $data = param_input();
        $result = $this->dashboard_tts->load_summary_kpi($data);
        responseJSON($result);
    }

    public function load_trend()
    {
        session_write_close();
        $data = param_input();
        $result = $this->dashboard_tts->load_trend_monthly($data);
        responseJSON($result);
    }

    public function get_area_by_regional()
    {
        session_write_close();
        $regionalid = $this->input->post('regionalid');
        $result = $this->dashboard_tts->get_area_list($regionalid);
        responseJSON($result);
    }

    public function get_subarea_by_area()
    {
        session_write_close();
        $areaid = $this->input->post('areaid');
        $result = $this->dashboard_tts->get_subarea_list($areaid);
        responseJSON($result);
    }

    public function get_salesman_by_filter()
    {
        session_write_close();
        $data = param_input();
        $result = $this->dashboard_tts->get_salesman_list($data);
        responseJSON($result);
    }

    public function load_breakdown_absensi()
    {
        session_write_close();
        $data = param_input();
        $result = $this->dashboard_tts->load_breakdown_absensi($data);
        responseJSON($result);
    }

    public function load_breakdown_call()
    {
        session_write_close();
        $data = param_input();
        $result = $this->dashboard_tts->load_breakdown_call($data);
        responseJSON($result);
    }

    public function load_breakdown_kategori()
    {
        session_write_close();
        $data = param_input();
        $result = $this->dashboard_tts->load_breakdown_kategori($data);
        responseJSON($result);
    }

    public function load_breakdown_user_wajib()
    {
        session_write_close();
        $data = param_input();
        $result = $this->dashboard_tts->load_breakdown_user_wajib($data);
        responseJSON($result);
    }

    public function load_breakdown_detailing()
    {
        session_write_close();
        $data = param_input();
        $result = $this->dashboard_tts->load_breakdown_detailing($data);
        responseJSON($result);
    }

    public function load_breakdown_selling()
    {
        session_write_close();
        $data = param_input();
        $result = $this->dashboard_tts->load_breakdown_selling($data);
        responseJSON($result);
    }

    public function load_breakdown_join_visit()
    {
        session_write_close();
        $data = param_input();
        $result = $this->dashboard_tts->load_breakdown_join_visit($data);
        responseJSON($result);
    }

    public function get_detail_join_visit()
    {
        session_write_close();
        $req_no = $this->input->post('req_no') ?? $this->input->get('req_no');
        $result = $this->dashboard_tts->get_detail_join_visit($req_no);
        responseJSON($result);
    }

    public function load_breakdown_tpe_aktif()
    {
        session_write_close();
        $data = param_input();
        $result = $this->dashboard_tts->load_breakdown_tpe_aktif($data);
        responseJSON($result);
    }

    public function load_breakdown_area()
    {
        session_write_close();
        $data = param_input();
        $result = $this->dashboard_tts->load_breakdown_absensi($data);
        responseJSON($result);
    }

    public function load_breakdown_channel_spesialis()
    {
        session_write_close();
        $data = param_input();
        $result = $this->dashboard_tts->load_breakdown_user_wajib($data);
        responseJSON($result);
    }

    public function load_breakdown_produk()
    {
        session_write_close();
        $data = param_input();
        $result = $this->dashboard_tts->load_breakdown_detailing($data);
        responseJSON($result);
    }

    public function load_detail_tpe()
    {
        session_write_close();
        $data = param_input();
        $result = $this->dashboard_tts->load_detail_tpe_activity($data);
        responseJSON($result);
    }

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

        $rows = $this->dashboard_tts->load_breakdown_area($data);

        $filename = "Breakdown_Performa_TPE_{$tahun}_{$bulan}.xls";
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo '<table border="1">';
        echo '<tr style="background-color: #2c3e50; color: #fff;">
                <th>No</th>
                <th>Regional</th>
                <th>Area</th>
                <th>Sub Area</th>
                <th>Salesman ID</th>
                <th>Salesman</th>
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
