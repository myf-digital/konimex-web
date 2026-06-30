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

        $target_data = $data;

        unset($data['spesialisasiid']);
        unset($data['periode_spesialisasi']);
        unset($data['target_spesialisasi']);
        unset($data['productid']);
        unset($data['periode_product']);
        unset($data['target_product']);
        unset($data['target_product_qty']);

        $payload = $this->generatePayload($data);
        $insert = $this->db->insert('app_role', $payload);
        if ($insert) {
            $roleId = $this->db->insert_id();

            if (!empty($target_data['periode'])) {
                $periode = explode("-", $target_data['periode']);
                $data_target['role_id'] = $roleId;
                $data_target['tahun'] = $periode[0] ?? date('Y');
                $data_target['bulan'] = $periode[1] ?? date('m');
                $data_target['target_dub'] = $target_data['target_dub'];
                $data_target['target_hk'] = $target_data['target_hk'];
                $data_target['target_call_dub'] = $target_data['target_call_dub'];
                $data_target['target_call_visit'] = $target_data['target_call_visit'];
                $insert_target = $this->db->insert('role_mapping_target', $data_target);
                if (!$insert_target) {
                    $this->db->trans_rollback();
                    return [
                        'code' => 400,
                        'message' => 'Mapping target gagal ditambahkan',
                    ];
                }
            }
            
            $this->save_targets($target_data, $roleId, $target_data['role_name']);

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

        $target_data = $data;

        unset($data['spesialisasiid']);
        unset($data['periode_spesialisasi']);
        unset($data['target_spesialisasi']);
        unset($data['productid']);
        unset($data['periode_product']);
        unset($data['target_product']);
        unset($data['target_product_qty']);

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

        if (!empty($target_data['periode'])) {
            $periode = explode("-", $target_data['periode']);
            $data_target['role_id'] = $roleId;
            $data_target['tahun'] = $periode[0] ?? date('Y');
            $data_target['bulan'] = $periode[1] ?? date('m');
            $data_target['target_dub'] = $target_data['target_dub'];
            $data_target['target_hk'] = $target_data['target_hk'];
            $data_target['target_call_dub'] = $target_data['target_call_dub'];
            $data_target['target_call_visit'] = $target_data['target_call_visit'];

            $target = $this->db->query("
                select *
                from role_mapping_target
                where role_id = ? and tahun = ? and bulan = ?
            ", [
                $target_data['role_id'] ?? null,
                $data_target['tahun'],
                $data_target['bulan'],
            ])->row();
            if ($target) {
                $this->db->where('role_id', $target_data['role_id']);
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

        $this->save_targets($target_data, $roleId, $target_data['role_name']);

        $this->db->trans_commit();
        return [
            'code' => 200,
            'message' => 'Role berhasil diubah',
        ];
    }

    public function delete($data)
    {
        $this->db->trans_begin();

        $this->db->where('role_id', $data['role_id']);
        $this->db->delete('m_sales_spesialis_target');

        $this->db->where('role_id', $data['role_id']);
        $this->db->delete('m_sales_produk_target');

        $this->db->where('role_id', $data['role_id']);
        $this->db->delete('app_role');

        $this->db->trans_commit();
        return [
            'code' => 200,
            'message' => 'Role berhasil dihapus',
        ];
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
            where (rmt.tahun <> ? or rmt.bulan <> ?) and a.role_id = ?", [date('Y'), date('m'), $data['role_id']])->result();

        $detail->spesialis_active = $this->db->query("
            select tahun, bulan, nama_spesialisasi, target
            from m_sales_spesialis_target
            where role_id = ? and tahun = ? and bulan = ?
            order by nama_spesialisasi asc
        ", [$data['role_id'], date('Y'), date('m')])->result_array();

        $detail->spesialis_history = $this->db->query("
            select tahun, bulan, nama_spesialisasi, target
            from m_sales_spesialis_target
            where role_id = ? and (tahun <> ? or bulan <> ?)
            order by tahun desc, bulan desc, nama_spesialisasi asc
        ", [$data['role_id'], date('Y'), date('m')])->result_array();

        $detail->produk_active = $this->db->query("
            select tahun, bulan, product_id, nama_invoice, target, target_qty
            from m_sales_produk_target
            where role_id = ? and tahun = ? and bulan = ?
            order by nama_invoice asc
        ", [$data['role_id'], date('Y'), date('m')])->result_array();

        $detail->produk_history = $this->db->query("
            select tahun, bulan, product_id, nama_invoice, target, target_qty
            from m_sales_produk_target
            where role_id = ? and (tahun <> ? or bulan <> ?)
            order by tahun desc, bulan desc, nama_invoice asc
        ", [$data['role_id'], date('Y'), date('m')])->result_array();

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

    public function spesialisasi($data)
    {
        return $this->db->from('ref_spesialisasi')->order_by('name', 'ASC')->get()->result_array();
    }

    public function products($data)
    {
        if (!empty($data['q'])) {
            $this->db->group_start();
            $this->db->like('productid', $data['q']);
            $this->db->or_like('nama_invoice', $data['q']);
            $this->db->group_end();
        }
        $this->db->order_by('nama_invoice', 'ASC');
        $this->db->limit(50);
        return $this->db->from('m_product')->get()->result_array();
    }

    public function get_spesialisasi_targets($data)
    {
        if (empty($data['role_id']) || empty($data['periode'])) {
            return [];
        }
        $parts = explode('-', $data['periode']);
        $tahun = intval($parts[0]);
        $bulan = intval($parts[1]);

        $this->db->where('role_id', $data['role_id']);
        $this->db->where('tahun', $tahun);
        $this->db->where('bulan', $bulan);
        return $this->db->get('m_sales_spesialis_target')->result_array();
    }

    public function get_product_targets($data)
    {
        if (empty($data['role_id']) || empty($data['periode'])) {
            return [];
        }
        $parts = explode('-', $data['periode']);
        $tahun = intval($parts[0]);
        $bulan = intval($parts[1]);

        $this->db->where('role_id', $data['role_id']);
        $this->db->where('tahun', $tahun);
        $this->db->where('bulan', $bulan);
        return $this->db->get('m_sales_produk_target')->result_array();
    }

    public function save_targets($data, $roleId, $roleName)
    {
        $usersession = $data['usersession'] ?? $this->session->userdata('username') ?? 'Admin';
        
        if (isset($data['periode_spesialisasi'])) {
            $periode_spesialisasi = $data['periode_spesialisasi'];
            if (!empty($periode_spesialisasi)) {
                $splitDate = explode('-', $periode_spesialisasi);
                $tahun = $splitDate[0] ?? date('Y');
                $bulan = $splitDate[1] ?? date('m');

                $this->db->where('role_id', $roleId);
                $this->db->where('tahun', $tahun);
                $this->db->where('bulan', $bulan);
                $this->db->delete('m_sales_spesialis_target');

                $spesialisasi_ids = $data['spesialisasiid'] ?? [];
                $target_spesialisasi = $data['target_spesialisasi'] ?? [];

                if (!empty($spesialisasi_ids)) {
                    $this->db->where_in('id', $spesialisasi_ids);
                    $specialties = $this->db->get('ref_spesialisasi')->result_array();
                    $specialtyMap = [];
                    foreach ($specialties as $s) {
                        $specialtyMap[$s['id']] = $s['name'];
                    }

                    $insertSpesialisasi = [];
                    foreach ($spesialisasi_ids as $spId) {
                        $tgt = isset($target_spesialisasi[$spId]) ? intval($target_spesialisasi[$spId]) : 0;
                        $insertSpesialisasi[] = [
                            'tahun' => $tahun,
                            'bulan' => $bulan,
                            'role_id' => $roleId,
                            'role_name' => $roleName,
                            'spesialisasi_id' => $spId,
                            'nama_spesialisasi' => $specialtyMap[$spId] ?? '',
                            'target' => $tgt,
                            'created_by' => $usersession,
                            'created_date' => date('Y-m-d H:i:s'),
                        ];
                    }
                    if (!empty($insertSpesialisasi)) {
                        $this->db->insert_batch('m_sales_spesialis_target', $insertSpesialisasi);
                    }
                }
            }
        }

        if (isset($data['periode_product'])) {
            $periode_product = $data['periode_product'];
            if (!empty($periode_product)) {
                $splitDate = explode('-', $periode_product);
                $tahun = $splitDate[0] ?? date('Y');
                $bulan = $splitDate[1] ?? date('m');

                $this->db->where('role_id', $roleId);
                $this->db->where('tahun', $tahun);
                $this->db->where('bulan', $bulan);
                $this->db->delete('m_sales_produk_target');

                $product_ids = $data['productid'] ?? [];
                $target_product = $data['target_product'] ?? [];
                $target_product_qty = $data['target_product_qty'] ?? [];

                if (!empty($product_ids)) {
                    $this->db->where_in('productid', $product_ids);
                    $products = $this->db->get('m_product')->result_array();
                    $productMap = [];
                    foreach ($products as $p) {
                        $productMap[$p['productid']] = $p['nama_invoice'];
                    }

                    $insertProduct = [];
                    foreach ($product_ids as $prodId) {
                        $tgt = isset($target_product[$prodId]) ? intval($target_product[$prodId]) : 0;
                        $tgt_qty = isset($target_product_qty[$prodId]) ? intval($target_product_qty[$prodId]) : 0;
                        $insertProduct[] = [
                            'tahun' => $tahun,
                            'bulan' => $bulan,
                            'role_id' => $roleId,
                            'role_name' => $roleName,
                            'product_id' => $prodId,
                            'nama_invoice' => $productMap[$prodId] ?? '',
                            'target' => $tgt,
                            'target_qty' => $tgt_qty,
                            'created_by' => $usersession,
                            'created_date' => date('Y-m-d H:i:s'),
                        ];
                    }
                    if (!empty($insertProduct)) {
                        $this->db->insert_batch('m_sales_produk_target', $insertProduct);
                    }
                }
            }
        }
    }
}
