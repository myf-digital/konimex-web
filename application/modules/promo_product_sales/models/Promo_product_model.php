<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Promo_product_model extends CI_Model
{

    public function create($data)
    {
        /*$id = IDGenerator::getInstance()->nextID('promo_product_sales');
        if (!empty($id)) {
            $data['idpromo'] = $id;
        }*/
        $data["created_by"] = $data["usersession"];
        $sqldate = "select sysdate() datetime;";
        $datetime = $this->db->query($sqldate)->row();
        $data["created_date"] = $datetime->datetime;
        unset($data["usersession"]);

        $arrsku = array();
        if(isset($data['productid'])){ $arrsku= $data['productid']; unset($data['productid']); }
        $data['productid'] = implode(",",$arrsku);

        $execquery=$this->db->insert('t_sales_promo', $data);
        $data['idpromo']=$this->db->insert_id();

        for($i=0;$i<count($arrsku);$i++){
            $data_array = array(
                "idpromo" => $data['idpromo'],
                "productid" => $arrsku[$i],
                "product_name"=> $product_name
                );
            $this->db->insert('t_sales_promo_product', $data_array);
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

        $this->db->where('idpromo', $data['id']);
        $this->db->delete('t_sales_promo_product');

        $arrsku = array();
        if(isset($data['productid'])){ $arrsku= $data['productid']; unset($data['productid']); }
        $data['productid'] = implode(",",$arrsku);
        for($i=0;$i<count($arrsku);$i++){
            $data_array = array(
                "idpromo" => $data['id'],
                "productid" => $arrsku[$i],
                "product_name"=> $product_name
                );
            $this->db->insert('t_sales_promo_product', $data_array);
        }
        $this->db->where('id', $data['id']);
        return $this->db->update('t_sales_promo', $data);
    }

    public function delete($data)
    {
        $this->db->where('idpromo', $data['id']);
        $this->db->delete('t_sales_promo_product');
        
        $this->db->where('id', $data['id']);
        return $this->db->delete('t_sales_promo');
    }

    public function load($data)
    {
        $field = " a.* ";
        $table = " (select a.* from t_sales_promo a) as a";
        return easy_pagging($data, $field, $table);
    }

}
