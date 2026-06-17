<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('app_role');
        if (!empty($id)) {
            $data['role_id'] = $id;
        }

        $this->db->trans_begin();

        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $data["created_date"] = $datetime->datetime;
        $data["created_by"] = $data["usersession"];
        
        $data['description'] = $data['role_name'] == 'other' ? null : $data['role_name_desc'];
        $data['role_name'] = $data['role_name'] == 'other' ? $data['role_name_desc'] : $data['role_name'];

        $role = $this->db->query("select * from app_role where lower(role_name) = ?", [strtolower($data['role_name'])])->row();
        if ($role) {
            return [
                'code' => 400,
                'message' => 'Role ' . $data['role_name'] . ' sudah terdaftar.',
            ];
        }

        $payload = $this->generatePayload($data);
        $insert = $this->db->insert('app_role', $payload);
        if ($insert) {
            $roleId = $this->db->insert_id();

            if (!empty($data['periode'])) {
                $periode = explode("-", $data['periode']);
                $data_target['role_id'] = $roleId;
                $data_target['tahun'] = $periode[0] ?? date('Y');
                $data_target['bulan'] = $periode[1] ?? date('m');
                $data_target['target_dub'] = $data['target_dub'];
                $data_target['target_hk'] = $data['target_hk'];
                $data_target['target_call_dub'] = $data['target_call_dub'];
                $data_target['target_call_visit'] = $data['target_call_visit'];
                $insert_target = $this->db->insert('role_mapping_target', $data_target);
                if (!$insert_target) {
                    $this->db->trans_rollback();
                    return [
                        'code' => 400,
                        'message' => 'Mapping target gagal ditambahkan',
                    ];
                }
            }
            
            $this->db->trans_commit();
            return [
                'code' => 200,
                'message' => 'Role berhasil ditambahkan',
                'data' => [
                    'role_id' => $roleId,
                ]
            ];
        }

        $this->db->trans_rollback();
        return [
            'code' => 400,
            'message' => 'Role gagal ditambahkan',
        ];
    }

    public function update($data)
    {
        $role = $this->db->query("select * from app_role where role_id = ?", [$data['role_id'] ?? null])->row();
        if (!$role) {
            return [
                'code' => 404,
                'message' => 'Data Role tidak ditemukan.',
            ];
        }
        $roleId = $role->role_id ?? $data['role_id'];

        $this->db->trans_begin();

        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $data["modified_date"] = $datetime->datetime;
        $data["modified_by"] = $data["usersession"];

        $data['description'] = $data['role_name'] == 'other' ? null : $data['role_name_desc'];
        $data['role_name'] = $data['role_name'] == 'other' ? $data['role_name_desc'] : $data['role_name'];
        $payload = $this->generatePayload($data);
        $this->db->where('role_id', $roleId);
        $update = $this->db->update('app_role', $payload);
        if (!$update) {
            $this->db->trans_rollback();
            return [
                'code' => 400,
                'message' => 'Role gagal diubah',
            ];
        }

        if (!empty($data['periode'])) {
            $periode = explode("-", $data['periode']);
            $data_target['role_id'] = $roleId;
            $data_target['tahun'] = $periode[0] ?? date('Y');
            $data_target['bulan'] = $periode[1] ?? date('m');
            $data_target['target_dub'] = $data['target_dub'];
            $data_target['target_hk'] = $data['target_hk'];
            $data_target['target_call_dub'] = $data['target_call_dub'];
            $data_target['target_call_visit'] = $data['target_call_visit'];

            $target = $this->db->query("
                select *
                from role_mapping_target
                where role_id = ? and tahun = ? and bulan = ?
            ", [
                $data['role_id'] ?? null,
                $data_target['tahun'],
                $data_target['bulan'],
            ])->row();
            if ($target) {
                $this->db->where('role_id', $data['role_id']);
                $this->db->where('tahun', $data_target['tahun']);
                $this->db->where('bulan', $data_target['bulan']);
                $update_target = $this->db->update('role_mapping_target', $data_target);
            } else {
                $update_target = $this->db->insert('role_mapping_target', $data_target);
            }

            if (!$update_target) {
                $this->db->trans_rollback();
                return [
                    'code' => 400,
                    'message' => 'Mapping target gagal diubah',
                ];
            }
        }

        $this->db->trans_commit();
        return [
            'code' => 200,
            'message' => 'Role berhasil diubah',
        ];
    }

    public function delete($data)
    {
        $this->db->where('role_id', $data['role_id']);
        return $this->db->delete('app_role');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 'app_role a order by a.modified_date desc, a.created_date desc';
        return easy_pagging($data, $field, $table);
    }

    public function detail($data)
    {
        if (empty($data['role_id'])) {
            return [
                'status' => false,
                'message' => 'role_id wajib diisi'
            ];
        }
        $sql = "
            select 
                a.role_id,
                a.role_name,
                a.description,
                rmt.tahun,
                rmt.bulan,
                rmt.target_dub,
                rmt.target_hk,
                rmt.target_call_dub,
                rmt.target_call_visit
            from app_role a
            left join role_mapping_target rmt on a.role_id = rmt.role_id
                and rmt.tahun = ?
                and rmt.bulan = ?
            where a.role_id = ?
        ";
        $detail = $this->db->query($sql, [date('Y'), date('m'), $data['role_id']])->row();
        if (empty($detail)) {
            return [
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ];
        }
        if (!empty($data['history'])) {
            $detail->history = $this->db->query("
            select 
                a.role_id,
                a.role_name,
                a.description,
                rmt.tahun,
                rmt.bulan,
                rmt.target_dub,
                rmt.target_hk,
                rmt.target_call_dub,
                rmt.target_call_visit
            from app_role a
            join role_mapping_target rmt on a.role_id = rmt.role_id
            where rmt.tahun <> ? and rmt.bulan <> ? and a.role_id = ?", [date('Y'), date('m'), $data['role_id']])->result();
        }

        return [
            'status' => true,
            'message' => 'success',
            'result' => $detail
        ];
    }

    public function generatePayload($data)
    {
        $fields = [
            'role_name',
            'status',
            'created_by',
            'created_date',
            'modified_by',
            'modified_date'
        ];
        $payload = payload($fields, $data);
        $payload['description'] = $data['description'];
        return $payload;
    }
}
