<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sku_active_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('mapping_sku_active');
        if (!empty($id)) {
            $data['id'] = $id;
        }
        
        $data_product = array();
        if(isset($data['productid'])){ $data_product = $data['productid']; unset($data['productid']); }

        for($i=0;$i<count($data_product);$i++){
                    $data_array = array(
                        "idaccount" => $data['idaccount'],
                        "productid" => $data_product[$i]
                        );
                    $execreturn = $this->db->insert('mapping_sku_active', $data_array);
        }
        
        if (!$execreturn){
            return false;
        }else{
            return true;
        }

        //return $this->db->insert('mapping_sku_active', $data);
    }

    public function update($data)
    {

        $data_product = array();
        if(isset($data['productid'])){ $data_product = $data['productid']; unset($data['productid']); }

        $this->db->where('idaccount', $data['idaccount']);
        $this->db->delete('mapping_sku_active');

        for($i=0;$i<count($data_product);$i++){
                    $data_array = array(
                        "idaccount" => $data['idaccount'],
                        "productid" => $data_product[$i]
                        );
                    $execreturn = $this->db->insert('mapping_sku_active', $data_array);
        }

        if (!$execreturn){
            return false;
        }else{
            return true;
        }

    }

    public function delete($data)
    {
        $this->db->where('idaccount', $data['idaccount']);
        return $this->db->delete('mapping_sku_active');
    }

    public function load($data)
    {
        $field = "a.*";
        $table = " (select a.*, b.nama_class account, count(1) count_product, GROUP_CONCAT(a.productid) group_productid 
                        from mapping_sku_active a left join m_customer_class b on a.idaccount=b.classid 
                    group by a.idaccount, b.nama_class ) a"
                    ;
        return easy_pagging($data, $field, $table);
    }

}
