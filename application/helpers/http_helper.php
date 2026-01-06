<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('send_onesignal')) {
    function send_onesignal($payload) {
        if (!$payload || !isset($payload['player_ids'])) {
            log_message('error', 'Player Ids OneSignal configuration is incomplete.');
            return [
                'status' => false,
                'message' => 'Player Ids OneSignal is incomplete.'
            ];
        }
        if ($payload['title'] != 'Berhasil Login') send_onesignal_api($payload);

        $CI =& get_instance();
        $CI->load->config('onesignal');
        $CI->load->library('Http_client');
        
        $url = $CI->config->item('onesignal_url');
        $url_to = $CI->config->item('onesignal_url_to');
        $app_id = $CI->config->item('onesignal_app_id');
        $rest_api_key = $CI->config->item('onesignal_rest_api_key');
        
        if (empty($url) || empty($app_id) || empty($rest_api_key)) {
            log_message('error', 'OneSignal configuration is incomplete.');
            return [
                'status' => false,
                'message' => 'OneSignal configuration is incomplete.'
            ];
        }

        $headers = [
            'Content-Type' => 'application/json; charset=utf-8',
            'Authorization' => 'Basic ' . $rest_api_key
        ];
        $fields = [
            'app_id' => $app_id,
            'include_player_ids' => is_array($payload['player_ids']) ? $payload['player_ids'] : [$payload['player_ids']],
            'headings' => ['en' => $payload['title']],
            'contents' => ['en' => $payload['message']],
            'url' => $url_to . $payload['url'],
            'data' => $payload['data'],
        ];
        $response = $CI->http_client->request('POST', $url, ['headers' => $headers, 'json' => $fields]);

        log_http([
            'url' => $url,
            'service' => 'onesignal',
            'method' => 'POST',
            'headers' => json_encode($headers),
            'request' => json_encode($fields),
            'response' => json_encode($response),
        ]);
        
        return $response;
    }
}

if (!function_exists('send_onesignal_api')) {
    function send_onesignal_api($payload) {
        if (!$payload || !isset($payload['player_ids'])) {
            log_message('error', 'Player Ids OneSignal configuration is incomplete.');
            return [
                'status' => false,
                'message' => 'Player Ids OneSignal is incomplete.'
            ];
        }

        $CI =& get_instance();
        $CI->load->config('onesignal');
        $CI->load->library('Http_client');
        
        $url = $CI->config->item('onesignal_url');
        $url_to = $CI->config->item('onesignal_url_to');
        $app_id = $CI->config->item('api_onesignal_app_id');
        $rest_api_key = $CI->config->item('api_onesignal_rest_api_key');
        
        if (empty($url) || empty($app_id) || empty($rest_api_key)) {
            log_message('error', 'API OneSignal configuration is incomplete.');
            return [
                'status' => false,
                'message' => 'API OneSignal configuration is incomplete.'
            ];
        }

        $headers = [
            'Content-Type' => 'application/json; charset=utf-8',
            'Authorization' => 'Basic ' . $rest_api_key
        ];
        $fields = [
            'app_id' => $app_id,
            'include_player_ids' => is_array($payload['player_ids']) ? $payload['player_ids'] : [$payload['player_ids']],
            'headings' => ['en' => $payload['title']],
            'contents' => ['en' => $payload['message']],
            'url' => $url_to . $payload['url'],
            'data' => $payload['data'],
        ];
        $response = $CI->http_client->request('POST', $url, ['headers' => $headers, 'json' => $fields]);

        log_http([
            'url' => $url,
            'service' => 'onesignal',
            'method' => 'POST',
            'headers' => json_encode($headers),
            'request' => json_encode($fields),
            'response' => json_encode($response),
        ]);
        
        return $response;
    }
}

if (!function_exists('send_wa')) {
    function send_wa($payload) {
        $CI =& get_instance();
        $CI->load->config('wa_zawa');
        $CI->load->library('Http_client');
        
        $url = $CI->config->item('wa_zawa_url');
        $app_id = $CI->config->item('wa_zawa_id');
        $session_id = $CI->config->item('wa_zawa_session_id');
        
        if (empty($url) || empty($app_id) || empty($session_id)) {
            log_message('error', 'WA Zawa configuration is incomplete.');
            return [
                'status' => false,
                'message' => 'WA Zawa configuration is incomplete.'
            ];
        }

        $headers = [
            'Content-Type' => 'application/json; charset=utf-8',
            'id' => $app_id,
            'session-id' => $session_id,
        ];
        $response = $CI->http_client->request('POST', $url, ['headers' => $headers, 'json' => $payload]);

        log_http([
            'url' => $url,
            'service' => 'wa_zawa',
            'method' => 'POST',
            'headers' => json_encode($headers),
            'request' => json_encode($payload),
            'response' => json_encode($response),
        ]);
        
        return $response;
    }
}

