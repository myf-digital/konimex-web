<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setup_dub_model extends CI_Model
{
    public function create($data)
    {
        if (empty($data['dub_detail'])) {
            return [
                'status' => false,
                'message' => 'DUB wajib dipilih'
            ];
        }

        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $data["created_date"] = $datetime->datetime;
        $data["created_by"] = $data["usersession"];

        $execreturn = false;
        $this->db->trans_begin();
       
        $payload = $this->generatePayload($data);
        $execreturn = $this->db->insert('req_dub', $payload);
        if (!$execreturn){
            $this->db->trans_rollback();
            return [
                'status' => false,
                'message' => 'Gagal memnyimpan DUB'
            ];
        }
        $req_no = $this->db->insert_id();

        $details = [];
        $unique_keys = [];
        foreach ($data['dub_detail'] as $customer) {
            $key = $data['salesmanid'] . '_' . $customer['user_id'] . '_' . $customer['customerid'];
            if (in_array($key, $unique_keys)) {
                continue;
            }
            $unique_keys[] = $key;

            $details[] = $this->generatePayload(array_merge($customer, [
                'req_no' => $req_no,
                'salesmanid' => $data['salesmanid'],
                'created_by' => $data['created_by'],
                'created_date' => $data['created_date'],
            ]), 'detail');
        }
        if (!empty($details) && count($details) > 0) {
            $this->db->insert_batch('req_dub_detail', $details);
            $execreturn = true;
        }
        
        if (!empty($data['rolename']) && strpos(strtolower($data['rolename']), 'admin') !== false) {
            $this->db->where('req_no', $req_no);
            $execreturn = $this->db->update('req_dub', [
                'status' => 3,
                'reason' => 'Data DUB telah disetujui oleh ' . $data['usersession'],
            ]);
            if ($execreturn) {
                $this->sync_to_customer_ob($req_no, $data['created_by']);
            }
		}

        if (!$execreturn){
            $this->db->trans_rollback();
            return [
                'status' => false,
                'message' => 'Gagal memnyimpan DUB Detail'
            ];
        } else {
            $this->db->trans_commit();
            return [
                'status' => true,
                'message' => 'Berhasil menyimpan DUB',
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
        if (empty($data['dub_detail'])) {
            return [
                'status' => false,
                'message' => 'DUB wajib dipilih'
            ];
        }

        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $data["modified_date"] = $datetime->datetime;
        $data["modified_by"] = $data["usersession"];

        $execreturn = false;
        $this->db->trans_begin();
       
        $req_no = $data['req_no'];

        $this->db->where('req_no', $req_no);
        $this->db->delete('req_dub_detail');

        $details = [];
        $unique_keys = [];
        foreach ($data['dub_detail'] as $customer) {
            $key = $data['salesmanid'] . '_' . $customer['user_id'] . '_' . $customer['customerid'];
            if (in_array($key, $unique_keys)) {
                continue;
            }
            $unique_keys[] = $key;

            $details[] = $this->generatePayload(array_merge($customer, [
                'req_no' => $req_no,
                'salesmanid' => $data['salesmanid'],
                'created_by' => $data['modified_by'],
                'created_date' => $data['modified_date'],
            ]), 'detail');
        }
        
        if (!empty($details) && count($details) > 0) {
            $this->db->insert_batch('req_dub_detail', $details);
        }

        if (!empty($data['rolename']) && strpos(strtolower($data['rolename']), 'admin') !== false) {
            $data['status'] = 3;
            $data['reason'] = 'Data DUB telah disetujui oleh ' . $data['usersession'];
		}

        $payload = $this->generatePayload($data);
        $this->db->where('req_no', $data['req_no']);
        $execreturn = $this->db->update('req_dub', $payload);
        if (!$execreturn){
            $this->db->trans_rollback();    
            return [
                'status' => false,
                'message' => 'Gagal memnyimpan DUB'
            ];
        } else {
            if (isset($data['status']) && $data['status'] == 3) {
                $this->sync_to_customer_ob($req_no, $data['modified_by']);
            }
        }

        if (!$execreturn){
            $this->db->trans_rollback();
            return [
                'status' => false,
                'message' => 'Gagal memnyimpan DUB Detail'
            ];
        } else {
            $this->db->trans_commit();
            return [
                'status' => true,
                'message' => 'Berhasil menyimpan DUB',
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
        
        $this->db->where('req_no', $data['req_no']);
        $this->db->delete('req_dub');

        $this->db->where('req_no', $data['req_no']);
        $this->db->delete('req_dub_detail');
        
        if (!$this->db->trans_status()){
            $this->db->trans_rollback();
            return [
                'status' => false,
                'message' => 'Gagal menghapus DUB Detail'
            ];
        } else {
            $this->db->trans_commit();
            return [
                'status' => true,
                'message' => 'Berhasil menghapus DUB'
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
                from req_dub a
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
            'created_by',
            'created_date'
        ];
        $fields = $type == 'detail' ? $fieldDetail : $fieldHeader;

        if ($type == 'customer_ob') {
            $fields = [
                'salesmanid',
                'user_id',
                'customerid',
                'created_by',
                'created_date'
            ];
        }
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
                b.nama_customer as outlet, 
                a.user_id, 
                c.nama_professional as user_name
            FROM req_dub_detail a
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
        $req_data = $this->db->get_where('req_dub', ['req_no' => $req_no])->row_array();
        if (empty($req_data)) {
            return [
                'status' => false,
                'message' => 'Data request DUB tidak ditemukan.'
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
        $this->db->update('req_dub', [
            'status' => $status,
            'reason' => $reason,
            'modified_by' => $user,
            'modified_date' => $now
        ]);

        if ($status == 3) {
            $this->sync_to_customer_ob($req_no, $user);
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
                $title = $status == 3 ? 'Approve DUB' : 'Reject DUB';
                $message = $status == 3 ? 'DUB berhasil di Approve oleh ' . ($user ? ' (' . $user . ')' : '') : 'DUB berhasil di Reject oleh ' . ($user ? ' (' . $user . ')' : '');
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
                                'type' => $title,
                                'req_no' => $req_data['req_no'] ?? '',
                                'periode' => $req_data['periode'] ?? '',
                                'salesmanid' => $req_data['salesmanid'] ?? '',
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

    public function sync_to_customer_ob($req_no, $user_create)
    {
        $req = $this->db->get_where('req_dub', ['req_no' => $req_no])->row_array();
        if (!$req) {
            return;
        }

        $salesmanid = $req['salesmanid'];
        
        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $now = $datetime->datetime;

        $this->db->where('salesmanid', $salesmanid);
        $this->db->delete('m_customer_ob');

        $details = $this->db->get_where('req_dub_detail', ['req_no' => $req_no])->result_array();
        $ob = [];
        $unique_ob_keys = [];
        foreach ($details as $detail) {
            $ob_key = $salesmanid . '_' . $detail['user_id'] . '_' . $detail['customerid'];
            if (in_array($ob_key, $unique_ob_keys)) {
                continue;
            }
            $unique_ob_keys[] = $ob_key;

            $ob[] = [
                'salesmanid' => $salesmanid,
                'customerid' => $detail['customerid'],
                'user_id' => $detail['user_id'],
                'created_by' => $user_create,
                'created_date' => $now
            ];
        }
        if (!empty($ob)) {
            $this->db->insert_batch('m_customer_ob', $ob);
        }
    }

    public function get_template_data($salesmanid)
    {
        $salesman_areas = $this->db->get_where('m_salesman_area', ['salesmanid' => $salesmanid])->result_array();
        if (empty($salesman_areas)) {
            return [];
        }

        $subareaids = array_unique(array_filter(array_column($salesman_areas, 'subareaid')));
        $areaids = array_unique(array_filter(array_column($salesman_areas, 'areaid')));
        $regionalids = array_unique(array_filter(array_column($salesman_areas, 'regionalid')));

        $conditions = [];
        if (!empty($subareaids)) {
            $conditions[] = "a.subareaid IN (" . implode(",", $subareaids) . ")";
        }
        if (!empty($areaids)) {
            $conditions[] = "a.areaid IN (" . implode(",", $areaids) . ")";
        }
        if (!empty($regionalids)) {
            $conditions[] = "a.regionalid IN (" . implode(",", $regionalids) . ")";
        }

        if (empty($conditions)) {
            return [];
        }

        $where = " AND (" . implode(" OR ", $conditions) . ")";

        $sql = "
            select distinct
                '".$this->db->escape_str($salesmanid)."' as salesmanid,
                a.customerid,
                a.nama_customer,
                rp.id as user_id,
                rp.nama_professional as nama_user
            from m_customer a
            join ref_professional_mapping rpm on rpm.customerid = a.customerid
            join ref_professional rp on rp.id = rpm.id_professional
            where a.customerid <> '' $where
            order by a.nama_customer asc, rp.nama_professional asc
        ";
        return $this->db->query($sql)->result_array();
    }

    public function get_active_target_dub($salesmanid)
    {
        $sql = "
            select 
                mss.salesmanid,
                mss.nama_salesman,
                mss.tipe_sales,
                role.role_id,
                role.role_name,
                rmt.target_dub,
                rmt.tahun,
                rmt.bulan
            from m_sales_salesman mss
            left join app_role role on role.role_name = mss.tipe_sales
            left join role_mapping_target rmt on rmt.role_id = role.role_id
                and rmt.tahun = YEAR(CURRENT_DATE) and rmt.bulan = MONTH(CURRENT_DATE)
            where mss.salesmanid = ? and mss.aktif = 1
        ";
        return $this->db->query($sql, [$salesmanid])->row_array();
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

        // 1. Validate all salesmen first before running database transactions
        $prepared = [];
        foreach ($grouped_data as $salesmanid => $rows) {
            $salesmanid = trim($salesmanid);
            if ($salesmanid === '') {
                continue;
            }

            $target_info = $this->get_active_target_dub($salesmanid);
            if (empty($target_info)) {
                return [
                    'status' => false,
                    'message' => "MEDREP dengan ID '{$salesmanid}' tidak ditemukan atau tidak aktif."
                ];
            }

            if (empty($target_info['role_id'])) {
                return [
                    'status' => false,
                    'message' => "Role untuk tipe sales '{$target_info['tipe_sales']}' (MEDREP {$salesmanid}) tidak ditemukan."
                ];
            }

            if (is_null($target_info['target_dub']) || (int)$target_info['target_dub'] <= 0) {
                return [
                    'status' => false,
                    'message' => "Target DUB untuk tipe sales '{$target_info['tipe_sales']}' (MEDREP {$salesmanid} - {$target_info['nama_salesman']}) belum diatur pada periode aktif bulan ini."
                ];
            }

            $target_dub = (int)$target_info['target_dub'];

            // Deduplicate items for this salesman
            $unique_keys = [];
            $unique_details = [];
            foreach ($rows as $item) {
                $cid = trim($item['customerid'] ?? '');
                $uid = trim($item['user_id'] ?? '');

                if ($cid === '' || $uid === '') {
                    continue;
                }

                $key = $cid . '_' . $uid;
                if (!isset($unique_keys[$key])) {
                    $unique_keys[$key] = true;
                    $unique_details[] = [
                        'customerid' => $cid,
                        'user_id' => $uid,
                    ];
                }
            }

            $count_user = count($unique_details);
            if ($count_user !== $target_dub) {
                return [
                    'status' => false,
                    'message' => "Jumlah user DUB untuk MEDREP {$salesmanid} ({$target_info['nama_salesman']}) adalah {$count_user} user, wajib tepat {$target_dub} user sesuai target role {$target_info['tipe_sales']} periode aktif."
                ];
            }

            $prepared[$salesmanid] = [
                'target_info' => $target_info,
                'details' => $unique_details
            ];
        }

        if (empty($prepared)) {
            return [
                'status' => false,
                'message' => 'Tidak ada data valid yang dapat diproses.'
            ];
        }

        // 2. Perform database transaction
        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $now = $datetime->datetime;
        $periode = date('Y-m-d');

        $this->db->trans_begin();

        $processed_req_nos = [];
        foreach ($prepared as $salesmanid => $pData) {
            $existing_req = $this->db->get_where('req_dub', ['salesmanid' => $salesmanid])->row_array();

            $status = $isAdmin ? 3 : 1;
            $reason = $isAdmin ? ('Data DUB telah diupload dan disetujui oleh ' . $usersession) : 'Upload Setup DUB';

            if ($existing_req) {
                $req_no = $existing_req['req_no'];
                $this->db->where('req_no', $req_no);
                $this->db->update('req_dub', [
                    'siteid' => 'HIMALAYA',
                    'periode' => $periode,
                    'keterangan' => 'Upload Setup DUB',
                    'status' => $status,
                    'reason' => $reason,
                    'modified_by' => $usersession,
                    'modified_date' => $now
                ]);

                $this->db->where('req_no', $req_no);
                $this->db->delete('req_dub_detail');
            } else {
                $this->db->insert('req_dub', [
                    'siteid' => 'HIMALAYA',
                    'periode' => $periode,
                    'salesmanid' => $salesmanid,
                    'keterangan' => 'Upload Setup DUB',
                    'status' => $status,
                    'reason' => $reason,
                    'created_by' => $usersession,
                    'created_date' => $now
                ]);
                $req_no = $this->db->insert_id();
            }

            $insert_details = [];
            foreach ($pData['details'] as $det) {
                $insert_details[] = [
                    'req_no' => $req_no,
                    'salesmanid' => $salesmanid,
                    'customerid' => $det['customerid'],
                    'user_id' => $det['user_id'],
                    'created_by' => $usersession,
                    'created_date' => $now
                ];
            }

            if (!empty($insert_details)) {
                $this->db->insert_batch('req_dub_detail', $insert_details);
            }

            if ($status == 3) {
                $this->sync_to_customer_ob($req_no, $usersession);
            }

            $processed_req_nos[] = $req_no;
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return [
                'status' => false,
                'message' => 'Gagal menyimpan data upload DUB.'
            ];
        } else {
            $this->db->trans_commit();
            return [
                'status' => true,
                'message' => 'Berhasil upload data DUB.',
                'data' => $processed_req_nos
            ];
        }
    }
}
