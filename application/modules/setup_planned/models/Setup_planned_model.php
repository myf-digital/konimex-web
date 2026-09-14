<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setup_planned_model extends CI_Model
{
    public function create($data)
    {
        if (empty($data['planned_detail'])) {
            return [
                'status' => false,
                'message' => 'Planned wajib dipilih'
            ];
        }

        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $data["created_date"] = $datetime->datetime;
        $data["created_by"] = $data["usersession"];

        $execreturn = false;
        $this->db->trans_begin();
       
        $payload = $this->generatePayload($data);
        $execreturn = $this->db->insert('req_pjp_daily', $payload);
        if (!$execreturn){
            $this->db->trans_rollback();
            return [
                'status' => false,
                'message' => 'Gagal memnyimpan Planned'
            ];
        }
        $req_no = $this->db->insert_id();

        $details = [];
        $unique_keys = [];
        foreach ($data['planned_detail'] as $planned) {
            $key = $planned['periode'] . '_' . $planned['customerid'] . '_' . $planned['user_id'] . '_' . $data['salesmanid'];
            if (in_array($key, $unique_keys)) {
                continue;
            }
            $unique_keys[] = $key;

            $details[] = $this->generatePayload(array_merge($planned, [
                'req_no' => $req_no,
                'salesmanid' => $data['salesmanid'],
                'created_by' => $data['created_by'],
                'created_date' => $data['created_date'],
            ]), 'detail');
        }
        if (!empty($details) && count($details) > 0) {
            $this->db->insert_batch('req_pjp_daily_detail', $details);
            $execreturn = true;
        }
        
        if (!empty($data['rolename']) && strpos(strtolower($data['rolename']), 'admin') !== false) {
            $this->db->where('req_no', $req_no);
            $execreturn = $this->db->update('req_pjp_daily', [
                'status' => 3,
                'reason' => 'Data Planned telah disetujui oleh ' . $data['usersession'],
            ]);
            if ($execreturn) {
                $this->sync_to_rrk($req_no, $data['created_by']);
            }
		}

        if (!$execreturn){
            $this->db->trans_rollback();
            return [
                'status' => false,
                'message' => 'Gagal memnyimpan Planned Detail'
            ];
        } else {
            $this->db->trans_commit();
            return [
                'status' => true,
                'message' => 'Berhasil menyimpan Planned',
                'data' => $req_no
            ];
        }
    }

    public function update($data)
    {
        if (empty($data['req_no'])) {
            return [
                'status' => false,
                'message' => 'req_no wajib diisi.'
            ];
        }
        if (empty($data['planned_detail'])) {
            return [
                'status' => false,
                'message' => 'Planned wajib dipilih'
            ];
        }

        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $data["modified_date"] = $datetime->datetime;
        $data["modified_by"] = $data["usersession"];

        $execreturn = false;
        $this->db->trans_begin();
       
        $req_no = $data['req_no'];

        $old_details = $this->db->get_where('req_pjp_daily_detail', ['req_no' => $req_no])->result_array();
        $old_periodes = array_unique(array_filter(array_column($old_details, 'periode')));

        $this->db->where('req_no', $req_no);
        $this->db->delete('req_pjp_daily_detail');

        $details = [];
        $unique_keys = [];
        foreach ($data['planned_detail'] as $planned) {
            $key = $planned['periode'] . '_' . $planned['customerid'] . '_' . $planned['user_id'] . '_' . $data['salesmanid'];
            if (in_array($key, $unique_keys)) {
                continue;
            }
            $unique_keys[] = $key;

            $details[] = $this->generatePayload(array_merge($planned, [
                'req_no' => $req_no,
                'salesmanid' => $data['salesmanid'],
                'created_by' => $data['modified_by'],
                'created_date' => $data['modified_date'],
            ]), 'detail');
        }
        
        if (!empty($details) && count($details) > 0) {
            $this->db->insert_batch('req_pjp_daily_detail', $details);
        }

        if (!empty($data['rolename']) && strpos(strtolower($data['rolename']), 'admin') !== false) {
            $data['status'] = 3;
            $data['reason'] = 'Data Planned telah disetujui oleh ' . $data['usersession'];
		}

        $payload = $this->generatePayload($data);
        $this->db->where('req_no', $data['req_no']);
        $execreturn = $this->db->update('req_pjp_daily', $payload);
        if (!$execreturn){
            $this->db->trans_rollback();    
            return [
                'status' => false,
                'message' => 'Gagal memnyimpan Planned'
            ];
        } else {
            $current_req = $this->db->get_where('req_pjp_daily', ['req_no' => $req_no])->row_array();
            if ($current_req && $current_req['status'] == 3) {
                $this->sync_to_rrk($req_no, $data['modified_by'], $old_periodes);
            }
        }

        if (!$execreturn){
            $this->db->trans_rollback();
            return [
                'status' => false,
                'message' => 'Gagal memnyimpan Planned Detail'
            ];
        } else {
            $this->db->trans_commit();
            return [
                'status' => true,
                'message' => 'Berhasil menyimpan Planned',
                'data' => $req_no
            ];
        }
    }

    public function delete($data)
    {
        if (empty($data['req_no'])) {
            return [
                'status' => false,
                'message' => 'req_no wajib diisi.'
            ];
        }
        $this->db->trans_begin();

        $this->delete_from_rrk($data['req_no']);
        
        $this->db->where('req_no', $data['req_no']);
        $this->db->delete('req_pjp_daily');

        $this->db->where('req_no', $data['req_no']);
        $this->db->delete('req_pjp_daily_detail');
        
        if (!$this->db->trans_status()){
            $this->db->trans_rollback();
            return [
                'status' => false,
                'message' => 'Gagal menghapus Planned Detail'
            ];
        } else {
            $this->db->trans_commit();
            return [
                'status' => true,
                'message' => 'Berhasil menghapus Planned'
            ];
        }
    }

    public function load($data, $type = 'load')
    {
        $strquery = "";
        if (!empty($data["restrict_level"])) {
            $restrict_query = get_salesman_restrict($data["usersession"], $data["restrict_level"]);
            if ($restrict_query) {
                $strquery = " AND a.salesmanid IN (" . $restrict_query . ")";
            }
        }

        if (!empty($data['salesmanid'])) {
            $strquery .= " and a.salesmanid IN (".$data['salesmanid'].") ";
        }

        $field = " a.* ";
        $table = " ( 
                select
                    a.req_no,
                    a.siteid,
                    a.periode,
                    a.salesmanid,
                    a.keterangan,
                    a.status,
                    a.reason,
                    a.created_by,
                    a.created_date,
                    a.modified_by,
                    a.modified_date,
                    b.nama_salesman
                from req_pjp_daily a
                left join m_sales_salesman b on a.salesmanid = b.salesmanid
                where a.siteid = 'KNX01' $strquery
                order by
                case a.status
                    when 1 then 1
                    when 5 then 2
                    when 3 then 3
                    else 4
                end, a.req_no desc
            ) a";

        if ($type == 'export') {
            $result = $this->db->query("select * from " . $table);
            return $result->result_array();
        }

        if (empty($data['sort'])) {
            $data['sort'] = "case a.status when 1 then 1 when 5 then 2 when 3 then 3 else 4 end, a.req_no";
            $data['order'] = "desc";
        }

        return easy_pagging($data, $field, $table);
    }

    public function generatePayload($data, $type = 'header')
    {
        $fieldHeader = [
            'siteid',
            'periode',
            'salesmanid',
            'salesman_name',
            'keterangan',
            'status',
            'reason',
            'created_by',
            'created_date',
            'modified_by',
            'modified_date'
        ];
        $fieldDetail = [
            'req_no',
            'salesmanid',
            'user_id',
            'customerid',
            'periode',
        ];
        $fields = $type == 'detail' ? $fieldDetail : $fieldHeader;
        $payload = payload($fields, $data);
        return $payload;
    }

    public function get_detail($data)
    {
        if (empty($data['req_no'])) {
            return result(new stdClass(), 422, 'req_no is required');
        }

        $req_nos = is_array($data['req_no']) ? $data['req_no'] : [$data['req_no']];
        if (empty($req_nos)) {
            return result([]);
        }

        $escaped_req_nos = array_map(function($val) {
            return $this->db->escape($val);
        }, $req_nos);

        $sql = "
            SELECT 
                a.req_no,
                a.customerid,
                a.periode,
                b.nama_customer as outlet, 
                a.user_id, 
                c.nama_professional as user_name
            FROM req_pjp_daily_detail a
            LEFT JOIN m_customer b ON a.customerid = b.customerid
            LEFT JOIN ref_professional c ON a.user_id = c.id
            WHERE a.req_no IN (" . implode(',', $escaped_req_nos) . ")
        ";
        $q = $this->db->query($sql);
        return result($q->result_array());
    }

    public function update_status($data)
    {
        if (empty($data['req_no']) || empty($data['status'])) {
            return [
                'status' => false,
                'message' => 'req_no dan status wajib diisi.'
            ];
        }

        $req_no = $data['req_no'];
        $req_data = $this->db->get_where('req_pjp_daily', ['req_no' => $req_no])->row_array();
        if (empty($req_data)) {
            return [
                'status' => false,
                'message' => 'Data request Planned tidak ditemukan.'
            ];
        }

        $status = $data['status'];
        $reason = isset($data['reason']) ? $data['reason'] : '';
        $user = isset($data['usersession']) ? $data['usersession'] : 'Admin';

        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $now = $datetime->datetime;

        $this->db->trans_begin();

        $this->db->where('req_no', $req_no);
        $this->db->update('req_pjp_daily', [
            'status' => $status,
            'reason' => $reason,
            'modified_by' => $user,
            'modified_date' => $now
        ]);

        if ($status == 3) {
            $this->sync_to_rrk($req_no, $user);
        } else {
            $this->delete_from_rrk($req_no);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return [
                'status' => false,
                'message' => 'Gagal mengubah status.'
            ];
        } else {
            $this->db->trans_commit();

            $x_players = get_x_player([$req_data['salesmanid']]);
            if (count($x_players) > 0) {
                $title = $status == 3 ? 'Approve Planned' : 'Reject Planned';
                $message = $status == 3 ? 'Planned berhasil di Approve oleh ' . ($user ? ' (' . $user . ')' : '') : 'Planned berhasil di Reject oleh ' . ($user ? ' (' . $user . ')' : '');
                foreach ($x_players as $xp) {
                    if (isset($xp->account_id)) {
                        if (!empty($xp->telegram_chat_id)) {
                            send_telegram_notif($xp->telegram_chat_id, $title, $message);
                        }
                        send_onesignal_api([
                            'player_ids' => $xp->player_id,
                            'external_ids' => $xp->account_id,
                            'title' => $title,
                            'message' => $message,
                            'data' => [
                                'type' => $status == 3 ? 'Approve Planned' : 'Reject Planned',
                                'req_no' => $req_data['req_no'] ?? '',
                                'periode' => $req_data['periode'] ?? '',
                                'salesmanid' => $req_data['salesmanid'] ?? '',
                                'salesman_name' => $req_data['salesman_name'] ?? '',
                            ],
                        ]);
                    }
                }
            }

            return [
                'status' => true,
                'message' => 'Status berhasil diperbarui.',
                'data' => $req_no
            ];
        }
    }

    public function delete_from_rrk($req_no, $extra_periodes = [])
    {
        $req = $this->db->get_where('req_pjp_daily', ['req_no' => $req_no])->row_array();
        if (!$req) {
            return;
        }

        $salesmanid = $req['salesmanid'];
        $siteid = !empty($req['siteid']) ? $req['siteid'] : 'KNX01';
        $details = $this->db->get_where('req_pjp_daily_detail', ['req_no' => $req_no])->result_array();

        $periodes = array_unique(array_filter(array_merge(
            array_column($details, 'periode'),
            (array)$extra_periodes,
            [$req['periode'] ?? null]
        )));

        if (!empty($periodes)) {
            $this->db->where('salesmanid', $salesmanid);
            $this->db->where('siteid', $siteid);
            $this->db->where_in('periode', $periodes);
            $this->db->delete('t_sales_rrk');

            $this->db->where('salesmanid', $salesmanid);
            $this->db->where('siteid', $siteid);
            $this->db->where_in('periode', $periodes);
            $this->db->delete('t_sales_rrk_user');
        }
    }

    public function sync_to_rrk($req_no, $user_create, $extra_periodes = [])
    {
        $req = $this->db->get_where('req_pjp_daily', ['req_no' => $req_no])->row_array();
        if (!$req) {
            return;
        }

        $salesmanid = $req['salesmanid'];
        $siteid = !empty($req['siteid']) ? $req['siteid'] : 'KNX01';

        $sales = $this->db->get_where('m_sales_salesman', ['salesmanid' => $salesmanid])->row_array();
        $nama_salesman = $sales ? $sales['nama_salesman'] : '';

        $details = $this->db->get_where('req_pjp_daily_detail', ['req_no' => $req_no])->result_array();

        $periodes = array_unique(array_filter(array_merge(
            array_column($details, 'periode'),
            (array)$extra_periodes,
            [$req['periode'] ?? null]
        )));

        if (!empty($periodes)) {
            $this->db->where('salesmanid', $salesmanid);
            $this->db->where('siteid', $siteid);
            $this->db->where_in('periode', $periodes);
            $this->db->delete('t_sales_rrk');

            $this->db->where('salesmanid', $salesmanid);
            $this->db->where('siteid', $siteid);
            $this->db->where_in('periode', $periodes);
            $this->db->delete('t_sales_rrk_user');
        }

        if (empty($details)) {
            return;
        }

        $unique_rrk_keys = [];
        $unique_rrk_user_keys = [];
        $rrk_insert = [];
        $rrk_user_insert = [];
        $now = date('Y-m-d H:i:s');

        foreach ($details as $detail) {
            $rrk_key = $detail['periode'] . '_' . $siteid . '_' . $detail['customerid'] . '_' . $salesmanid;
            if (!in_array($rrk_key, $unique_rrk_keys)) {
                $unique_rrk_keys[] = $rrk_key;
                $rrk_insert[] = [
                    'periode' => $detail['periode'],
                    'siteid' => $siteid,
                    'customerid' => $detail['customerid'],
                    'salesmanid' => $salesmanid,
                    'nama_salesman' => $nama_salesman,
                    'flag_proses' => '0',
                    'user_create' => $user_create,
                    'date_create' => $now,
                ];
            }

            $rrk_user_key = $detail['periode'] . '_' . $siteid . '_' . $detail['customerid'] . '_' . $salesmanid . '_' . $detail['user_id'];
            if (!in_array($rrk_user_key, $unique_rrk_user_keys)) {
                $unique_rrk_user_keys[] = $rrk_user_key;
                $rrk_user_insert[] = [
                    'periode' => $detail['periode'],
                    'siteid' => $siteid,
                    'customerid' => $detail['customerid'],
                    'salesmanid' => $salesmanid,
                    'nama_salesman' => $nama_salesman,
                    'user_id' => $detail['user_id'],
                ];
            }
        }

        if (!empty($rrk_insert)) {
            $this->db->insert_batch('t_sales_rrk', $rrk_insert);
        }

        if (!empty($rrk_user_insert)) {
            $this->db->insert_batch('t_sales_rrk_user', $rrk_user_insert);
        }
    }

    public function get_template_data($salesmanid)
    {
        $salesId = $this->db->escape_str($salesmanid);
        $where = '';
        if (!empty($salesId) && $salesId != 'all') {
            $where = "and a.salesmanid = '".$salesId."'";
        }
        $sql = "
            select distinct
                a.salesmanid,
                rpm.customerid,
                rpm.nama_customer,
                rpm.id_professional as user_id,
                rpm.nama_professional as nama_user
            from m_customer_ob a
            join ref_professional_mapping rpm on rpm.id_professional = a.user_id and rpm.customerid = a.customerid
            where a.customerid <> '' $where
            order by rpm.customerid desc, rpm.id_professional desc
        ";
        $data = $this->db->query($sql)->result_array();
        return $data;
    }

    public function process_upload($grouped_data, $usersession, $rolename)
    {
        if (empty($grouped_data)) {
            return [
                'status' => false,
                'message' => 'Tidak ada data valid yang diupload.'
            ];
        }

        $isAdmin = !empty($rolename) && strpos(strtolower($rolename), 'admin') !== false;

        $prepared = [];
        foreach ($grouped_data as $salesmanid => $rows) {
            $salesmanid = trim($salesmanid);
            if ($salesmanid === '') {
                continue;
            }

            $sales = $this->db->get_where('m_sales_salesman', ['salesmanid' => $salesmanid])->row_array();
            if (!$sales) {
                return [
                    'status' => false,
                    'message' => "Salesman ID '{$salesmanid}' tidak ditemukan dalam database."
                ];
            }

            $unique_keys = [];
            $unique_details = [];
            foreach ($rows as $item) {
                $cid = trim($item['customerid'] ?? '');
                $uid = trim($item['user_id'] ?? '');
                $tanggal = trim($item['tanggal'] ?? '');

                if ($cid === '' || $uid === '' || $tanggal === '') {
                    continue;
                }

                $key = $tanggal . '_' . $cid . '_' . $uid;
                if (!isset($unique_keys[$key])) {
                    $unique_keys[$key] = true;
                    $unique_details[] = [
                        'customerid' => $cid,
                        'periode' => $tanggal,
                        'user_id' => $uid,
                    ];
                }
            }

            if (empty($unique_details)) {
                continue;
            }

            $prepared[$salesmanid] = [
                'salesman_name' => $sales['nama_salesman'],
                'details' => $unique_details
            ];
        }

        if (empty($prepared)) {
            return [
                'status' => false,
                'message' => 'Tidak ada data valid yang dapat diproses.'
            ];
        }

        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $now = $datetime->datetime;
        $periode = date('Y-m-d');

        $this->db->trans_begin();

        $processed_req_nos = [];
        foreach ($prepared as $salesmanid => $pData) {
            $status = $isAdmin ? 3 : 1;
            $reason = $isAdmin ? ('Data Planned telah diupload dan disetujui oleh ' . $usersession) : 'Upload Setup Planned';

            $this->db->insert('req_pjp_daily', [
                'siteid' => 'KNX01',
                'periode' => $periode,
                'salesmanid' => $salesmanid,
                'salesman_name' => $pData['salesman_name'],
                'keterangan' => 'Upload Setup Planned',
                'status' => $status,
                'reason' => $reason,
                'created_by' => $usersession,
                'created_date' => $now
            ]);
            $req_no = $this->db->insert_id();

            $insert_details = [];
            foreach ($pData['details'] as $det) {
                $insert_details[] = [
                    'req_no' => $req_no,
                    'salesmanid' => $salesmanid,
                    'customerid' => $det['customerid'],
                    'periode' => $det['periode'],
                    'user_id' => $det['user_id'] ?? 0,
                ];
            }

            if (!empty($insert_details)) {
                $this->db->insert_batch('req_pjp_daily_detail', $insert_details);
            }

            if ($status == 3) {
                $this->sync_to_rrk($req_no, $usersession);
            }

            $processed_req_nos[] = $req_no;
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return [
                'status' => false,
                'message' => 'Gagal menyimpan data upload Planned.'
            ];
        } else {
            $this->db->trans_commit();
            return [
                'status' => true,
                'message' => 'Berhasil upload data Planned.',
                'data' => $processed_req_nos
            ];
        }
    }
}
