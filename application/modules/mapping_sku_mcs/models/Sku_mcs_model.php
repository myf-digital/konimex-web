<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sku_mcs_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('mapping_sku_mcs');
        if (!empty($id)) {
            $data['id'] = $id;
        }
		$datetime=date("Y-m-d H:i:s");

        $data_product = array();
        if(isset($data['productid'])){ $data_product = $data['productid']; unset($data['productid']); }

        for($i=0;$i<count($data_product);$i++){
                    $data_array = array(
                        "typeid" => $data['typeid'],
                        "productid" => $data_product[$i],
						"created_by" => $data["usersession"],
						"created_date" => $datetime
                        );
                    $execreturn = $this->db->insert('mapping_sku_active_data_mcs', $data_array);
        }
        
        if (!$execreturn){
            return false;
        }else{
            return true;
        }
    }

    public function update($data)
    {

        $data_product = array();
        if(isset($data['productid'])){ $data_product = $data['productid']; unset($data['productid']); }
		$datetime=date("Y-m-d H:i:s");

        $this->db->where('typeid', $data['typeid']);
        $this->db->delete('mapping_sku_active_data_mcs');

        $execreturn = false;
        
        for($i=0;$i<count($data_product);$i++){
                    $data_array = array(
                        "typeid" => $data['typeid'],
                        "productid" => $data_product[$i],
						"modified_by" => $data["usersession"],
						"modified_date" => $datetime
                        );
                    $execreturn = $this->db->insert('mapping_sku_active_data_mcs', $data_array);
        }

        if (!$execreturn){
            return false;
        }else{
            return true;
        }

    }

    public function delete($data)
    {
        $this->db->where('typeid', $data['typeid']);
        return $this->db->delete('mapping_sku_active_data_mcs');
    }

    public function load($data)
    {
        $field = "a.*";
        $table = " (select a.*, b.nama_type, count(1) count_product, GROUP_CONCAT(a.productid) group_productid 
                        from mapping_sku_active_data_mcs a left join m_customer_type b on a.typeid=b.typeid 
                    group by a.typeid ) a"
                    ;
        return easy_pagging($data, $field, $table);
    }

}
