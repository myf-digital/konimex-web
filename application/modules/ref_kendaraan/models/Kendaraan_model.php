<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kendaraan_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_kendaraan');
        if (!empty($id)) {
            $data['siteid'] = $id;
        }
        return $this->db->insert('m_kendaraan', $data);
    }

    public function update($data)
    {
        $this->db->where('siteid', $data['siteid']);
        $this->db->where('kendaraanid', $data['kendaraanid']);
        return $this->db->update('m_kendaraan', $data);
    }

    public function delete($data)
    {
        $this->db->where('siteid', $data['siteid']);
        $this->db->where('kendaraanid', $data['kendaraanid']);
        return $this->db->delete('m_kendaraan');
    }

    public function load($data)
    {
        $field = "a.*, b.nama_salesman ";
        $table = 'm_kendaraan a';
        $join = " left join m_sales_salesman b on a.salesmanid=b.salesmanid ";
        return easy_pagging($data, $field, $table.$join);
    }

}
