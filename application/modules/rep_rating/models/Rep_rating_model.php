<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_rating_model extends CI_Model
{
    function load($data)
    {
        $sql = 'select a.customerid, a.outletid, a.salesmanid, round(avg(a.rating_star),2) as rating_star, b.nama_customer, c.nama_class 
				from rating_review a left join m_customer b on a.customerid = b.customerid left join m_customer_class c on b.classid = c.classid 
				where a.periode between "'.$data['start'].'" and "'.$data['end'].'" group by b.nama_customer order by rating_star desc limit '.$data['offset'].','.$data['limit'];

        $query = $this->db->query($sql);

        return $query->result_array();
    }

    function load_detail($data)
    {

    	if ($data['filter'] == 'all') {
    		$sql = 'select periode, username, rating_star, pc_review, stock_review, promo_review, posm_review, others_review, review, transaction_id 
					from rating_review where customerid = "'.$data['id'].'" and periode between "'.$data['start'].'" and "'.$data['end'].'" 
					order by periode desc limit '.$data['offset'].','.$data['limit'];
    	} else {
    		$sql = 'select periode, username, rating_star, pc_review, stock_review, promo_review, posm_review, others_review, review, transaction_id from rating_review 
					where customerid = "'.$data['id'].'" and periode between "'.$data['start'].'" and "'.$data['end'].'" having round(rating_star) = "'.$data['filter'].'" 
					order by periode desc limit '.$data['offset'].','.$data['limit'];
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
            $sql = 'select periode, username, rating_star, review, transaction_id from rating_review where customerid = "'.$data['id'].'" and periode between "'.$data['start'].'" and "'.$data['end'].'" ';
        } else {
            $sql = 'select periode, username, rating_star, review, transaction_id from rating_review where customerid = "'.$data['id'].'" and periode between "'.$data['start'].'" and "'.$data['end'].'" having round(rating_star) = "'.$data['filter'].'" ';
        }

        $query = $this->db->query($sql);

        return $query->num_rows();
    }

    function load_detail_image($id)
    {
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