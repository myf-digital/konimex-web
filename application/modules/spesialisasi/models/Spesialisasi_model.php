<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Spesialisasi_model extends CI_Model
{
    public function load($data)
    {
        $field = "a.* ";
        $table = " (
                    SELECT a.*
                    FROM ref_spesialisasi a
                    ORDER BY a.id DESC
                ) a ";
        return easy_pagging($data, $field, $table);
    }

    public function detail($data)
    {
        $this->db->where_in('id', $data['id']);
        $result = $this->db
            ->from('ref_spesialisasi')
            ->get()->result_array();
        return $result;
    }

    public function create($data)
    {
        return $this->update($data);
    }

    public function update($data)
    {
        $error = '';
        if (empty($data['spesialisasi'])) {
            $error = "Spesialisasi wajib diisi.";
        }

        if (!empty($error)) {
            return [
                'code' => 422,
                'message' => $error,
                'result' => false
            ];
        }

        $id = $data['id'] ?? null;
        $spesialisasi = $data['spesialisasi'] ?? null;
        if (empty($id)) {
            $this->db->insert('ref_spesialisasi', [
                'name' => $spesialisasi,
            ]);
            $id = $this->db->insert_id();
        } else {
            $this->db->where('id', $id);
            $this->db->update('ref_spesialisasi', [
                'name' => $spesialisasi,
            ]);
        }

        return [
            'code' => 200,
            'message' => 'Success',
            'result' => [
                'id' => $id,
                'spesialisasi' => $spesialisasi,
            ]
        ];
    }

    public function savetoxlsx($data)
    {
        $sql = "
                SELECT a.*
                FROM ref_spesialisasi a
                ORDER BY a.id DESC
            ";
		return $this->db->query($sql)->result_array();
    }
}
