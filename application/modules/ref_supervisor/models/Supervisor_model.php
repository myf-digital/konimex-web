<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_salesman_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_sales_salesman');
        if (!empty($id)) {
            $data['siteid'] = $id;
        }
        $this->db->select_max('gudangid','gudangid');
        $idgudang = $this->db->get('m_gudang')->row()->gudangid;
        $data_array_gudang = array(
            "siteid" => $data['siteid'],
            "gudangid" => $idgudang+1,
            "gudang_name" => $data['salesmanid']." - GUDANG KANVAS",
            "status_aktif" => "1"
        );
        $this->db->insert('m_gudang', $data_array_gudang);

        $data_array_category = array(
            "siteid" => $data['siteid'],
            "categoryid" => "11",
            "salesmanid" => $data['salesmanid'],
            "nama_category" => "CATEGORY"." - ".$data['salesmanid']
        );
        $this->db->insert('m_sales_salesman_category', $data_array_category);

        $data['gudangid']=$data_array_gudang["gudangid"];
        $data['supervisorid']="SPV01";
        $data['categoryid']="11";

        return $this->db->insert('m_sales_salesman', $data);
    }

    public function update($data)
    {
        $this->db->where('siteid', $data['siteid']);
        $this->db->where('salesmanid', $data['salesmanid']);
        return $this->db->update('m_sales_salesman', $data);
    }

    public function delete($data)
    {
        $this->db->where('siteid', $data['siteid']);
        $this->db->where('salesmanid', $data['salesmanid']);
        return $this->db->delete('m_sales_salesman');
    }

    public function load($data)
    {
        $field = "a.*, b.gudang_name, case when a.tipe_sales='R' then 'Regular' when a.tipe_sales='C' then 'Canvas' end tipesales, case when a.aktif = 1 then 'Active' when a.aktif = 0 then 'Not Active' end aktifstatus ";
        $table = "m_sales_salesman a ";
        $join = " join m_gudang b on a.gudangid=b.gudangid ";
        return easy_pagging($data, $field, $table.$join);
    }

}
