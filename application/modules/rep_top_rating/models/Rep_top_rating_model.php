<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_top_rating_model extends CI_Model
{
    function load($data)
    {

        $where = $data['gff'] != 'null' ? ' where a.salesmanid="'.$data['gff'].'" ' : '';
        $and = $where != '' ? 'and' : 'where';

        $search = $data['search'] != '' ? ' '.$and.' b.nama_customer like "%'.$data['search'].'%" or a.customerid like "%'.$data['search'].'%" or b.kode_outlet like "%'.$data['search'].'%" ' : '';

        $sql = 'select a.customerid, a.outletid, a.salesmanid, ROUND(AVG(a.rating_star),2) AS rating_star, b.nama_customer, b.kode_outlet, c.nama_class, d.nama_regional as regional, e.nama_area as area, f.nama_area as city FROM rating_review a LEFT JOIN m_customer b ON a.customerid = b.customerid LEFT JOIN m_customer_class c ON b.classid = c.classid LEFT JOIN m_area_regional d ON b.regionalid = d.regionalid LEFT JOIN m_area_areasite e ON b.areaid = e.areaid LEFT JOIN m_area_subarea f ON b.subareaid = f.subareaid '.$where.$search.' GROUP BY b.nama_customer ORDER BY rating_star DESC LIMIT '.$data['top'];

        $query = $this->db->query($sql);

        return $query->result_array();
    }

    function load_detail($data)
    {

    	if ($data['filter'] == 'all') {
    		$sql = 'select periode, username, rating_star, review, transaction_id from rating_review where customerid = "'.$data['id'].'" order by periode desc limit '.$data['offset'].','.$data['limit'];
    	} else {
    		$sql = 'select periode, username, rating_star, review, transaction_id from rating_review where customerid = "'.$data['id'].'" having round(rating_star) = "'.$data['filter'].'" order by periode desc limit '.$data['offset'].','.$data['limit'];
    	}

        $query = $this->db->query($sql);

        return $query->result_array();
    }

    function load_count($data)
    {
        $sql = 'select a.customerid from rating_review a left join m_customer b on a.customerid = b.customerid where periode between "'.$data['start'].'" and "'.$data['end'].'" group by b.nama_customer order by rating_star desc';

        $query = $this->db->query($sql);

        return $query->num_rows();
    }

    function load_detail_count($data)
    {

        if ($data['filter'] == 'all') {
            $sql = 'select periode, username, rating_star, review, transaction_id from rating_review where customerid = "'.$data['id'].'" ';
        } else {
            $sql = 'select periode, username, rating_star, review, transaction_id from rating_review where customerid = "'.$data['id'].'" having round(rating_star) = "'.$data['filter'].'" ';
        }

        $query = $this->db->query($sql);

        return $query->num_rows();
    }

    function load_detail_image($id)
    {

        //$sql = 'select concat("'.URL_IMAGE_REVIEW.'", image) as image from m_customer_image where transaction_id = "'.$id.'"';
        $sql = 'select concat("'.URL_IMAGE.'", REPLACE(image, "./uploads//", "")) as image from m_customer_image where transaction_id = "'.$id.'"';

        $query = $this->db->query($sql);

        return $query->result_array();
    }

    function load_outlet_image($customerid)
    {
        $sql = 'select concat("'.URL_IMAGE.'", image) as image from m_customer_image where image_type = "IMG_OUTLET" and customerid ="'.$customerid.'" ';

        $query = $this->db->query($sql);

        return $query->row();
    }
}