if (!function_exists('format_phone')) {
    function format_phone($phone) {
        $nomor = preg_replace('/[^0-9]/', '', $phone);
        if (empty($nomor)) return false;

        if (substr($nomor, 0, 2) == '62') {
            $nomor = substr($nomor, 2);
        } elseif (substr($nomor, 0, 1) == '0') {
            $nomor = substr($nomor, 1);
        }
        
        $nomor = '62' . $nomor;
        if (strlen($nomor) < 12) return false;
        
        return $nomor;
    }
}

if (!function_exists('log_http')) {
    function log_http($data) {
        $CI =& get_instance();
        return $CI->db->insert('log_http', $data);
    }
}

if (!function_exists('format_date_id')) {
    function format_date_id($datetime, $short = false, $time = true)
    {
        if (!$datetime) return '-';

        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        if ($short) {
            $bulan = [
                1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
            ];
        }

        $timestamp = strtotime($datetime);
        $tgl = date('j', $timestamp);
        $bln = $bulan[(int)date('n', $timestamp)];
        $thn = date('Y', $timestamp);

        if ($time) {
            $jam = date('H:i:s', $timestamp);
            return "$tgl $bln $thn $jam";
        } else {
            return "$tgl $bln $thn";
        }
    }
}
if (!function_exists('format_time')) {
    function format_time($datetime)
    {
        if (!$datetime) return '-';
        $timestamp = strtotime($datetime);
        return date('H:i:s', $timestamp);
    }
}

if (!function_exists('cal_duration_date')) {
    function cal_duration_date($start, $end)
    {
        if ($start && $end) {
            $awal  = new DateTime($start);
            $akhir = new DateTime($end);
            $diff  = $awal->diff($akhir);

            $result = [];
            if ($diff->h) $result[] = sprintf('%d jam', $diff->h);
            if ($diff->i) $result[] = sprintf('%d menit', $diff->i);
            if ($diff->s) $result[] = sprintf('%d detik', $diff->s);
            return implode(' ', $result);
        }
        return '-';
    }
}

if (!function_exists('date_interval')) {
    function date_interval($start, $end)
    {
        if ($start && $end) {
            $period = new DatePeriod(
                new DateTime($start),
                new DateInterval('P1D'),
                (new DateTime($end))->modify('+1 day')
            );

            $tanggal_array = [];
            foreach ($period as $date) {
                $tanggal_array[] = $date->format('Y-m-d');
            }
            return $tanggal_array;
        }
        return [];
    }
}

if (!function_exists('format_jarak')) {
    function format_jarak($meter)
    {
        if (!$meter) return '-';
        
        if ($meter >= 1000) {
            $km = floor($meter / 1000);
            $m  = $meter % 1000;
            return number_format($km,0,'.','.') . ' km ' . number_format($m,0,'.','.') . ' m';
        } else {
            return number_format($meter,0,'.','.') . ' m';
        }
    }
}

if (!function_exists('number_to_alphabet')) {
    function number_to_alphabet($number)
    {
        $result = '';
        while ($number > 0) {
            $mod = ($number - 1) % 26;
            $result = chr(65 + $mod) . $result;
            $number = (int)(($number - $mod) / 26);
        }
        return $result;
    }
}

if (!function_exists('sending_email')) {
    function sending_email($to, $subject, $template, $data = [], $attachments = [])
    {
        $CI =& get_instance();
        
        $CI->load->config('email');
        $CI->load->library('email');
        $CI->load->helper('email');
        $CI->email->initialize($CI->config->item('email'));

        $message = $CI->load->view($template, $data, TRUE);

        $CI->email->from('noreply@alphaciptatech.com', 'PAR-MA');
        $CI->email->to($to);
        $CI->email->subject($subject);
        $CI->email->message($message);

        $cc = $CI->config->item('email')['cc'] ?? [];
        if (!is_array($cc)) {
            $cc = explode(',', $cc);
        }

        if (isset($data['cc_to']) && count($data['cc_to']) > 0) {
            foreach ($data['cc_to'] as $cc_to) {
                if (valid_email($cc_to) && !in_array($cc_to, $cc)) {
                    $cc[] = $cc_to;
                }
            }
        }
        if (!empty($cc) && count($cc) > 0) {
            $CI->email->cc($cc);
        }

        if (!empty($attachments)) {
            foreach ((array) $attachments as $file) {
                if (file_exists($file)) {
                    $CI->email->attach($file);
                }
            }
        }

        $sent = $CI->email->send();
        $CI->email->clear(TRUE);

        return $sent;
    }
}

