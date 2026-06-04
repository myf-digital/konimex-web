<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Professional_model extends CI_Model
{
    public function load($data)
    {
        $field = "a.* ";
        $table = " (
                    SELECT
                        a.id,
                        a.nama_professional,
                        a.spesialisasi_id,
                        rs.name as spesialisasi,
                        concat('".URL_IMAGE."', a.url_foto) as url_foto,
                        concat('".URL_IMAGE."', a.url_img_signature) as url_img_signature,
                        GROUP_CONCAT(
                            DISTINCT CONCAT(rpm.customerid, ' - ', rpm.nama_customer, ' - ', mc.typeid)
                            ORDER BY rpm.customerid 
                            SEPARATOR '||'
                        ) as customer_list
                    FROM ref_professional a
                    LEFT JOIN ref_spesialisasi rs ON rs.id = a.spesialisasi_id
                    LEFT JOIN ref_professional_mapping rpm ON rpm.id_professional = a.id
                    LEFT JOIN m_customer mc ON mc.customerid = rpm.customerid
                    WHERE rpm.customerid IS NOT NULL
                    GROUP BY a.id
                    ORDER BY a.id DESC
                ) a ";
        return easy_pagging($data, $field, $table);
    }

    public function detail($data)
    {
        $this->db->where_in('id', $data['id_professional']);
        $result = $this->db
            ->from('ref_professional')
            ->get()->result_array();
        return $result;
    }

    public function outlet($data)
    {
        if (!empty($data['q'])) {
            $this->db->group_start();
            $this->db->like('customerid', $data['q']);
            $this->db->or_like('nama_customer', $data['q']);
            $this->db->or_like('typeid', $data['q']);
            $this->db->group_end();
        }
        $this->db->where('customerid IS NOT NULL', null, false);
        $this->db->where('customerid <>', '');
        $this->db->order_by('customerid', 'DESC');
        $this->db->limit(50);
        $result = $this->db->from('m_customer')->get()->result_array();
        return $result;
    }

    public function spesialisasi($data)
    {
        $result = $this->db->from('ref_spesialisasi')->get()->result_array();
        return $result;
    }

    public function create($data)
    {
        return $this->update($data);
    }

    public function update($data)
    {
        $error = '';
        if (empty($data['professional'])) {
            $error = "Profesional wajib diisi.";
        } else if (empty($data['spesialisasi'])) {
            $error = "Spesialisasi wajib dipilih.";
        } else if (empty($data['customerid'])) {
            $error = "Outlet wajib dipilih.";
        }

        if (!empty($error)) {
            return [
                'code' => 422,
                'message' => $error,
                'result' => false
            ];
        }

        $professionalId = $data['id_professional'] ?? null;
        $namaProfessional = $data['professional'] ?? null;
        if (empty($professionalId)) {
            $this->db->insert('ref_professional', [
                'siteid' => "KNX01",
                'nama_professional' => $namaProfessional,
                'spesialisasi_id' => $data['spesialisasi'],
                'created_by' => $data['usersession'],
                'created_date' => date('Y-m-d H:i:s'),
            ]);
            $professionalId = $this->db->insert_id();
        } else {
            $this->db->where('id', $professionalId);
            $this->db->update('ref_professional', [
                'nama_professional' => $namaProfessional,
                'spesialisasi_id' => $data['spesialisasi'],
                'modified_by' => $data['usersession'],
                'modified_date' => date('Y-m-d H:i:s'),
            ]);
        }

        $professional = $this->db->get_where('ref_professional', ['id' => $professionalId])->row_array();
        
        $outlets = $this->db->where_in('customerid', $data['customerid'])->get('m_customer')->result_array();
        $outletMap = [];
        foreach ($outlets as $o) {
            $outletMap[$o['customerid']] = $o['nama_customer'];
        }

        $mappingData = [];
        foreach ($data['customerid'] as $customerId) {
            $mappingData[] = [
                'id_professional' => $professionalId,
                'nama_professional' => $professional['nama_professional'],
                'customerid' => $customerId,
                'nama_customer' => $outletMap[$customerId] ?? '',
            ];
        }
        
        $status = false;
        if (!empty($mappingData)) {
            $this->db->trans_start();
            $this->db->where('id_professional', $professionalId);
            $this->db->delete('ref_professional_mapping');
            $this->db->insert_batch('ref_professional_mapping', $mappingData);
            $this->db->trans_complete();
            $status = $this->db->trans_status();
        }

        return [
            'code' => 200,
            'message' => 'Success',
            'result' => $status
        ];
    }

    public function savetoxlsx($data)
    {
        $sql = "
                SELECT
                    a.id,
                    a.nama_professional,
                    a.spesialisasi_id,
                    rs.name as spesialisasi,
                    concat('".URL_IMAGE."', a.url_foto) as url_foto,
                    concat('".URL_IMAGE."', a.url_img_signature) as url_img_signature,
                    GROUP_CONCAT(
                        DISTINCT CONCAT(rpm.customerid, ' - ', rpm.nama_customer, ' - ', mc.typeid)
                        ORDER BY rpm.customerid 
                        SEPARATOR '||'
                    ) as customer_list
                FROM ref_professional a
                LEFT JOIN ref_spesialisasi rs ON rs.id = a.spesialisasi_id
                LEFT JOIN ref_professional_mapping rpm ON rpm.id_professional = a.id
                LEFT JOIN m_customer mc ON mc.customerid = rpm.customerid
                WHERE rpm.customerid IS NOT NULL
                GROUP BY a.id
                ORDER BY a.id DESC
            ";
		return $this->db->query($sql)->result_array();
    }

    public function load_target($data)
    {
        $field = "a.* ";
        $table = " (
                    SELECT
                        a.*,
                        COALESCE(s.total_actual, 0) as total_actual
                    FROM target_professional a
                    LEFT JOIN (
                        SELECT 
                            tsm.customerid,
                            tsm.userid,
                            tsd.productid,
                            SUM(tsd.qty_kecil) as total_actual
                        FROM t_sales_detail tsd
                        JOIN t_sales_master tsm ON tsm.no_po = tsd.no_po
                        GROUP BY tsd.productid
                    ) s ON s.userid = a.id_professional AND s.customerid = a.customerid AND s.productid = a.productid
                    WHERE a.id_professional IS NOT NULL
                ) a ";
        return easy_pagging($data, $field, $table);
    }

    public function load_history($data)
    {
        $field = "a.* ";
        $table = " (
                    select a.*
                    from target_uploads a
                ) a ";
        return easy_pagging($data, $field, $table);
    }

    public function load_history_detail($data)
    {
        $uploadId = $data['id'] ? str_replace('/', '', $data['id']) : null;

        $field = "a.* ";
        $table = " (
                    select a.*
                    from target_professional_history a
                    where a.upload_id = '{$uploadId}'
                ) a ";
        return easy_pagging($data, $field, $table);
    }

    public function insert_batch_on_duplicate($table, $data, $update_fields = [])
    {
        if (empty($data)) return false;

        $CI =& get_instance();

        // ambil kolom dari index pertama
        $columns = array_keys($data[0]);

        // escape nama kolom
        $escaped_columns = array_map(function($col) use ($CI) {
            return $CI->db->protect_identifiers($col);
        }, $columns);

        $values = [];

        foreach ($data as $row) {
            $row_values = [];

            foreach ($columns as $col) {
                $row_values[] = $CI->db->escape($row[$col]);
            }

            $values[] = "(" . implode(',', $row_values) . ")";
        }

        // build query utama
        $sql = "INSERT INTO " . $CI->db->protect_identifiers($table) .
            " (" . implode(',', $escaped_columns) . ") VALUES " .
            implode(',', $values);

        // kalau tidak ditentukan, update semua kolom kecuali primary key (opsional)
        if (empty($update_fields)) {
            $update_fields = $columns;
        }

        // build ON DUPLICATE KEY UPDATE
        $updates = [];
        foreach ($update_fields as $field) {
            $updates[] = $CI->db->protect_identifiers($field) . 
                        " = VALUES(" . $CI->db->protect_identifiers($field) . ")";
        }

        $sql .= " ON DUPLICATE KEY UPDATE " . implode(', ', $updates);

        return $CI->db->query($sql);
    }
}
