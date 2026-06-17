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
            if (isset($data['status']) && $data['status'] == 3) {
                $this->sync_to_rrk($req_no, $data['modified_by']);
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
		if ($data["restrict_level"] == '4') { 
			$strquery = " and a.salesmanid in (
                    select salesmanid from m_sales_salesman 
                    where subareaid in (
                        select distinct b.subareaid from  
                            app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                            where a.username='".$data["usersession"]."'
                        )
                )";
		} else if ($data["restrict_level"] == '3') {
			$strquery = " and a.salesmanid in (
                    select salesmanid from m_sales_salesman 
                    where areaid in (
                        select distinct b.areaid from  
                            app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                            where a.username='".$data["usersession"]."'
                        )
                )";
		} else if ($data["restrict_level"] == '2') {
			$strquery = " and a.salesmanid in (
                    select salesmanid from m_sales_salesman 
                    where regionalid in (
                        select distinct b.regionalid from  
                            app_resource a left join app_restrict_location b on a.resource_id=b.resource_id 
                            where a.username='".$data["usersession"]."'
                        )
                ) ";
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
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return [
                'status' => false,
                'message' => 'Gagal mengubah status.'
            ];
        } else {
            $this->db->trans_commit();
            return [
                'status' => true,
                'message' => 'Status berhasil diperbarui.',
                'data' => $req_no
            ];
        }
    }

    public function sync_to_rrk($req_no, $user_create)
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

        $unique_rrk_keys = [];
        $unique_rrk_user_keys = [];

        foreach ($details as $detail) {
            $rrk_key = $detail['periode'] . '_' . $siteid . '_' . $detail['customerid'] . '_' . $salesmanid;
            if (!in_array($rrk_key, $unique_rrk_keys)) {
                $unique_rrk_keys[] = $rrk_key;

                $res_rrk = $this->db->get_where('t_sales_rrk', [
                    'periode' => $detail['periode'],
                    'siteid' => $siteid,
                    'customerid' => $detail['customerid'],
                    'salesmanid' => $salesmanid,
                ])->result_array();

                if (count($res_rrk) < 1) {
                    $this->db->insert('t_sales_rrk', [
                        'periode' => $detail['periode'],
                        'siteid' => $siteid,
                        'customerid' => $detail['customerid'],
                        'salesmanid' => $salesmanid,
                        'nama_salesman' => $nama_salesman,
                        'flag_proses' => '0',
                        'user_create' => $user_create,
                        'date_create' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            $rrk_user_key = $detail['periode'] . '_' . $siteid . '_' . $detail['customerid'] . '_' . $salesmanid . '_' . $detail['user_id'];
            if (!in_array($rrk_user_key, $unique_rrk_user_keys)) {
                $unique_rrk_user_keys[] = $rrk_user_key;

                $res_rrk_user = $this->db->get_where('t_sales_rrk_user', [
                    'periode' => $detail['periode'],
                    'siteid' => $siteid,
                    'customerid' => $detail['customerid'],
                    'salesmanid' => $salesmanid,
                    'user_id' => $detail['user_id'],
                ])->result_array();

                if (count($res_rrk_user) < 1) {
                    $this->db->insert('t_sales_rrk_user', [
                        'periode' => $detail['periode'],
                        'siteid' => $siteid,
                        'customerid' => $detail['customerid'],
                        'salesmanid' => $salesmanid,
                        'nama_salesman' => $nama_salesman,
                        'user_id' => $detail['user_id'],
                    ]);
                }
            }
        }
    }
}
