<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_class_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_customer_class');
        if (!empty($id)) {
            $data['classid'] = $id;
        }
		$dataref['idaccount'] = $data['classid'];
		$dataref['account'] = $data['nama_class'];
        $this->db->insert('ref_account_outlet', $dataref);
        return $this->db->insert('m_customer_class', $data);
    }

    public function update($data)
    {
		$dataref['idaccount'] = $data['classid'];
		$dataref['account'] = $data['nama_class'];
		
        $this->db->where('idaccount', $dataref['idaccount']);
        $this->db->update('ref_account_outlet', $dataref);

        $this->db->where('classid', $data['classid']);
        return $this->db->update('m_customer_class', $data);
    }

    public function delete($data)
    {
        $this->db->where('idaccount', $data['classid']);
        $this->db->delete('ref_account_outlet');

        $this->db->where('classid', $data['classid']);
        return $this->db->delete('m_customer_class');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = 'm_customer_class a';
        return easy_pagging($data, $field, $table);
    }

    public function loadClass($data = [])
    {
        $this->db->from("m_customer_class a");
        if (!empty($data['typeid'])) {
            $this->db->where('a.typeid', $data['typeid']);
        }
        return $this->db->get()->result();
    }

    public function load_subchannel($data)
    {
        $field = " a.* ";
        $table = " ( select typeid, nama_type from m_customer_type order by nama_type asc
                    ) as a";
        return easy_pagging($data, $field, $table);
    }

}