if (!function_exists('render_tables_html')) {
    function render_tables_html($tables)
    {
        $html = "";
        // OUTLET COVERAGE
        if (!empty($tables['outlet_coverage'])) {
            $html .= "
                <h3>Outlet Coverage</h3>
                <table border='1' cellspacing='0' cellpadding='5' width='100%' style='border-collapse: collapse;'>
                    <thead>
                        <tr style='background:#eee'>
                            <th>PAR-MA</th>
                            <th>Nama PAR-MA</th>
                            <th>Area</th>
                            <th>Apotik</th>
                            <th>Clinic</th>
                            <th>Hospital</th>
                        </tr>
                    </thead>
                <tbody>
            ";
            foreach ($tables['outlet_coverage'] as $oc) {
                $html .= "<tr>
                            <td>{$oc->parma}</td>
                            <td>{$oc->nama_parma}</td>
                            <td>{$oc->nama_area}</td>
                            <td>{$oc->Apotik}</td>
                            <td>{$oc->Clinic}</td>
                            <td>{$oc->Hospital}</td>
                          </tr>";
            }
            $html .= "</tbody></table><br>";
        }

        // TARGET CALL MONTHLY
        if (!empty($tables['target_call_monthly'])) {
            $html .= "
                <h3>Target Call Monthly</h3>
                <table border='1' cellspacing='0' cellpadding='5' width='100%' style='border-collapse: collapse;'>
                <thead>
                    <tr style='background:#eee'>
                        <th>PAR-MA</th>
                        <th>Nama PAR-MA</th>
                        <th>Area</th>
                        <th>Target</th>
                        <th>Call</th>
                        <th>Extra Call</th>
                        <th>Actual</th>
                    </tr>
                </thead>
                <tbody>
            ";
            foreach ($tables['target_call_monthly'] as $tcm) {
                $html .= "<tr>
                            <td>{$tcm->parma}</td>
                            <td>{$tcm->nama_parma}</td>
                            <td>{$tcm->nama_area}</td>
                            <td>{$tcm->target_call}</td>
                            <td>{$tcm->Call}</td>
                            <td>{$tcm->ExtraCall}</td>
                            <td>{$tcm->actual_call}</td>
                          </tr>";
            }
            $html .= "</tbody></table><br>";
        }

        // TARGET CALL DAILY
        if (!empty($tables['target_call_daily'])) {
            $html .= "
                <h3>Target Call Daily</h3>
                <table border='1' cellspacing='0' cellpadding='5' width='100%' style='border-collapse: collapse;'>
                    <thead>
                        <tr style='background:#eee'>
                            <th>Tanggal</th>
                            <th>PAR-MA</th>
                            <th>Nama PAR-MA</th>
                            <th>Area</th>
                            <th>Target</th>
                            <th>Call</th>
                            <th>Extra Call</th>
                            <th>Actual</th>
                        </tr>
                    </thead>
                <tbody>
            ";
            foreach ($tables['target_call_daily'] as $tcd) {
                $periode = format_date_id($tcd->periode, true, false);
                $html .= "<tr>
                            <td>{$periode}</td>
                            <td>{$tcd->parma}</td>
                            <td>{$tcd->nama_parma}</td>
                            <td>{$tcd->nama_area}</td>
                            <td>{$tcd->target_call}</td>
                            <td>{$tcd->Call}</td>
                            <td>{$tcd->ExtraCall}</td>
                            <td>{$tcd->actual_call}</td>
                          </tr>";
            }
            $html .= "</tbody></table><br>";
        }

        return $html ?: "<p>Tidak ada data tersedia.</p>";
    }
}

