<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Promo_active_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('mapping_promo_active');
        if (!empty($id)) {
            $data['idpromo'] = $id;
        }
        $data["created_by"] = $data["usersession"];
        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $data["created_date"] = $datetime->datetime;
        unset($data["usersession"]);

        $arrsku = array();
        if(isset($data['productid'])){ $arrsku= $data['productid']; unset($data['productid']); }
        $data['productid'] = implode(",",$arrsku);

        $execquery=$this->db->insert('mapping_promo_active', $data);
        $data['idpromo']=$this->db->insert_id();

        for($i=0;$i<count($arrsku);$i++){
            $data_array = array(
                "idpromo" => $data['idpromo'],
                "productid" => $arrsku[$i]
                );
            $this->db->insert('mapping_promo_product', $data_array);
        }

        return $execquery;
    }

    public function update($data)
    {
        $data["modified_by"] = $data["usersession"];
        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $data["modified_date"] = $datetime->datetime;
        unset($data["usersession"]);

        $this->db->where('idpromo', $data['idpromo']);
        $this->db->delete('mapping_promo_product');

        $arrsku = array();
        if(isset($data['productid'])){ $arrsku= $data['productid']; unset($data['productid']); }
        $data['productid'] = implode(",",$arrsku);
        for($i=0;$i<count($arrsku);$i++){
            $data_array = array(
                "idpromo" => $data['idpromo'],
                "productid" => $arrsku[$i]
                );
            $this->db->insert('mapping_promo_product', $data_array);
        }
        $this->db->where('idpromo', $data['idpromo']);
        return $this->db->update('mapping_promo_active', $data);
    }

    public function delete($data)
    {
        $this->db->where('idpromo', $data['idpromo']);
        $this->db->delete('mapping_promo_product');
        
        $this->db->where('idpromo', $data['idpromo']);
        return $this->db->delete('mapping_promo_active');
    }

    public function load($data)
    {
        $field = " a.* ";
        $table = " ( select a.*, b.nama_class account from mapping_promo_active a left join m_customer_class b on a.classid=b.classid where promo <> 'Promo GSK' ) as a";
        return easy_pagging($data, $field, $table);
    }

}
