<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Salesman_relationship_model extends CI_Model
{
    public function create($data)
    {
		$sql = "
            select a.*
            from m_salesman_relationship a
            where a.siteid = '".$data['siteid']."'
            and a.salesmanid = '".$data['salesmanid']."'
            and a.relationship = '".$data['relationship']."'
        ";
		$result_array = $this->db->query($sql);
		$relationship = $result_array->result();

        if (count($relationship) > 0) {
            return [
                'status' => false,
                'message' => 'Keluarga ' . $data['relationship'] . ' sudah ada'
            ];
        }

        $fields = [
            "siteid",
            "salesmanid",
            "nama_salesman",
            "relationship",
            "nama_relationship",
            "jenis_kelamin",
            "tanggal_lahir",
            "keterangan",
            "created_by",
            "created_date",
        ];
        $payload = payload($fields, $data);
        return $this->db->insert('m_salesman_relationship', $payload);
    }

    public function update($data)
    {
        $fields = [
            "nama_relationship",
            "jenis_kelamin",
            "tanggal_lahir",
            "keterangan",
            "modified_by",
            "modified_date",
        ];
        $payload = payload($fields, $data);

        $this->db->where('siteid', $data['siteid']);
        $this->db->where('salesmanid', $data['salesmanid']);
        $this->db->where('relationship', $data['relationship']);
        return $this->db->update('m_salesman_relationship', $payload);
    }

    public function delete($data)
    {
        $this->db->where('siteid', $data['siteid']);
        $this->db->where('salesmanid', $data['salesmanid']);
        $this->db->where('relationship', $data['relationship']);
        return $this->db->delete('m_salesman_relationship');
    }

    public function load($data)
    {
        $field = " a.* ";
        $table = " (
            select a.*, b.desc as relationship_desc
            from m_salesman_relationship a
            left join ref_param_global b on b.value = a.relationship and b.key_param = 'key_relationship'
        ) a";
        return easy_pagging($data, $field, $table);
    }
}
