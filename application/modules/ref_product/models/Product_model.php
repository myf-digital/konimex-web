<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model
{

    public function create($data)
    {
        $id = IDGenerator::getInstance()->nextID('m_product');
        if (!empty($id)) {
            $data['productid'] = $id;
        }

        $this->db->select("*");
		$this->db->from("ref_brand");
        $this->db->where("brandid", $data['brandid']);
        $vdata = $this->db->get()->row();

        $data['nama_brand'] = $vdata->brand;
        return $this->db->insert("m_product", $data);
    }

    public function update($data)
    {
        $this->db->select("*");
		$this->db->from("ref_brand");
        $this->db->where("brandid", $data['brandid']);
        $vdata = $this->db->get()->row();
        $data['nama_brand'] = $vdata->brand;
        $this->db->where('productid', $data['productid']);
        return $this->db->update('m_product', $data);
    }

    public function delete($data)
    {
        $this->db->where('productid', $data['productid']);
        return $this->db->delete('m_product');
    }

    public function load($data)
    {
        $field = "a.* ";
        $table = " (select a.productid,
                           a.barcode,
                           a.nama_invoice,
                           a.group_product,
                           a.category_product,
                           a.brandid,
                           a.h_grosir,
                           a.h_ritel,
                           a.status,
                           case when a.status='A' then 'ACTIVE' else 'DISCONTINUE' end status_desc,
                           b.brand nama_brand                           
                    from m_product a left join ref_brand b on a.brandid=b.brandid) a ";
        return easy_pagging($data, $field, $table);
    }

    public function list()
    {
        $sql = "select
                a.productid,
                a.barcode,
                a.nama_invoice,
                a.group_product,
                a.category_product,
                a.brandid,
                a.h_grosir,
                a.h_ritel,
                a.status,
                case when a.status='A' then 'ACTIVE' else 'DISCONTINUE' end status_desc,
                b.brand nama_brand                           
        from m_product a left join ref_brand b on a.brandid=b.brandid
        order by a.productid";
        return $this->db->query($sql)->result_array();
    }
}