if (!function_exists('render_order_pending_html')) {
    function render_order_pending_html($list)
    {
        $html = "
            <style>
                body {
                    font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
                    font-size: 10pt;
                    color: #000;
                }
                table {
                    border-collapse: collapse;
                    width: 100%;
                    margin-bottom: 8px;
                }
                td, th {
                    padding: 6px 8px;
                    vertical-align: top;
                }
                .no-border td {
                    border: none !important;
                }
                .header-table td {
                    border: none !important;
                    font-size: 10pt;
                }
                .details th {
                    background-color: #f3f3f3;
                    border: 1px solid #444;
                    text-align: left;
                }
                .details td {
                    border: 1px solid #444;
                }
                .details td, .details th {
                    padding: 5px 8px;
                }
                .spacer {
                    height: 15px;
                }
                hr {
                    border: none;
                    border-top: 1px solid #999;
                    margin: 12px 0;
                }
            </style>
        ";
        if (!empty($list) && count($list) > 0) {
            foreach ($list as $op) {
                $header = $op['headers'];

                $html .= "
                    <table class='header-table'>
                        <tr>
                            <td width='50%'>
                                <table class='no-border'>
                                    <tr>
                                        <td width='25%'>No PO</td>
                                        <td width='1%'>:</td>
                                        <td>{$header['no_po']}</td>
                                    </tr>
                                    <tr>
                                        <td>No Sales</td>
                                        <td>:</td>
                                        <td>{$header['no_sales']}</td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal</td>
                                        <td>:</td>
                                        <td>{$header['tanggal']}</td>
                                    </tr>
                                </table>
                            </td>
                            <td width='50%'>
                                <table class='no-border'>
                                    <tr>
                                        <td width='25%'>Customer</td>
                                        <td width='1%'>:</td>
                                        <td>{$header['nama_customer']}</td>
                                    </tr>
                                    <tr>
                                        <td>Parma</td>
                                        <td>:</td>
                                        <td>{$header['salesman']}</td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td>:</td>
                                        <td>{$header['status']}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                ";

                // Detail produk
                $details = $op['details'];
                if (!empty($details) && count($details) > 0) {
                    $html .= "
                    <table border='1' class='details'>
                        <thead>
                            <tr>
                                <th width='30'>No</th>
                                <th>Produk</th>
                                <th>Brand</th>
                                <th width='50' style='text-align:right;'>Qty</th>
                                <th width='80' style='text-align:right;'>Harga</th>
                                <th width='90' style='text-align:right;'>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                    ";

                    $n = 1;
                    foreach ($details as $d) {
                        $qty   = number_format($d->qty_kecil, 0, '.', ',');
                        $h_jual = number_format($d->h_jual, 0, '.', ',');
                        $total = number_format(($d->qty_kecil * $d->h_jual), 0, '.', ',');

                        $html .= "
                            <tr>
                                <td align='center'>{$n}</td>
                                <td>{$d->nama_invoice}</td>
                                <td>{$d->nama_brand}</td>
                                <td align='right'>{$qty}</td>
                                <td align='right'>{$h_jual}</td>
                                <td align='right'>{$total}</td>
                            </tr>
                        ";
                        $n++;
                    }

                    $html .= "</tbody></table>";
                }

                $html .= "<div class='spacer'></div><hr>";
            }
        } else {
            $html = "<p>Tidak ada data tersedia.</p>";
        }

        return $html;
    }
}

if (!function_exists('group_order_by_no_po')) {
    function group_order_by_no_po($orders)
    {
        $result = [];
        if (empty($orders)) return $result;

        foreach ($orders as $row) {;
            $no_po = $row->no_po;

            if (!isset($result[$no_po])) {
                $result[$no_po] = [
                    'headers' => [
                        'no_po'          => $row->no_po,
                        'no_sales'       => $row->no_sales,
                        'tanggal'        => format_date_id($row->tanggal, true, false),
                        'nama_customer'  => $row->nama_customer,
                        'salesman'       => $row->salesmanid . ' - ' . $row->nama_salesman,
                        'status'         => get_status_po_label($row->status_po ?? null),
                    ],
                    'details' => []
                ];
            }

            $result[$no_po]['details'][] = $row;
        }
        return $result;
    }
}

if (!function_exists('get_status_po_label')) {
    function get_status_po_label($status_po)
    {
        switch ($status_po) {
            case '1': return 'Pending';
            case '2': return 'On Progress';
            case '3': return 'Closing';
            default:  return 'Pending';
        }
    }
}