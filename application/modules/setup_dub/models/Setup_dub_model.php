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
                where a.siteid = 'AXION' $strquery
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
                foreach ($x_players as $xp) {
                    if (isset($xp->account_id)) {
                        send_onesignal_api([
                            'player_ids' => $xp->player_id,
                            'external_ids' => $xp->account_id,
                            'title' => $status == 3 ? 'Approve DUB' : 'Reject DUB',
                            'message' => $status == 3 ? 'DUB berhasil di Approve oleh ' . ($user ? ' (' . $user . ')' : '') : 'DUB berhasil di Reject oleh ' . ($user ? ' (' . $user . ')' : ''),
                            'data' => array_merge(
                                ['type' => $status == 3 ? 'Approve DUB' : 'Reject DUB'], [
                                    'req_no' => $req_data['req_no'] ?? '',
                                    'periode' => $req_data['periode'] ?? '',
                                    'salesmanid' => $req_data['salesmanid'] ?? '',
                                ]),
                            'url' => '/setup_dub',
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
}
